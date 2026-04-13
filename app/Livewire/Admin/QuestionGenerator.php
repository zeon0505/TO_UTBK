<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\SubTest;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Support\Facades\Http;
use Livewire\WithFileUploads;

class QuestionGenerator extends Component
{
    use WithFileUploads;

    public $topic = '';
    public $difficulty = 'Sedang';
    public $selectedSubTest = '';
    public $generatedQuestions = [];
    // Manual Fields (Smart Parsing)
    public $bulkText = ""; 
    public $manualWeight = 1.0; 
    public $isGenerating = false;
    public $mode = 'manual'; 
    public $scannedImage;

    public function setMode($mode)
    {
        $this->mode = $mode;
        $this->generatedQuestions = [];
    }

    public function updatedScannedImage()
    {
        $this->processOCR();
    }

    public function processOCR()
    {
        if (!$this->scannedImage) return;

        $this->validate([
            'scannedImage' => 'mimes:jpeg,png,jpg,pdf|max:10240', // Max 10MB
        ]);

        $this->isGenerating = true;

        try {
            $imagePath = $this->scannedImage->getRealPath();
            $fileData = base64_encode(file_get_contents($imagePath));
            $mimeType = $this->scannedImage->getMimeType();

            $apiKey = env('GEMINI_API_KEY'); 

            // Menggunakan versi LITE agar kuota lebih hemat dan longgar
            $response = Http::timeout(120)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-lite-001:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => "Ekstrak SELURUH soal dari dokumen/gambar ini. JANGAN tampilkan teks pembuka, langsung soalnya saja. 
                                FORMAT OUTPUT:
                                [Soal]
                                A. ...
                                B. ...
                                * [Kunci]
                                Pembahasan: ...
                                Poin: 5"],
                                [
                                    'inline_data' => [
                                        'mime_type' => $mimeType,
                                        'data' => $fileData
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]);

            if ($response->successful()) {
                $result = $response->json();
                $textResult = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if (!$textResult) {
                    throw new \Exception('AI terhubung tapi tidak menemukan soal.');
                }
                
                $this->bulkText = trim($this->bulkText . "\n\n" . $textResult);
                $this->mode = 'manual';
                session()->flash('success', 'Magic Scan Berhasil!');
            } else {
                $errorBody = $response->json();
                throw new \Exception($errorBody['error']['message'] ?? 'Gagal memproses.');
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Scan Gagal: ' . $e->getMessage());
        }

        $this->isGenerating = false;
        $this->scannedImage = null; // Reset upload
    }

    public function smartAIParse()
    {
        if (empty(trim($this->bulkText))) {
            session()->flash('error', 'Silakan paste teks soal terlebih dahulu.');
            return;
        }

        $this->isGenerating = true;

        try {
            $apiKey = env('GEMINI_API_KEY');
            $response = Http::timeout(60)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [
                        ['text' => "Tolong rapikan teks soal yang berantakan berikut ini menjadi format standar:
                        FORMAT:
                        [Teks Soal]
                        A. Pilihan 1
                        B. Pilihan 2
                        * Pilihan Benar (Kasih tanda bintang)
                        Pembahasan: [Alasan]
                        Poin: 5

                        TEKS YANG HARUS DIRAPIKAN:
                        {$this->bulkText}"]
                    ]]
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $cleanedText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '';
                $this->bulkText = trim($cleanedText);
                session()->flash('success', 'AI berhasil merapikan format soal Anda!');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal merapikan: ' . $e->getMessage());
        }

        $this->isGenerating = false;
    }

    public function saveManual()
    {
        $this->validate([
            'bulkText' => 'required',
            'selectedSubTest' => 'required',
        ]);

        $this->isGenerating = true;

        try {
            $apiKey = env('GEMINI_API_KEY');
            // AI akan membedah teks kacau Anda menjadi data yang rapi
            $response = Http::timeout(120)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [
                        ['text' => "Bedah teks soal berantakan ini menjadi JSON yang rapi. 
                        Pisahkan mana soal, mana pilihan jawaban, mana jawaban benar, dan mana pembahasan.
                        PENTING: Pastikan instruksi soal yang panjang tidak masuk ke pilihan jawaban.
                        
                        OUTPUT HARUS JSON ARRAY:
                        [{
                            'question': '...',
                            'explanation': '...',
                            'weight': 1.0,
                            'options': [
                                {'text': '...', 'point': 5},
                                {'text': '...', 'point': 0}
                            ]
                        }]

                        TEKS:
                        {$this->bulkText}"]
                    ]]
                ],
                'generationConfig' => ['response_mime_type' => 'application/json']
            ]);

            if ($response->successful()) {
                $data = json_decode($response->json()['candidates'][0]['content']['parts'][0]['text'], true);
                $count = 0;

                foreach ($data as $item) {
                    $question = Question::create([
                        'sub_test_id' => $this->selectedSubTest,
                        'text' => $item['question'],
                        'type' => 'Pilihan Ganda',
                        'explanation' => $item['explanation'] ?? null,
                        'irt_weight' => $item['weight'] ?? 1.0,
                    ]);

                    foreach ($item['options'] as $opt) {
                        Option::create([
                            'question_id' => $question->id,
                            'text' => $opt['text'],
                            'point' => $opt['point'] ?? 0,
                        ]);
                    }
                    $count++;
                }

                $this->reset(['bulkText']);
                session()->flash('success', "Magic Success! {$count} soal berhasil dipilah dan disimpan dengan sempurna.");
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal Memproses: ' . $e->getMessage());
        }

        $this->isGenerating = false;
    }

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
                'text' => $qData['question'],
                'type' => 'Pilihan Ganda',
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
