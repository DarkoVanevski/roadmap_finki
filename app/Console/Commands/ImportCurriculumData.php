<?php

namespace App\Console\Commands;

use App\Models\Subject;
use App\Models\StudyProgram;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportCurriculumData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-curriculum-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import curriculum data from JSON files including prerequisites';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting curriculum data import...');

        try {
            // Import hand-fixed subjects with prerequisites
            $this->importSubjectsWithPrerequisites();

            $this->info('Curriculum data imported successfully!');
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    /**
     * Import subjects from hand_fixed_subjects.json with prerequisites
     */
    private function importSubjectsWithPrerequisites()
    {
        $jsonPath = storage_path('finki_subjects/hand_fixed_subjects.json');

        if (!file_exists($jsonPath)) {
            $this->warn("File not found: $jsonPath");
            return;
        }

        $json = json_decode(file_get_contents($jsonPath), true);

        if (!is_array($json)) {
            $this->error('Invalid JSON format');
            return;
        }

        $importedCount = 0;
        $updatedCount = 0;
        $prerequisiteCount = 0;

        foreach ($json as $item) {
            try {
                $code = $item['code'] ?? null;
                $name = $item['name'] ?? null;
                $level = $item['level'] ?? null;
                $semester = $item['semester'] ?? null;
                $prerequisites = $item['prerequisites'] ?? [];

                if (!$code || !$name || !$level) {
                    continue;
                }

                // Determine year from level
                $year = $this->mapLevelToYear($level);

                // Determine semester type
                $semesterType = $this->mapSemesterName($semester);

                // Check if subject exists
                $subject = Subject::where('code', $code)->first();

                if ($subject) {
                    // Update existing subject with more detailed info
                    $subject->update([
                        'name' => $name,
                        'name_mk' => $name,
                        'year' => $year,
                        'semester_type' => $semesterType,
                    ]);
                    $updatedCount++;
                } else {
                    // Create new subject
                    $subject = Subject::create([
                        'code' => $code,
                        'name' => $name,
                        'name_mk' => $name,
                        'year' => $year,
                        'semester_type' => $semesterType,
                        'subject_type' => 'mandatory',
                        'credits' => 6,
                        'description' => null,
                        'description_mk' => null,
                    ]);
                    $importedCount++;
                }

                // Process prerequisites
                if (!empty($prerequisites) && is_array($prerequisites)) {
                    $this->attachPrerequisites($subject, $prerequisites);
                    $prerequisiteCount++;
                }

            } catch (\Exception $e) {
                $this->warn("Skipped: " . ($item['name'] ?? 'Unknown') . " - " . $e->getMessage());
            }
        }

        $this->line("Imported: {$importedCount} new subjects");
        $this->line("Updated: {$updatedCount} subjects");
        $this->line("Processed: {$prerequisiteCount} subjects with prerequisites");
    }

    /**
     * Attach prerequisites to a subject
     */
    private function attachPrerequisites($subject, $prerequisites)
    {
        // Prerequisites are array of arrays
        // Each inner array is a group of OR conditions
        foreach ($prerequisites as $prereqGroup) {
            if (!is_array($prereqGroup)) {
                $prereqGroup = [$prereqGroup];
            }

            foreach ($prereqGroup as $prereqName) {
                // Find prerequisite subject by name (fuzzy match)
                $prereqSubject = $this->findSubjectByName($prereqName);

                if ($prereqSubject && $prereqSubject->id !== $subject->id) {
                    try {
                        // Check if not already attached before attaching
                        if (!$subject->prerequisites()->where('prerequisite_subject_id', $prereqSubject->id)->exists()) {
                            $subject->prerequisites()->attach($prereqSubject->id);
                        }
                    } catch (\Exception $e) {
                        // Silently skip duplicate attachments
                    }
                }
            }
        }
    }

    /**
     * Find subject by name (fuzzy match)
     */
    private function findSubjectByName($name)
    {
        // First try exact match with name or name_mk
        $subject = Subject::where('name', $name)
            ->orWhere('name_mk', $name)
            ->first();

        if ($subject) {
            return $subject;
        }

        // Try partial match (case-insensitive)
        $name_lower = strtolower($name);
        $subject = Subject::whereRaw("LOWER(name) LIKE ?", ["%$name_lower%"])
            ->orWhereRaw("LOWER(name_mk) LIKE ?", ["%$name_lower%"])
            ->first();

        return $subject;
    }

    /**
     * Map level (1,2,3,4) to year
     */
    private function mapLevelToYear($level)
    {
        $map = [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 4, // Sometimes 5 is used for 4-year programs (lol)
        ];

        return $map[$level] ?? 1;
    }

    /**
     * Map semester name to semester_type (winter/summer)
     */
    private function mapSemesterName($semester)
    {
        if (!$semester) {
            return 'winter';
        }

        $semester_lower = strtolower($semester);

        if (stripos($semester_lower, 'зимски') !== false || stripos($semester_lower, 'winter') !== false) {
            return 'winter';
        } elseif (stripos($semester_lower, 'летен') !== false || stripos($semester_lower, 'summer') !== false) {
            return 'summer';
        }

        return 'winter'; // Default
    }
}

