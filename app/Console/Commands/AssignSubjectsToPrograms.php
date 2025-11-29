<?php

namespace App\Console\Commands;

use App\Models\StudyProgram;
use App\Models\Subject;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AssignSubjectsToPrograms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finki:assign-subjects-to-programs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign subjects from majors.json to their corresponding study programs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to assign subjects to study programs...');

        // Read the majors.json file
        $jsonPath = storage_path('finki_subjects/majors.json');
        if (!file_exists($jsonPath)) {
            $this->error("File not found: {$jsonPath}");
            return 1;
        }

        $majorsData = json_decode(file_get_contents($jsonPath), true);
        if (!$majorsData) {
            $this->error("Failed to decode JSON");
            return 1;
        }

        $assigned = 0;
        $skipped = 0;
        $notFound = 0;

        // Process each major
        foreach ($majorsData as $majorData) {
            $majorName = $majorData['major'];

            // Find matching study program
            $program = StudyProgram::where('name_mk', $majorName)
                ->orWhere('name_en', $majorName)
                ->first();

            if (!$program) {
                $this->line(" Skipping: '$majorName' - not found in database");
                $skipped++;
                continue;
            }

            $this->line("\n Processing: {$program->name_mk}");

            // Get all subjects for this major from the curriculum
            $subjectNames = [];
            foreach ($majorData['curriculum'] as $semester) {
                if (isset($semester['subjects']) && is_array($semester['subjects'])) {
                    foreach ($semester['subjects'] as $subject) {
                        if (isset($subject['subject'])) {
                            $subjectNames[] = [
                                'name' => $subject['subject'],
                                'mandatory' => $subject['mandatory'] ?? true,
                            ];
                        }
                    }
                }
            }

            // Attach subjects to program
            $attachCount = 0;
            $notFoundCount = 0;

            foreach ($subjectNames as $subjectData) {
                // Try to find subject by name (both Macedonian and English)
                $subject = Subject::where('name_mk', $subjectData['name'])
                    ->orWhere('name', $subjectData['name'])
                    ->first();

                if ($subject) {
                    // Check if already attached
                    $existing = DB::table('study_program_subject')
                        ->where('study_program_id', $program->id)
                        ->where('subject_id', $subject->id)
                        ->first();

                    if (!$existing) {
                        $type = $subjectData['mandatory'] ? 'mandatory' : 'elective';
                        $program->subjects()->attach($subject->id, ['type' => $type]);
                        $attachCount++;
                    }
                } else {
                    $notFoundCount++;
                }
            }

            $this->line("  Assigned {$attachCount} subjects" . ($notFoundCount > 0 ? " ({$notFoundCount} not found)" : ""));
            $assigned += $attachCount;
            $notFound += $notFoundCount;
        }

        $this->info("\n Assignment complete!");
        $this->info("Total subjects assigned: {$assigned}");
        $this->info("Total subjects not found: {$notFound}");
        $this->info("Total majors skipped: {$skipped}");

        return 0;
    }
}
