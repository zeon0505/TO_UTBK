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

    public function goToQuestion($index)
    {
        $this->saveAnswer();
        $this->currentQuestionIndex = $index;
        $this->loadQuestion();
    }

    public function toggleDoubtful()
    {
        $this->isDoubtful = !$this->isDoubtful;
        $this->saveAnswer();
    }

    public function nextQuestion()
    {
        $this->saveAnswer();

        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->currentQuestionIndex++;
            $this->loadQuestion();
        } else {
            $this->moveToNextSection();
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

        $point = 0;
        if ($this->selectedOptionId) {
            $option = Option::find($this->selectedOptionId);
            if ($option) {
                $point = $option->point;
            }
        }

        UserAnswer::updateOrCreate([
            'result_id' => $this->result->id,
            'question_id' => $this->currentQuestion->id,
        ], [
            'option_id' => $this->selectedOptionId,
            'remaining_time' => 0,
            'score_obtained' => $point,
            'is_doubtful' => $this->isDoubtful,
        ]);

        $this->updateTotalScore();
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
        $this->result->update(['finished_at' => now()]);
        $this->isFinished = true;
    }

    public function render()
    {
        return view('livewire.exam-control')->layout('layouts.app');
    }
}
