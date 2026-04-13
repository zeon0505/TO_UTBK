<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Exam;
use App\Models\SubTest;
use App\Models\Question;
use App\Models\Option;
use App\Models\Result;
use App\Models\UserAnswer;
use Carbon\Carbon;
use App\Services\IRTService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ExamControl extends Component
{
    public $exam;
    public $result;
    public $currentSubTestIndex = 0;
    public $currentSubTest;
    public $questions;
    public $currentQuestionIndex = 0;
    public $currentQuestion;
    public $selectedOptionId;
    public $isDoubtful = false;
    
    // Timer Variables
    public $sectionTimeLeft; // in seconds for the WHOLE SUBTEST
    public $isFinished = false;
    public $showInstructions = true;

    protected $listeners = ['timeUp' => 'moveToNextSection'];

    public function mount($examId)
    {
        $this->exam = Exam::with(['subTests.questions.options'])->findOrFail($examId);
        $this->result = Result::firstOrCreate([
            'user_id' => Auth::id(),
            'exam_id' => $this->exam->id,
            'finished_at' => null,
        ], [
            'started_at' => now(),
            'total_score' => 0,
        ]);

        $targetSubject = request()->query('subject');
        if ($targetSubject) {
            $index = $this->exam->subTests->search(fn($st) => $st->id == $targetSubject);
            if ($index !== false) {
                $this->currentSubTestIndex = $index;
            }
        }
    }

    public function startExam()
    {
        $this->showInstructions = false;
        $this->loadSubTest();
        $this->dispatch('play-sfx', type: 'start');
    }

    public function loadSubTest()
    {
        if ($this->exam->subTests->isEmpty()) {
            session()->flash('error', 'Tryout ini belum memiliki Sub-tes.');
            return $this->redirect('/dashboard', navigate: true);
        }

        $this->currentSubTest = $this->exam->subTests[$this->currentSubTestIndex];
        $this->questions = $this->currentSubTest->questions;
        
        if ($this->questions->isEmpty()) {
            session()->flash('error', 'Sub-tes ini belum memiliki soal.');
            return $this->redirect('/dashboard', navigate: true);
        }

        $this->currentQuestionIndex = 0;
        
        // --- SHUFFLING LOGIC (INTEGRITAS & KEADILAN) ---
        if (!isset($data[$sectionId]['question_order'])) {
            // Jika baru mulai, acak urutan ID soal
            $questionIds = $this->currentSubTest->questions->pluck('id')->toArray();
            shuffle($questionIds);
            
            // Acak urutan ID opsi untuk setiap soal
            $optionOrders = [];
            foreach ($this->currentSubTest->questions as $q) {
                $oIds = $q->options->pluck('id')->toArray();
                shuffle($oIds);
                $optionOrders[$q->id] = $oIds;
            }

            $data[$sectionId]['question_order'] = $questionIds;
            $data[$sectionId]['option_orders'] = $optionOrders;
            $data[$sectionId]['started_at'] = now()->toDateTimeString();
            
            $this->result->update(['section_data' => $data]);
            $this->sectionTimeLeft = $totalMinutes * 60;
        } else {
            // Gunakan sisa waktu yang ada
            $startedAt = Carbon::parse($data[$sectionId]['started_at']);
            $elapsedSeconds = now()->diffInSeconds($startedAt);
            $this->sectionTimeLeft = ($totalMinutes * 60) - $elapsedSeconds;
        }

        // Muat soal sesuai urutan yang sudah diacak (konsisten)
        $orderedIds = $data[$sectionId]['question_order'];
        $this->questions = Question::whereIn('id', $orderedIds)
            ->with(['options'])
            ->get()
            ->sortBy(function($question) use ($orderedIds) {
                return array_search($question->id, $orderedIds);
            })->values();

        // Terapkan urutan opsi yang sudah diacak
        $savedOptionOrders = $data[$sectionId]['option_orders'];
        foreach ($this->questions as $question) {
            if (isset($savedOptionOrders[$question->id])) {
                $oIds = $savedOptionOrders[$question->id];
                $question->setRelation('options', $question->options->sortBy(function($option) use ($oIds) {
                    return array_search($option->id, $oIds);
                })->values());
            }
        }

        // Jika waktu sudah habis sebelum refresh selesai
        if ($this->sectionTimeLeft <= 0) {
            return $this->moveToNextSection();
        }

        $this->loadQuestion();
        
        // Dispatch ke frontend
        $this->dispatch('start-section-timer', duration: $this->sectionTimeLeft);
    }

    public function loadQuestion()
    {
        if ($this->questions->isEmpty()) return;
        
        $this->currentQuestion = $this->questions[$this->currentQuestionIndex];
        
        $existingAnswer = UserAnswer::where('result_id', $this->result->id)
            ->where('question_id', $this->currentQuestion->id)
            ->first();

        if ($existingAnswer) {
            $this->selectedOptionId = $existingAnswer->option_id;
            $this->isDoubtful = $existingAnswer->is_doubtful ?? false;
        } else {
            $this->selectedOptionId = null;
            $this->isDoubtful = false;
        }

        // Paksa Timer per-soal (60 detik)
        $this->dispatch('question-loaded', duration: 60);
    }

    public function updatedSelectedOptionId()
    {
        $this->saveAnswer();
    }

    public function updatedIsDoubtful()
    {
        $this->saveAnswer();
    }

    public function selectOption($optionId)
    {
        $this->selectedOptionId = $optionId;
        $this->saveAnswer();
    }

    public function toggleDoubtful()
    {
        $this->isDoubtful = !$this->isDoubtful;
        $this->saveAnswer();
    }

    public function goToQuestion($index)
    {
        $this->saveAnswer();
        $this->currentQuestionIndex = $index;
        $this->loadQuestion();
    }

    public function recordViolation()
    {
        $data = $this->result->section_data ?? [];
        $sectionId = (string) $this->currentSubTest->id;
        
        $violations = $data[$sectionId]['violations'] ?? 0;
        $data[$sectionId]['violations'] = $violations + 1;
        
        $this->result->update(['section_data' => $data]);
        
        // Cek Batas Pelanggaran (Maksimal 2 kali)
        $totalViolations = 0;
        foreach ($data as $sec) {
            $totalViolations += ($sec['violations'] ?? 0);
        }

        if ($totalViolations >= 2) {
            session()->flash('error', 'UJIAN DIHENTIKAN OTOMATIS: Terdeteksi pelanggaran berulang (pindah-pindah tab).');
            return $this->finishExam();
        }

        // Log secara diam-diam di sisi server
        Log::info("Violation recorded for user ".Auth::id()." on subtest ".$sectionId.". Total: ".$totalViolations);
    }

    public function nextQuestion()
    {
        $this->saveAnswer();

        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->currentQuestionIndex++;
            $this->loadQuestion();
        }
    }

    public function previousQuestion()
    {
        $this->saveAnswer();

        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
            $this->loadQuestion();
        }
    }

    public function saveAnswer()
    {
        if (!$this->currentQuestion) return;

        UserAnswer::updateOrCreate([
            'result_id' => $this->result->id,
            'question_id' => $this->currentQuestion->id,
        ], [
            'option_id' => $this->selectedOptionId,
            'is_doubtful' => $this->isDoubtful,
        ]);
    }

    public function moveToNextSection()
    {
        $this->saveAnswer();

        if ($this->currentSubTestIndex < count($this->exam->subTests) - 1) {
            $this->currentSubTestIndex++;
            $this->loadSubTest();
        } else {
            $this->finishExam();
        }
    }

    public function updateTotalScore()
    {
        $total = UserAnswer::where('result_id', $this->result->id)->sum('score_obtained');
        $this->result->update(['total_score' => $total]);
    }

    public function finishExam()
    {
        $this->saveAnswer();
        
        $this->result->update([
            'finished_at' => now(),
        ]);

        // Hitung skor IRT secara real-time
        try {
            IRTService::updateAllUserScores();
        } catch (\Exception $e) {
            Log::error("Gagal update score IRT: " . $e->getMessage());
        }

        $this->isFinished = true;
        
        session()->flash('success', 'Ujian telah berhasil dikumpulkan!');
    }

    public function render()
    {
        return view('livewire.exam-control')->layout('layouts.app');
    }
}
