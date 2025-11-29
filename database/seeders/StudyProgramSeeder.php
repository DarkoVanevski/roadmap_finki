<?php

namespace Database\Seeders;

use App\Models\StudyProgram;
use Illuminate\Database\Seeder;

class StudyProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = [
            // 4-year programs (Прв циклус)
            [
                'code' => 'SE-4Y',
                'name_mk' => 'Софтверско инженерство и информациски системи',
                'name_en' => 'Software engineering and information systems',
                'duration_years' => 4,
                'cycle' => 'first',
            ],
            [
                'code' => 'INTERNET-4Y',
                'name_mk' => 'Интернет, мрежи и безбедност',
                'name_en' => 'Internet, networks and security',
                'duration_years' => 4,
                'cycle' => 'first',
            ],
            [
                'code' => 'AITC-4Y',
                'name_mk' => 'Примена на информациски технологии',
                'name_en' => 'Application of information technologies',
                'duration_years' => 4,
                'cycle' => 'first',
            ],
            [
                'code' => 'ITDU-4Y',
                'name_mk' => 'Информатичка едукација',
                'name_en' => 'IT education',
                'duration_years' => 4,
                'cycle' => 'first',
            ],
            [
                'code' => 'CE-4Y',
                'name_mk' => 'Компјутерско инженерство',
                'name_en' => 'Computer engineering',
                'duration_years' => 4,
                'cycle' => 'first',
            ],
            [
                'code' => 'CS-4Y',
                'name_mk' => 'Компјутерски науки',
                'name_en' => 'Computer science',
                'duration_years' => 4,
                'cycle' => 'first',
            ],

            // 3-year programs (Прв циклус - скратена верзија)
            [
                'code' => 'SE-3Y',
                'name_mk' => 'Софтверско инженерство и информациски системи',
                'name_en' => 'Software engineering and information systems',
                'duration_years' => 3,
                'cycle' => 'first',
            ],
            [
                'code' => 'INTERNET-3Y',
                'name_mk' => 'Интернет, мрежи и безбедност',
                'name_en' => 'Internet, networks and security',
                'duration_years' => 3,
                'cycle' => 'first',
            ],
            [
                'code' => 'AITC-3Y',
                'name_mk' => 'Примена на информациски технологии',
                'name_en' => 'Application of information technologies',
                'duration_years' => 3,
                'cycle' => 'first',
            ],
            [
                'code' => 'CE-3Y',
                'name_mk' => 'Компјутерско инженерство',
                'name_en' => 'Computer engineering',
                'duration_years' => 3,
                'cycle' => 'first',
            ],
            [
                'code' => 'CS-3Y',
                'name_mk' => 'Компјутерски науки',
                'name_en' => 'Computer science',
                'duration_years' => 3,
                'cycle' => 'first',
            ],

            // Professional 3-year and 2-year programs
            [
                'code' => 'PROG-3Y',
                'name_mk' => 'Стручни студии за програмирање',
                'name_en' => 'Professional studies in programming',
                'duration_years' => 3,
                'cycle' => 'first',
            ],
            [
                'code' => 'PROG-2Y',
                'name_mk' => 'Стручни студии за програмирање',
                'name_en' => 'Professional studies in programming',
                'duration_years' => 2,
                'cycle' => 'first',
            ],
        ];

        foreach ($programs as $program) {
            StudyProgram::create($program);
        }
    }
}
