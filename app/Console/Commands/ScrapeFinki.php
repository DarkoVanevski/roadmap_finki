<?php

namespace App\Console\Commands;

use App\Models\Subject;
use App\Models\StudyProgram;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Client;

class ScrapeFinki extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:scrape-finki';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape FINKI study programs and subjects from the official website';

    private $client;
    private $baseUrl = 'https://www.finki.ukim.mk';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->client = new Client(['verify' => false]);

        try {
            $this->info('Starting FINKI scraper...');

            // Scrape study programs list
            $programs = $this->scrapeStudyPrograms();

            $this->info("Found " . count($programs) . " study programs");

            foreach ($programs as $program) {
                $this->info("Processing: {$program['name_mk']}");
                $this->scrapeSubjectsForProgram($program);
            }

            $this->info('Scraping completed successfully!');
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    /**
     * Scrape study programs from the main page
     */
    private function scrapeStudyPrograms()
    {
        $url = $this->baseUrl . '/mk/dodiplomski-studii';

        try {
            $response = $this->client->get($url);
            $html = $response->getBody()->getContents();

            // Use regex to extract program links instead of DOM parsing
            $programs = [];
            $seen = [];

            // First, find all program links with their complete content
            preg_match_all('/<a\s+href="\/program\/([A-Z0-9_]+)\/(?:mk|en)"[^>]*>[\s\S]{0,500}<\/a>/', $html, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $code = $match[1];
                $fullContent = $match[0];

                // Only process Macedonian versions to avoid duplicates
                if (stripos($fullContent, '/mk') === false) {
                    continue;
                }

                // Extract program name from the span
                if (preg_match('/<span>([^<]+)<\/span>/', $fullContent, $nameMatch)) {
                    $name = trim($nameMatch[1]);
                } else {
                    continue;
                }

                // Extract duration from the span within parentheses
                // Pattern: (<span>NUMBER</span> ANYTHING) - matches both години and years
                if (preg_match('/\(<span>(\d+)<\/span>[^)]*\)/', $fullContent, $durationMatch)) {
                    $duration = (int)$durationMatch[1];
                } else {
                    continue;
                }

                if ($code && $name && $duration > 0) {
                    // Create unique identifier combining code and duration
                    $uniqueKey = $code . '-' . $duration . 'Y';

                    // Only add if this combination hasn't been seen before
                    if (!isset($seen[$uniqueKey])) {
                        $programs[] = [
                            'code' => $code,
                            'name_mk' => $name,
                            'url' => '/program/' . $code . '/mk',
                            'duration_years' => $duration,
                        ];
                        $seen[$uniqueKey] = true;
                    }
                }
            }

            return $programs;

        } catch (\Exception $e) {
            $this->error("Failed to scrape programs: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Scrape subjects for a specific program
     */
    private function scrapeSubjectsForProgram($program)
    {
        $url = $this->baseUrl . $program['url'];

        try {
            $this->info("  Fetching subjects for {$program['duration_years']}-year program...");
            $response = $this->client->get($url);
            $html = $response->getBody()->getContents();
            $crawler = new Crawler($html);

            $semesterCounter = 0;

            // Find all tables with class "table-striped"
            $tables = $crawler->filter('table.table-striped');

            if ($tables->count() === 0) {
                $this->warn("No subject tables found");
                return;
            }

            // First, ensure the study program exists in the database
            $studyProgram = $this->createOrUpdateStudyProgram($program);

            // Process each table
            $tables->each(function (Crawler $table) use (&$semesterCounter, $program, $studyProgram) {
                $semesterCounter++;

                // Determine year and semester type
                $year = ceil($semesterCounter / 2);
                $semesterType = $semesterCounter % 2 == 1 ? 'winter' : 'summer';

                // Extract subjects from table rows
                $this->scrapeSubjectsFromTable($table, $year, $semesterType, $program, $studyProgram);
            });

            $this->info("Processed {$semesterCounter} semesters");

        } catch (\Exception $e) {
            $this->warn("Error: " . $e->getMessage());
        }
    }

    /**
     * Create or update study program in database
     */
    private function createOrUpdateStudyProgram($program)
    {
        // Create a unique code for this program variant based on duration
        $programCode = $program['code'] . '-' . $program['duration_years'] . 'Y';

        // Try to find existing study program
        $studyProgram = StudyProgram::where('code', $programCode)->first();

        if (!$studyProgram) {
            // Determine cycle based on duration
            $cycle = 'first'; // Default to first cycle
            if ($program['duration_years'] == 2) {
                $cycle = 'first'; // Professional 2-year
            }

            try {
                $studyProgram = StudyProgram::create([
                    'code' => $programCode,
                    'name_mk' => $program['name_mk'],
                    'name_en' => $program['name_mk'], // Will be overwritten if we fetch English name
                    'duration_years' => $program['duration_years'],
                    'cycle' => $cycle,
                ]);
                $this->line("Created study program: {$programCode}");
            } catch (\Exception $e) {
                $this->warn("Failed to create study program {$programCode}: " . $e->getMessage());
                return null;
            }
        }

        return $studyProgram;
    }

    /**
     * Extract subjects from a semester table
     */
    private function scrapeSubjectsFromTable($table, $year, $semesterType, $program, $studyProgram = null)
    {
        $currentType = 'mandatory';
        $subjectCount = 0;

        // Get all rows - try tbody first, then fall back to direct tr
        $rows = $table->filter('tbody tr');
        if ($rows->count() === 0) {
            $rows = $table->filter('tr');
        }

        $rows->each(function (Crawler $row) use (&$currentType, &$subjectCount, $year, $semesterType, $program, $studyProgram) {
            try {
                $cells = $row->filter('td');
                $rowText = trim($row->text());

                // Skip empty rows
                if (empty($rowText)) {
                    return;
                }

                // Check if this is a type header row (contains h4 or colspan indicating a section)
                $h4 = $row->filter('h4');
                if ($h4->count() > 0) {
                    $headerText = $h4->text();
                    if (stripos($headerText, 'избран') !== false || stripos($headerText, 'elective') !== false) {
                        $currentType = 'elective';
                    } else {
                        $currentType = 'mandatory';
                    }
                    return; // Skip the header row itself
                }

                // Check if row has th (header row)
                $th = $row->filter('th');
                if ($th->count() > 0) {
                    return; // Skip header rows
                }

                // Check if row has colspan (also a header)
                if ($cells->count() > 0 && $cells->eq(0)->attr('colspan')) {
                    return;
                }

                // Process normal data rows
                if ($cells->count() >= 2) {
                    $codeCell = trim($cells->eq(0)->text());
                    $nameCell = trim($cells->eq(1)->text());

                    // Validate code format (should be like F23L1W004)
                    if (preg_match('/^[A-Z]\d{2}[A-Z]\d[A-Z]\d{3}$/', $codeCell) && !empty($nameCell)) {
                        $this->createOrUpdateSubject($codeCell, $nameCell, $year, $semesterType, $currentType, $program, $studyProgram);
                        $subjectCount++;
                    }
                }
            } catch (\Exception $e) {
                // Skip problematic rows
            }
        });

        if ($subjectCount > 0) {
            $this->line("    Found {$subjectCount} subjects");
        }
    }

    /**
     * Create or update subject in database
     */
    private function createOrUpdateSubject($code, $name, $year, $semesterType, $subjectType, $program, $studyProgram = null)
    {
        // Check if subject already exists
        $subject = Subject::where('code', $code)->first();

        if ($subject) {
            // If study program is provided, attach subject to it if not already attached
            if ($studyProgram && !$subject->studyPrograms()->where('study_program_id', $studyProgram->id)->exists()) {
                $subject->studyPrograms()->attach($studyProgram->id);
            }
            return;
        }

        try {
            $subject = Subject::create([
                'code' => $code,
                'name' => $name,
                'name_mk' => $name,
                'year' => $year,
                'semester_type' => $semesterType,
                'subject_type' => $subjectType,
                'credits' => 6, // Default credits
                'description' => null,
                'description_mk' => null,
            ]);

            // Attach to study program if provided
            if ($studyProgram) {
                $subject->studyPrograms()->attach($studyProgram->id);
            }

            $this->line("Created: {$code} - {$name} ({$subjectType})");

        } catch (\Exception $e) {
            $this->warn("Failed to create subject {$code}: " . $e->getMessage());
        }
    }

    /**
     * Fetch additional subject details from its dedicated page
     */
    private function enrichSubjectData($subject)
    {
        try {
            $url = $this->baseUrl . '/subject/' . $subject->code;
            $response = $this->client->get($url);
            $crawler = new Crawler($response->getBody()->getContents());

            // Try to extract ECTS
            $ectsText = $crawler->filter('*')->each(function (Crawler $node) {
                $text = $node->text();
                if (stripos($text, 'ects') !== false || stripos($text, 'кредити') !== false) {
                    return $text;
                }
            });

            if (!empty($ectsText)) {
                preg_match('/(\d+)\s*(ects|кредити)/i', implode(' ', $ectsText), $matches);
                if (isset($matches[1])) {
                    $subject->credits = (int)$matches[1];
                    $subject->save();
                }
            }

        } catch (\Exception $e) {
            // do fuckall
        }
    }
}
