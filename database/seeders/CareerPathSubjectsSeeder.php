<?php

namespace Database\Seeders;

use App\Models\CareerPath;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class CareerPathSubjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Map career paths to subject codes
        $careerPathMappings = [
            'Full Stack Web Development' => [
                'F23L2S001',   // Web Programming
                'F23L2W002',   // Object-Oriented Programming
                'F23L2W001',   // Algorithms and Data Structures
                'F23L3W004',   // Databases
                'F23L3W024',   // Web Programming (Advanced)
                'F23L3W003',   // Software Engineering
                'F18L3W079',   // Web Based Systems
                'F23L2S110',   // Internet technologies
            ],
            'Data Science & AI' => [
                'F23L2W001',   // Algorithms and Data Structures
                'F23L3W004',   // Databases
                'F23L4W002',   // Machine Learning
                'F18L3W008',   // Introduction to Data Science
                'F23L3S002',   // Artificial Intelligence
                'F18L1S023',   // Business Statistics
                'F18L3W081',   // Visualization
                'F18L3S150',   // Data Mining
            ],
            'DevOps & Cloud Computing' => [
                'F23L3W001',   // Operating Systems
                'F23L3W002',   // Computer Networks
                'F23L3W003',   // Software Engineering
                'F23L2W014',   // Computer Networks and Security
                'F18L3S118',   // Continuous Integration and Delivery
                'F18L3W060',   // System Administration
                'F18L3S062',   // Virtualization
            ],
            'Game Development' => [
                'F23L2W001',   // Algorithms and Data Structures
                'F23L2W002',   // Object-Oriented Programming
                'F23L3S003',   // Computer Graphics
                'F23L1W003',   // Discrete Mathematics
                'F18L3W092',   // Digital Post-production
                'F18L3S113',   // Computer Animation
                'F18L3W115',   // Computer Sound, Speech and Music
            ],
            'Cybersecurity' => [
                'F23L3W001',   // Operating Systems
                'F23L3W002',   // Computer Networks
                'F23L2W014',   // Computer Networks and Security
                'F23L3W003',   // Software Engineering
                'F18L3W043',   // Information Security
                'F18L3W065',   // Network Security
                'F18L3S122',   // Cryptography
                'F18L3S159',   // Software Defined Security
            ],
            'Database Administrator' => [
                'F23L3W004',   // Databases
                'F23L2W001',   // Algorithms and Data Structures
                'F23L3W001',   // Operating Systems
                'F18L3W074',   // Database Administration
                'F18L3S138',   // Advanced Databases
                'F18L3S141',   // Unstructured Databases
                'F18L3S157',   // Data Warehouses and OLAP
                'F18L3W075',   // Information Systems Analysis and Design
            ],
        ];

        foreach ($careerPathMappings as $careerPathName => $subjectCodes) {
            $careerPath = CareerPath::where('name', $careerPathName)->first();

            // If career path doesn't exist, create it
            if (!$careerPath) {
                $descriptions = [
                    'Full Stack Web Development' => 'Build complete web applications from frontend to backend',
                    'Data Science & AI' => 'Specialize in data analysis, machine learning, and AI',
                    'DevOps & Cloud Computing' => 'Deploy, manage, and scale applications in the cloud',
                    'Game Development' => 'Create engaging interactive games and experiences',
                    'Cybersecurity' => 'Protect systems and data from security threats',
                    'Database Administrator' => 'Manage and optimize databases for large-scale applications',
                ];

                $careerPath = CareerPath::create([
                    'name' => $careerPathName,
                    'description' => $descriptions[$careerPathName] ?? '',
                ]);
            }

            // Attach subjects to career path
            foreach ($subjectCodes as $index => $code) {
                $subject = Subject::where('code', $code)->first();

                if ($subject) {
                    // Check if not already attached
                    if (!$careerPath->subjects()->where('subject_id', $subject->id)->exists()) {
                        $careerPath->subjects()->attach($subject->id, [
                            'order' => $index + 1,
                            'is_required' => true,
                        ]);
                        $this->command->line("Attached {$code} ({$subject->name}) to {$careerPathName}");
                    }
                } else {
                    $this->command->warn("Subject not found: {$code}");
                }
            }
        }

        $this->command->info("Career path subjects have been seeded successfully!");
    }
}
