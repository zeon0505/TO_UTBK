<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exam;
use App\Models\SubTest;
use App\Models\Question;
use App\Models\Option;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create a Full Simulation Container
        $fullExam = Exam::create([
            'title' => 'SIMULASI AKBAR UTBK-SNBT 2025',
            'category' => 'TPS',
            'sub_category' => 'FULL',
            'duration' => 195, // Total UTBK duration
            'is_active' => true,
        ]);

        $subjects = [
            ['title' => 'Penalaran Umum', 'code' => 'PU', 'duration' => 30],
            ['title' => 'Pengetahuan & Pemahaman Umum', 'code' => 'PPU', 'duration' => 15],
            ['title' => 'Pemahaman Bacaan & Menulis', 'code' => 'PBM', 'duration' => 25],
            ['title' => 'Pengetahuan Kuantitatif', 'code' => 'PK', 'duration' => 20],
            ['title' => 'Literasi Bahasa Indonesia', 'code' => 'LBIN', 'duration' => 45],
            ['title' => 'Literasi Bahasa Inggris', 'code' => 'LBING', 'duration' => 30],
            ['title' => 'Penalaran Matematika', 'code' => 'PM', 'duration' => 30],
        ];

        foreach ($subjects as $index => $subject) {
            // Create Sub-Test (Bab)
            $subTest = SubTest::create([
                'exam_id' => $fullExam->id,
                'title' => $subject['title'],
                'duration' => $subject['duration'],
                'sort_order' => $index + 1,
            ]);

            // Create 50 questions for this subject
            for ($i = 1; $i <= 50; $i++) {
                $question = Question::create([
                    'sub_test_id' => $subTest->id,
                    'text' => "Soal {$subject['title']} #$i: Bagaimanakah hasil analisis dari premis yang diberikan jika dikaitkan dengan konteks keilmuan terbaru?",
                    'type' => $subject['code'],
                    'timer_per_question' => 90, // Average time per question
                ]);

                // Create options
                $correctIndex = rand(0, 3);
                $labels = ['A', 'B', 'C', 'D'];
                
                foreach ($labels as $idx => $label) {
                    Option::create([
                        'question_id' => $question->id,
                        'text' => "Pilihan Jawaban $label untuk soal nomor $i",
                        'point' => ($idx === $correctIndex) ? 5 : rand(0, 1), // Dynamic scoring
                    ]);
                }
            }
        }
    }
}
