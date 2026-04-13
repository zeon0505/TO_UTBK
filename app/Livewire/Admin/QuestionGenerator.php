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

            // Kembali ke 1.5-flash karena kuotanya lebih besar untuk akun Gratis
            $response = Http::timeout(120)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
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

    public function saveManual()
    {
        $this->validate([
            'bulkText' => 'required',
            'selectedSubTest' => 'required',
            'manualWeight' => 'required|numeric|min:0',
        ]);

        // 1. Pisahkan teks menjadi blok-blok soal berdasarkan baris kosong ganda
        $blocks = explode("\n\n", str_replace("\r", "", $this->bulkText));
        $count = 0;

        foreach ($blocks as $block) {
            $lines = explode("\n", trim($block));
            $lines = array_values(array_filter(array_map('trim', $lines)));

            if (count($lines) < 2) continue;

            $questionText = $lines[0];
            $explanation = null;
            $customWeight = $this->manualWeight; // Default dari input field
            $optionsData = [];

            // Proses baris-baris setelah pertanyaan
            for ($i = 1; $i < count($lines); $i++) {
                $line = $lines[$i];

                if (str_starts_with(strtolower($line), 'pembahasan:')) {
                    $explanation = trim(substr($line, 11));
                } elseif (str_starts_with(strtolower($line), 'poin:')) {
                    $customWeight = (float) trim(substr($line, 5));
                } else {
                    // Dianggap sebagai pilihan jawaban
                    $optionsData[] = [
                        'text' => rtrim($line, '*'),
                        'is_correct' => str_ends_with($line, '*'),
                    ];
                }
            }
            
            $question = Question::create([
                'sub_test_id' => $this->selectedSubTest,
                'question_text' => $questionText,
                'explanation' => $explanation,
                'irt_weight' => $customWeight,
            ]);

            foreach ($optionsData as $opt) {
                // Bersihkan awalan A. B. C.
                $cleanText = preg_replace('/^[A-E][.\s)]+/', '', $opt['text']);
                
                // Deteksi poin kustom dalam format {angka}
                $finalPoint = 0;
                if (preg_match('/\{(\d+)\}/', $cleanText, $matches)) {
                    $finalPoint = (int) $matches[1];
                    $cleanText = trim(str_replace($matches[0], '', $cleanText));
                } elseif ($opt['is_correct']) {
                    // Jika ditandai bintang (*) tapi tidak ada {x}, pakai manualWeight sebagai poin standar
                    $finalPoint = (int) $this->manualWeight;
                }

                if (!empty($cleanText)) {
                    Option::create([
                        'question_id' => $question->id,
                        'text' => trim($cleanText), // Sesuai kolom database: text
                        'point' => $finalPoint,     // Sesuai kolom database: point
                    ]);
                }
            }
            $count++;
        }

        $this->reset(['bulkText']);
        session()->flash('success', "Berhasil menyimpan {$count} soal sekaligus!");
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
