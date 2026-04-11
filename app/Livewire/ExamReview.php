<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Result;
use App\Models\Exam;
use App\Models\UserAnswer;

class ExamReview extends Component
{
    public $result;
    public $exam;
    public $currentSubTestIndex = 0;
    public $questions;

    public function mount($resultId)
    {
        $this->result = Result::with(['exam.subTests.questions.options', 'userAnswers'])->findOrFail($resultId);
        $this->exam = $this->result->exam;
        $this->loadSubTest();
    }

    public function loadSubTest()
    {
        if ($this->exam->subTests->isEmpty()) {
            $this->questions = $this->exam->questions()->with('options')->get();
        } else {
            $subTest = $this->exam->subTests[$this->currentSubTestIndex];
            $this->questions = $subTest->questions()->with('options')->get();
        }
    }

    public function setSubTest($index)
    {
        $this->currentSubTestIndex = $index;
        $this->loadSubTest();
    }

    public function render()
    {
        return view('livewire.exam-review')->layout('layouts.app');
    }
}
