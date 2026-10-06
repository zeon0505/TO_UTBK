<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Support\Facades\Http;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class QuestionGenerator extends Component
{
    use WithFileUploads;

    public $selectedExamId = null;
    public string $mode = 'single'; // 'single', 'ai', 'bulk'

    // Form Manual Single Question
    public string $questionType = 'multiple_choice'; // 'multiple_choice' or 'essay'
    public string $questionText = '';
    public float $weight = 10.0;
    public string $explanation = '';
    
    // Multiple Choice Options
    public array $options = [
        ['text' => '', 'is_correct' => true],
        ['text' => '', 'is_correct' => false],
        ['text' => '', 'is_correct' => false],
        ['text' => '', 'is_correct' => false],
        ['text' => '', 'is_correct' => false],
    ];
    public int $correctOptionIndex = 0;

    // AI Generator Fields
    public string $aiTopic = '';
    public string $aiQuestionType = 'multiple_choice';
    public int $aiCount = 5;
    public bool $isGenerating = false;

    // Bulk Paste Fields
    public string $bulkText = '';

    // Edit mode
    public ?int $editingQuestionId = null;

    public function mount()
    {
        $examId = request()->query('exam_id');
        if ($examId && Exam::where('id', $examId)->exists()) {
            $this->selectedExamId = $examId;
        } else {
            $firstExam = Exam::first();
            if ($firstExam) {
                $this->selectedExamId = $firstExam->id;
            }
        }
    }

    public function setMode(string $mode)
    {
        $this->mode = $mode;
    }

    public function addOption()
    {
        if (count($this->options) < 6) {
            $this->options[] = ['text' => '', 'is_correct' => false];
        }
    }

    public function removeOption($index)
    {
        if (count($this->options) > 2) {
            unset($this->options[$index]);
            $this->options = array_values($this->options);
            if ($this->correctOptionIndex >= count($this->options)) {
                $this->correctOptionIndex = 0;
            }
        }
    }

    public function saveSingleQuestion()
    {
        $this->validate([
            'selectedExamId' => 'required|exists:exams,id',
            'questionText' => 'required|string',
            'weight' => 'required|numeric|min:0.5|max:100',
        ]);

        if ($this->questionType === 'multiple_choice') {
            $filledOptions = array_filter($this->options, fn($opt) => !empty(trim($opt['text'])));
            if (count($filledOptions) < 2) {
                session()->flash('error', 'Pilihan Ganda harus memiliki minimal 2 opsi jawaban!');
                return;
            }
        }

        $question = Question::create([
            'exam_id' => $this->selectedExamId,
            'type' => $this->questionType,
            'question_text' => $this->questionText,
            'weight' => $this->weight,
            'explanation' => $this->explanation,
        ]);

        if ($this->questionType === 'multiple_choice') {
            foreach ($this->options as $index => $opt) {
                if (empty(trim($opt['text']))) continue;

                Option::create([
                    'question_id' => $question->id,
                    'option_text' => $opt['text'],
                    'is_correct' => ($index === (int)$this->correctOptionIndex),
                ]);
            }
        }

        // Reset form
        $this->questionText = '';
        $this->explanation = '';
        $this->options = [
            ['text' => '', 'is_correct' => true],
            ['text' => '', 'is_correct' => false],
            ['text' => '', 'is_correct' => false],
            ['text' => '', 'is_correct' => false],
            ['text' => '', 'is_correct' => false],
        ];
        $this->correctOptionIndex = 0;

        session()->flash('success', 'Soal UTS berhasil ditambahkan!');
    }

    public function loadQuestionForEdit(int $id)
    {
        $q = Question::with('options')->findOrFail($id);
        $this->editingQuestionId = $id;
        $this->questionType = $q->type;
        $this->questionText = $q->question_text;
        $this->weight = $q->weight;
        $this->explanation = $q->explanation ?? '';
        $this->mode = 'single';

        if ($q->type === 'multiple_choice') {
            $this->options = $q->options->map(fn($o) => [
                'text' => $o->option_text,
                'is_correct' => (bool) $o->is_correct,
            ])->toArray();
            $this->correctOptionIndex = collect($this->options)->search(fn($o) => $o['is_correct'] === true) ?: 0;
        } else {
            $this->options = [];
        }
    }

    public function updateQuestion()
    {
        $this->validate([
            'questionText' => 'required|string',
            'weight' => 'required|numeric|min:0.5|max:100',
        ]);

        $q = Question::findOrFail($this->editingQuestionId);
        $q->update([
            'type' => $this->questionType,
            'question_text' => $this->questionText,
            'weight' => $this->weight,
            'explanation' => $this->explanation,
        ]);

        if ($this->questionType === 'multiple_choice') {
            // Delete existing options and recreate
            $q->options()->delete();
            foreach ($this->options as $index => $opt) {
                if (empty(trim($opt['text']))) continue;
                Option::create([
                    'question_id' => $q->id,
                    'option_text' => $opt['text'],
                    'is_correct' => ($index === (int)$this->correctOptionIndex),
                ]);
            }
        }

        $this->cancelEdit();
        session()->flash('success', 'Soal berhasil diperbarui!');
    }

    public function cancelEdit()
    {
        $this->editingQuestionId = null;
        $this->questionText = '';
        $this->explanation = '';
        $this->weight = 10.0;
        $this->options = [
            ['text' => '', 'is_correct' => true],
            ['text' => '', 'is_correct' => false],
            ['text' => '', 'is_correct' => false],
            ['text' => '', 'is_correct' => false],
            ['text' => '', 'is_correct' => false],
        ];
        $this->correctOptionIndex = 0;
    }

    public function generateWithAI()
    {
        $this->validate([
            'selectedExamId' => 'required|exists:exams,id',
            'aiTopic' => 'required|string|min:3',
            'aiCount' => 'required|numeric|min:1|max:10',
        ]);

        $this->isGenerating = true;

        try {
            $exam = Exam::with('course')->find($this->selectedExamId);
            $courseName = $exam->course->name ?? 'Perkuliahan';
            $apiKey = env('GEMINI_API_KEY');

            if (!$apiKey) {
                throw new \Exception('GEMINI_API_KEY belum dikonfigurasi di file .env');
            }

            $prompt = "Buatkan {$this->aiCount} soal ujian perkuliahan tingkat tinggi (UTS Kampus) untuk mata kuliah '{$courseName}' dengan topik khusus: '{$this->aiTopic}'. 
            Tipe Soal: {$this->aiQuestionType} (multiple_choice atau essay).
            
            FORMAT HARUS DALAM JSON ARRAY PERSIS SEPERTI INI:
            [
                {
                    \"type\": \"{$this->aiQuestionType}\",
                    \"question_text\": \"isi pertanyaan studi kasus/konseptual\",
                    \"weight\": 10,
                    \"explanation\": \"rubrik penilaian / pembahasan jawaban\",
                    \"options\": [
                        {\"text\": \"pilihan A\", \"is_correct\": true},
                        {\"text\": \"pilihan B\", \"is_correct\": false},
                        {\"text\": \"pilihan C\", \"is_correct\": false},
                        {\"text\": \"pilihan D\", \"is_correct\": false}
                    ]
                }
            ]
            Catatan: Untuk tipe 'essay', field 'options' boleh kosong []. Hanya ada SATU option is_correct true untuk multiple_choice.";

            $response = Http::timeout(90)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key={$apiKey}", [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => ['response_mime_type' => 'application/json']
                ]);

            if ($response->successful()) {
                $rawText = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '[]';
                $cleanJson = preg_replace('/^```json|```$/m', '', $rawText);
                $items = json_decode(trim($cleanJson), true);

                if (is_array($items)) {
                    $savedCount = 0;
                    foreach ($items as $item) {
                        if (empty($item['question_text'])) continue;

                        $q = Question::create([
                            'exam_id' => $this->selectedExamId,
                            'type' => $item['type'] ?? $this->aiQuestionType,
                            'question_text' => $item['question_text'],
                            'weight' => $item['weight'] ?? 10.0,
                            'explanation' => $item['explanation'] ?? null,
                        ]);

                        if (($item['type'] ?? $this->aiQuestionType) === 'multiple_choice' && isset($item['options']) && is_array($item['options'])) {
                            foreach ($item['options'] as $opt) {
                                Option::create([
                                    'question_id' => $q->id,
                                    'option_text' => $opt['text'] ?? 'Opsi',
                                    'is_correct' => $opt['is_correct'] ?? false,
                                ]);
                            }
                        }
                        $savedCount++;
                    }
                    session()->flash('success', "AI Gemini Berhasil! {$savedCount} soal UTS berhasil dibuat & disimpan ke database!");
                } else {
                    throw new \Exception('Format keluaran AI tidak valid JSON array.');
                }
            } else {
                $err = $response->json();
                throw new \Exception($err['error']['message'] ?? 'Gagal menghubungi Gemini AI');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal Generator AI: ' . $e->getMessage());
        }

        $this->isGenerating = false;
    }

    public function deleteQuestion($id)
    {
        Question::destroy($id);
        session()->flash('success', 'Soal berhasil dihapus!');
    }

    public function render()
    {
        $exams = Exam::with('course')->get();

        $questions = [];
        $totalWeight = 0;
        if ($this->selectedExamId) {
            $questions = Question::with('options')
                ->where('exam_id', $this->selectedExamId)
                ->latest()
                ->get();
            $totalWeight = $questions->sum('weight');
        }

        return view('livewire.admin.question-generator', [
            'exams' => $exams,
            'questions' => $questions,
            'totalWeight' => $totalWeight,
        ]);
    }
}
