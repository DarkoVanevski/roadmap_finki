<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectPrerequisitesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Load the prerequisites data from the JSON file
        $jsonPath = storage_path('finki_subjects/pit_only_fixed_subjects.json');

        if (!file_exists($jsonPath)) {
            $this->command->error("File not found: {$jsonPath}");
            return;
        }

        $subjects = json_decode(file_get_contents($jsonPath), true);

        if (!is_array($subjects)) {
            $this->command->error("Invalid JSON format in subjects file");
            return;
        }

        $prerequisitesAdded = 0;

        foreach ($subjects as $subjectData) {
            // Find the subject in database by code
            $subject = Subject::where('code', $subjectData['code'])->first();

            if (!$subject) {
                continue;
            }

            // Check if there are prerequisites
            if (empty($subjectData['prerequisites'])) {
                continue;
            }

            // Prerequisites are stored as an array of arrays
            // Each inner array represents prerequisite alternatives (OR logic)
            // For now, we'll implement simple AND logic - all prerequisites must be met

            foreach ($subjectData['prerequisites'] as $prerequisiteGroup) {
                // For each prerequisite in the group
                foreach ($prerequisiteGroup as $prerequisiteName) {
                    // Find the prerequisite subject by name
                    $prerequisiteSubject = Subject::where('name', $prerequisiteName)
                        ->orWhere('name_mk', $prerequisiteName)
                        ->first();

                    if ($prerequisiteSubject) {
                        // Check if this prerequisite relation already exists
                        if (!$subject->prerequisites()->where('prerequisite_id', $prerequisiteSubject->id)->exists()) {
                            $subject->prerequisites()->attach($prerequisiteSubject->id);
                            $prerequisitesAdded++;
                            $this->command->line("Added prerequisite: {$prerequisiteSubject->code} ({$prerequisiteSubject->name}) -> {$subject->code} ({$subject->name})");
                        }
                    }
                }
            }
        }

        $this->command->info("Successfully added {$prerequisitesAdded} subject prerequisite relationships.");
    }
}
