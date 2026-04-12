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
            session()->flash('error', 'Tryout ini belum memiliki Sub-tes (bab).');
            return $this->redirect('/dashboard', navigate: true);
        }

        $this->currentSubTest = $this->exam->subTests[$this->currentSubTestIndex];
        $this->questions = $this->currentSubTest->questions;
        
        if ($this->questions->isEmpty()) {
            session()->flash('error', 'Tryout ini belum memiliki soal. Silakan hubungi admin.');
            return $this->redirect('/dashboard', navigate: true);
        }

        $this->currentQuestionIndex = 0;
        
        $this->loadQuestion();
    }

    public function loadQuestion()
    {
        if ($this->questions->isEmpty()) return;
        
        $this->currentQuestion = $this->questions[$this->currentQuestionIndex];
        $this->selectedOptionId = null;
        $this->isDoubtful = false;

        $existingAnswer = UserAnswer::where('result_id', $this->result->id)
            ->where('question_id', $this->currentQuestion->id)
            ->first();

        if ($existingAnswer) {
            $this->selectedOptionId = $existingAnswer->option_id;
            $this->isDoubtful = $existingAnswer->is_doubtful;
        }

        $this->dispatch('question-loaded', duration: $this->currentQuestion->timer_per_question ?? 60);
    }

    public function goToQuestion($index, $remainingTime = 0)
    {
        $this->saveAnswer($remainingTime);
        $this->currentQuestionIndex = $index;
        $this->loadQuestion();
    }

    public function toggleDoubtful($remainingTime = 0)
    {
        $this->isDoubtful = !$this->isDoubtful;
        $this->saveAnswer($remainingTime);
    }

    public function nextQuestion($remainingTime = 0)
    {
        $this->saveAnswer($remainingTime);

        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->currentQuestionIndex++;
            $this->loadQuestion();
        } else {
            $this->moveToNextSection();
        }
    }

    public function previousQuestion($remainingTime = 0)
    {
        $this->saveAnswer($remainingTime);

        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
            $this->loadQuestion();
        }
    }

    public function saveAnswer($remainingTime = 0)
    {
        if (!$this->currentQuestion) return;

        $point = 0;
        if ($this->selectedOptionId) {
            $option = Option::find($this->selectedOptionId);
            if ($option) {
                $basePoint = ($option->point > 0) ? $option->point : 2;
                
                // Dynamic Timer Bonus: Answering faster gives more points
                // Max bonus 20% if answered instantly
                $totalTime = $this->currentQuestion->timer_per_question ?? 60;
                $timeBonus = ($remainingTime / $totalTime) * ($basePoint * 0.2);
                $point = $basePoint + $timeBonus;
            }
        } else {
            $point = 0;
        }

        UserAnswer::updateOrCreate([
            'result_id' => $this->result->id,
            'question_id' => $this->currentQuestion->id,
        ], [
            'option_id' => $this->selectedOptionId,
            'remaining_time' => $remainingTime,
            'score_obtained' => $point,
            'is_doubtful' => $this->isDoubtful,
        ]);

        $this->updateTotalScore();
    }

    public function moveToNextSection($remainingTime = 0)
    {
        $this->saveAnswer($remainingTime);

        if ($this->currentSubTestIndex < count($this->exam->subTests) - 1) {
            $this->currentSubTestIndex++;
            $this->loadSubTest();
        } else {
            $this->finishExam($remainingTime);
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
