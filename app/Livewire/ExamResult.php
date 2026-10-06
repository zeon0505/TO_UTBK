<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Exam;
use App\Models\Result;
use App\Models\UserAnswer;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class ExamResult extends Component
{
    public int|string|null $examId = null;
    public ?Exam $exam = null;
    public ?Result $result = null;
    public mixed $userAnswers = null;

    public function mount(int|string $examId): void
    {
        $this->examId = $examId;
        $this->exam = Exam::with(['course', 'lecturer'])->findOrFail($examId);
        
        $this->result = Result::where('exam_id', $this->examId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $this->userAnswers = UserAnswer::with(['question.options', 'selectedOption'])
            ->where('exam_id', $this->examId)
            ->where('user_id', Auth::id())
            ->get();
    }

    public function render()
    {
        return view('livewire.exam-result');
    }
}
