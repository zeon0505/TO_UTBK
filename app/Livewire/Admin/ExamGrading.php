<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Exam;
use App\Models\Result;
use App\Models\UserAnswer;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class ExamGrading extends Component
{
    public int $examId;
    public ?int $selectedResultId = null;
    public array $essayScores = []; // [answer_id => score]

    public function mount(int $examId): void
    {
        $this->examId = $examId;
    }

    public function selectStudentResult(int $resultId): void
    {
        $this->selectedResultId = $resultId;
        $this->essayScores = [];

        $answers = UserAnswer::where('exam_id', $this->examId)
            ->where('user_id', Result::find($resultId)->user_id ?? 0)
            ->get();

        foreach ($answers as $ans) {
            $this->essayScores[$ans->id] = $ans->score_given ?? 0;
        }
    }

    public function saveGrading()
    {
        if (!$this->selectedResultId) return;

        $result = Result::findOrFail($this->selectedResultId);
        $answers = UserAnswer::with('question')
            ->where('exam_id', $this->examId)
            ->where('user_id', $result->user_id)
            ->get();

        $totalPgScore = 0;
        $totalEssayScore = 0;
        $totalExamMaxWeight = Question::where('exam_id', $this->examId)->sum('weight');

        foreach ($answers as $ans) {
            if ($ans->question->type === 'multiple_choice') {
                if ($ans->selectedOption && $ans->selectedOption->is_correct) {
                    $ans->score_given = $ans->question->weight;
                    $totalPgScore += $ans->question->weight;
                } else {
                    $ans->score_given = 0;
                }
                $ans->save();
            } else {
                // Essay
                $given = floatval($this->essayScores[$ans->id] ?? 0);
                // Cap given score at question weight
                if ($given > $ans->question->weight) {
                    $given = $ans->question->weight;
                }
                $ans->score_given = $given;
                $ans->save();
                $totalEssayScore += $given;
            }
        }

        // Calculate total scaled score out of 100
        $rawTotal = $totalPgScore + $totalEssayScore;
        $finalScore = ($totalExamMaxWeight > 0) ? ($rawTotal / $totalExamMaxWeight) * 100 : 0;

        // Calculate individual PG and Essay scores as percentage of total exam max weight
        $pgMaxWeight = Question::where('exam_id', $this->examId)->where('type', 'multiple_choice')->sum('weight');
        $essayMaxWeight = Question::where('exam_id', $this->examId)->where('type', 'essay')->sum('weight');
        $pgScorePct = ($pgMaxWeight > 0) ? ($totalPgScore / $pgMaxWeight) * 100 : 0;
        $essayScorePct = ($essayMaxWeight > 0) ? ($totalEssayScore / $essayMaxWeight) * 100 : 0;

        $result->update([
            'score' => round($finalScore, 2),
            'total_correct_pg' => UserAnswer::where('exam_id', $this->examId)->where('user_id', $result->user_id)->whereHas('selectedOption', fn($q) => $q->where('is_correct', true))->count(),
            'total_pg_score' => round($pgScorePct, 2),
            'total_essay_score' => round($essayScorePct, 2),
            'is_graded' => true,
        ]);

        session()->flash('message', "Nilai UTS untuk mahasiswa {$result->user->name} berhasil disimpan & dipublis!");
    }

    public function resetStudentExam(int $resultId): void
    {
        /** @var \App\Models\User $admin */
        $admin = Auth::user();
        if (!$admin || !$admin->isAdmin()) return;

        $result = Result::with('user')->findOrFail($resultId);

        // Hapus semua jawaban mahasiswa
        UserAnswer::where('user_id', $result->user_id)
            ->where('exam_id', $result->exam_id)
            ->delete();

        // Reset result: hapus submitted_at, reset pelanggaran, reset skor
        $result->update([
            'submitted_at'    => null,
            'violations_count' => 0,
            'score'           => 0,
            'total_correct_pg' => 0,
            'total_essay_score' => 0,
            'is_graded'       => false,
            'started_at'      => null,
        ]);

        if ($this->selectedResultId === $resultId) {
            $this->selectedResultId = null;
            $this->essayScores = [];
        }

        session()->flash('message', "Status ujian {$result->user->name} berhasil dipulihkan. Jawaban direset & pelanggaran dihapus.");
    }

    public function render()
    {
        $exam = Exam::with(['course', 'questions'])->findOrFail($this->examId);
        $results = Result::with('user')
            ->where('exam_id', $this->examId)
            ->latest()
            ->get();

        $selectedResult = null;
        $studentAnswers = [];
        if ($this->selectedResultId) {
            $selectedResult = Result::with('user')->find($this->selectedResultId);
            if ($selectedResult) {
                $studentAnswers = UserAnswer::with(['question.options', 'selectedOption'])
                    ->where('exam_id', $this->examId)
                    ->where('user_id', $selectedResult->user_id)
                    ->get();
            }
        }

        return view('livewire.admin.exam-grading', [
            'exam' => $exam,
            'results' => $results,
            'selectedResult' => $selectedResult,
            'studentAnswers' => $studentAnswers,
        ]);
    }
}
