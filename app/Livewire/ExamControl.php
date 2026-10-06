<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Result;
use App\Models\UserAnswer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class ExamControl extends Component
{
    public ?Exam $exam = null;
    public ?Result $result = null;
    public mixed $questions = null;
    public int $currentQuestionIndex = 0;
    public ?Question $currentQuestion = null;
    
    // Student Answer State
    public ?int $selectedOptionId = null;
    public string $essayAnswer = '';
    public bool $isDoubtful = false;

    // Token Modal State
    public string $tokenInput = '';
    public string $tokenError = '';
    public bool $showTokenModal = false;

    // Timer & Security Alarm State
    public int $timeLeftSeconds = 0;
    public int $violationsCount = 0;
    public bool $isFinished = false;

    protected $listeners = [
        'autoSubmitExam' => 'finishExam',
        'recordSecurityViolation' => 'recordViolation'
    ];

    public function mount(int|string $examId): void
    {
        $this->exam = Exam::with(['course', 'questions.options'])->findOrFail($examId);
        
        /** @var User $user */
        $user = Auth::user();
        if ($user && $user->isMahasiswa()) {
            if ($user->prodi && $this->exam->course && $this->exam->course->prodi && $user->prodi !== $this->exam->course->prodi) {
                abort(403, "Maaf, Ujian UTS ini khusus untuk Mahasiswa Program Studi {$this->exam->course->prodi}.");
            }
            if ($user->semester && $this->exam->course && $this->exam->course->semester && (int)$user->semester !== (int)$this->exam->course->semester) {
                abort(403, "Maaf, Ujian UTS ini khusus untuk Mahasiswa Semester {$this->exam->course->semester}.");
            }
        }

        $this->result = Result::where('user_id', Auth::id())
            ->where('exam_id', $this->exam->id)
            ->first();

        if (!$this->result) {
            $this->showTokenModal = true;
        } else {
            $this->initExamSession();
        }
    }

    public function verifyToken()
    {
        $this->tokenError = '';
        if (strtoupper(trim($this->tokenInput)) !== strtoupper($this->exam->token)) {
            $this->tokenError = 'Kode Token UTS salah! Silakan tanyakan kepada dosen pengampu.';
            return;
        }

        $this->showTokenModal = false;
        
        $this->result = Result::create([
            'user_id' => Auth::id(),
            'exam_id' => $this->exam->id,
            'started_at' => now(),
            'score' => 0,
            'is_graded' => false,
            'violations_count' => 0,
        ]);

        $this->initExamSession();
    }

    public function recordViolation()
    {
        if (!$this->result || $this->result->submitted_at) return;

        $this->violationsCount++;
        $this->result->increment('violations_count');

        if ($this->violationsCount >= 3) {
            $this->finishExam();
        }
    }

    public function initExamSession()
    {
        $this->showTokenModal = false;

        if ($this->result->submitted_at) {
            $this->isFinished = true;
            return;
        }

        $this->isFinished = false;

        // If started_at is null (was reset by admin), re-start session fresh now
        if (!$this->result->started_at) {
            $this->result->update([
                'started_at' => now(),
                'violations_count' => 0
            ]);
            $this->result->refresh();
        }

        $this->violationsCount = (int)($this->result->violations_count ?? 0);

        // Calculate accurate time left
        $startedAt = Carbon::parse($this->result->started_at);
        $allowedSeconds = (int)($this->exam->duration_minutes * 60);
        $now = now();
        
        $elapsedSeconds = $startedAt->greaterThan($now) ? 0 : (int)$now->diffInSeconds($startedAt);
        $this->timeLeftSeconds = max(0, $allowedSeconds - $elapsedSeconds);

        if ($this->timeLeftSeconds <= 0) {
            $this->finishExam();
            return;
        }

        // Load Questions
        if ($this->exam->randomize_questions) {
            $this->questions = $this->exam->questions->shuffle()->values();
        } else {
            $this->questions = $this->exam->questions;
        }

        if ($this->questions->isNotEmpty()) {
            $this->loadQuestion(0);
        }
    }

    public function loadQuestion(int $index): void
    {
        if ($this->questions->isEmpty()) return;

        // Auto save previous question answer before moving
        if ($this->currentQuestion) {
            $this->saveAnswer();
        }

        $this->currentQuestionIndex = $index;
        $this->currentQuestion = $this->questions[$this->currentQuestionIndex];

        $ans = UserAnswer::where('user_id', Auth::id())
            ->where('exam_id', $this->exam->id)
            ->where('question_id', $this->currentQuestion->id)
            ->first();

        if ($ans) {
            $this->selectedOptionId = $ans->selected_option_id;
            $this->essayAnswer = $ans->essay_answer ?? '';
            $this->isDoubtful = $ans->is_doubtful ?? false;
        } else {
            $this->selectedOptionId = null;
            $this->essayAnswer = '';
            $this->isDoubtful = false;
        }
    }

    public function selectOption(int $optionId): void
    {
        $this->selectedOptionId = $optionId;
        $this->saveAnswer();
    }

    public function toggleDoubtful(): void
    {
        $this->isDoubtful = !$this->isDoubtful;
        $this->saveAnswer();
    }

    public function saveAnswer(): void
    {
        if (!$this->currentQuestion || !$this->result || $this->result->submitted_at) return;

        UserAnswer::updateOrCreate([
            'user_id' => Auth::id(),
            'exam_id' => $this->exam->id,
            'question_id' => $this->currentQuestion->id,
        ], [
            'selected_option_id' => $this->currentQuestion->type === 'multiple_choice' ? $this->selectedOptionId : null,
            'essay_answer' => $this->currentQuestion->type === 'essay' ? $this->essayAnswer : null,
            'is_doubtful' => $this->isDoubtful,
        ]);
    }

    public function goToQuestion(int $index): void
    {
        $this->loadQuestion($index);
    }

    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->loadQuestion($this->currentQuestionIndex + 1);
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->loadQuestion($this->currentQuestionIndex - 1);
        }
    }

    public function finishExam()
    {
        if ($this->currentQuestion) {
            $this->saveAnswer();
        }

        if (!$this->result) return;

        // Auto Grade PG Questions
        $answers = UserAnswer::with(['question', 'selectedOption'])
            ->where('exam_id', $this->exam->id)
            ->where('user_id', Auth::id())
            ->get();

        $totalPgScore = 0;
        $totalCorrectPg = 0;
        $hasEssay = false;

        foreach ($answers as $ans) {
            if ($ans->question->type === 'multiple_choice') {
                if ($ans->selectedOption && $ans->selectedOption->is_correct) {
                    $ans->score_given = $ans->question->weight;
                    $totalPgScore += $ans->question->weight;
                    $totalCorrectPg++;
                } else {
                    $ans->score_given = 0;
                }
                $ans->save();
            } else {
                $hasEssay = true;
            }
        }

        $totalExamMaxWeight = $this->exam->questions->sum('weight');
        $initialScore = ($totalExamMaxWeight > 0) ? ($totalPgScore / $totalExamMaxWeight) * 100 : 0;

        $this->result->update([
            'submitted_at' => now(),
            'score' => round($initialScore, 2),
            'total_correct_pg' => $totalCorrectPg,
            'is_graded' => !$hasEssay, // If no essay, automatically fully graded!
        ]);

        $this->isFinished = true;
        return redirect()->route('exam.result', ['examId' => $this->exam->id]);
    }

    public function render()
    {
        $userAnswers = [];
        if ($this->exam && Auth::check()) {
            $userAnswers = UserAnswer::where('exam_id', $this->exam->id)
                ->where('user_id', Auth::id())
                ->pluck('is_doubtful', 'question_id')
                ->toArray();
        }

        return view('livewire.exam-control', [
            'userAnswers' => $userAnswers,
        ]);
    }
}
