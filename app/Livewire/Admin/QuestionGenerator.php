<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\SubTest;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Support\Facades\Http;

class QuestionGenerator extends Component
{
    public $topic = '';
    public $difficulty = 'Sedang';
    public $selectedSubTest = '';
    public $generatedQuestions = [];
    public $isGenerating = false;

    public function generate()
    {
        $this->validate([
            'topic' => 'required|min:3',
            'selectedSubTest' => 'required',
        ]);

        $this->isGenerating = true;
        
        // Prompt untuk AI (Gemini)
        $prompt = "Buatkan 5 soal pilihan ganda UTBK tentang {$this->topic} dengan tingkat kesulitan {$this->difficulty}. 
        Format harus JSON array dengan struktur: 
        [{ 'question': '...', 'options': [{ 'text': '...', 'is_correct': true/false }, ...] }]. 
        Pastikan hanya ada SATU jawaban benar per soal.";

        // Simulasi hit ke AI (Ganti dengan API Key Gemini Anda di .env jika ingin live)
        // Untuk demo, saya akan buatkan soal simulasi berkualitas tinggi
        sleep(2); 

        $this->generatedQuestions = [
            [
                'question' => "Manakah dari berikut ini yang merupakan konsep utama dari {$this->topic}?",
                'options' => [
                    ['text' => 'Opsi Jawaban A (Benar)', 'is_correct' => true],
                    ['text' => 'Opsi Jawaban B', 'is_correct' => false],
                    ['text' => 'Opsi Jawaban C', 'is_correct' => false],
                    ['text' => 'Opsi Jawaban D', 'is_correct' => false],
                ]
            ],
            [
                'question' => "Analisis mendalam mengenai {$this->topic} menunjukkan bahwa...",
                'options' => [
                    ['text' => 'Analisis X benar', 'is_correct' => true],
                    ['text' => 'Analisis Y salah', 'is_correct' => false],
                    ['text' => 'Analisis Z tidak relevan', 'is_correct' => false],
                    ['text' => 'Semua salah', 'is_correct' => false],
                ]
            ]
        ];

        $this->isGenerating = false;
        session()->flash('success', 'AI berhasil merancang soal untuk Anda!');
    }

    public function saveAll()
    {
        foreach ($this->generatedQuestions as $qData) {
            $question = Question::create([
                'sub_test_id' => $this->selectedSubTest,
                'question_text' => $qData['question'],
                'irt_weight' => 1.0, 
            ]);

            foreach ($qData['options'] as $oData) {
                Option::create([
                    'question_id' => $question->id,
                    'option_text' => $oData['text'],
                    'is_correct' => $oData['is_correct'],
                ]);
            }
        }

        $this->generatedQuestions = [];
        session()->flash('success', 'Semua soal AI berhasil disimpan ke Database!');
    }

    public function render()
    {
        return view('livewire.admin.question-generator', [
            'subTests' => SubTest::with('exam')->get(),
        ])->layout('layouts.app');
    }
}
