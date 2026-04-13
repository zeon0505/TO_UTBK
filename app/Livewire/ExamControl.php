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
        
        // Timer Sub-Test (in seconds)
        $this->sectionTimeLeft = ($this->currentSubTest->duration ?? 30) * 60;

        $this->loadQuestion();
        
        // Dispatch to frontend to start central timer
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

    public function finishExam($remainingTime = 0)
    {
        $this->saveAnswer($remainingTime);
        $this->result->update(['finished_at' => now()]);
        $this->isFinished = true;
    }

    public function render()
    {
        return view('livewire.exam-control')->layout('layouts.app');
    }
}
