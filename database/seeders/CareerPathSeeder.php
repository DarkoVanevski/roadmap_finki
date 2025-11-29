<?php

namespace Database\Seeders;

use App\Models\CareerPath;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class CareerPathSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $careerPaths = [
            [
                'name' => 'Full Stack Web Development',
                'description' => 'Build complete web applications from frontend to backend',
            ],
            [
                'name' => 'Data Science & AI',
                'description' => 'Specialize in data analysis, machine learning, and AI',
            ],
            [
                'name' => 'DevOps & Cloud Computing',
                'description' => 'Deploy, manage, and scale applications in the cloud',
            ],
            [
                'name' => 'Game Development',
                'description' => 'Create engaging interactive games and experiences',
            ],
            [
                'name' => 'Cybersecurity',
                'description' => 'Protect systems and data from security threats',
            ],
        ];

        foreach ($careerPaths as $pathData) {
            CareerPath::create($pathData);
        }

        // Define which subjects are required for each career path
        $fullStackSubjects = [
            'MATH101' => 1,
            'PROG101' => 1,
            'DS101' => 2,
            'DISCRETE101' => 2,
            'ALGO201' => 3,
            'DB201' => 3,
            'WEB201' => 3,
            'OOP201' => 4,
            'OS201' => 4,
            'DB301' => 5,
            'SE301' => 5,
            'DEVOPS401' => 7,
            'DISTRIB401' => 7,
            'PROJECT401' => 8,
        ];

        $dataScienceSubjects = [
            'MATH101' => 1,
            'PROG101' => 1,
            'MATH102' => 2,
            'DS101' => 2,
            'DISCRETE101' => 2,
            'ALGO201' => 3,
            'LINALG201' => 3,
            'PROB201' => 4,
            'OOP201' => 4,
            'DB201' => 3,
            'ML401' => 7,
            'DL401' => 7,
            'NLP401' => 7,
            'PROJECT401' => 8,
        ];

        $devOpsSubjects = [
            'PROG101' => 1,
            'OS201' => 4,
            'NETWORK301' => 5,
            'SE301' => 5,
            'DB201' => 3,
            'DB301' => 5,
            'ARCHI201' => 4,
            'SECURITY301' => 6,
            'DEVOPS401' => 7,
            'DISTRIB401' => 7,
            'PROJECT401' => 8,
        ];

        $gameDevSubjects = [
            'MATH101' => 1,
            'PROG101' => 1,
            'DS101' => 2,
            'ALGO201' => 3,
            'OOP201' => 4,
            'LINALG201' => 3,
            'GRAPHICS301' => 6,
            'GAME401' => 7,
            'PHYS101' => 1,
            'PHYS102' => 2,
            'SE301' => 5,
            'PROJECT401' => 8,
        ];

        $cybersecuritySubjects = [
            'PROG101' => 1,
            'DISCRETE101' => 2,
            'MATH101' => 1,
            'ALGO201' => 3,
            'OS201' => 4,
            'NETWORK301' => 5,
            'DB201' => 3,
            'ARCHI201' => 4,
            'SECURITY301' => 6,
            'SE301' => 5,
            'DISTRIB401' => 7,
            'PROJECT401' => 8,
        ];

        // Attach subjects to career paths
        $this->attachSubjectsToCareePath('Full Stack Web Development', $fullStackSubjects);
        $this->attachSubjectsToCareePath('Data Science & AI', $dataScienceSubjects);
        $this->attachSubjectsToCareePath('DevOps & Cloud Computing', $devOpsSubjects);
        $this->attachSubjectsToCareePath('Game Development', $gameDevSubjects);
        $this->attachSubjectsToCareePath('Cybersecurity', $cybersecuritySubjects);
    }

    private function attachSubjectsToCareePath(string $careerPathName, array $subjectCodes): void
    {
        $careerPath = CareerPath::where('name', $careerPathName)->first();
        if (!$careerPath) {
            return;
        }

        foreach ($subjectCodes as $code => $order) {
            $subject = Subject::where('code', $code)->first();
            if ($subject) {
                $careerPath->subjects()->attach($subject->id, [
                    'order' => $order,
                    'is_required' => true,
                ]);
            }
        }
    }
}
