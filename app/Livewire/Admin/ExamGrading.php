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
        $resultId = request()->query('result_id');
        if ($resultId) {
            $this->selectStudentResult((int) $resultId);
        }
    }

    public function selectStudentResult(int $resultId): void
    {
        $this->selectedResultId = $resultId;
        $this->essayScores = [];

        $result = Result::find($resultId);
        if (!$result) return;

        $answers = UserAnswer::where('exam_id', $this->examId)
            ->where('user_id', $result->user_id)
            ->get();

        foreach ($answers as $ans) {
            $this->essayScores[$ans->question_id] = $ans->score_given ?? 0;
        }
    }

    public function saveGrading()
    {
        if (!$this->selectedResultId) return;

        $result = Result::findOrFail($this->selectedResultId);
        $questions = Question::with('options')->where('exam_id', $this->examId)->get();
        $totalExamMaxWeight = $questions->sum('weight');

        $totalPgScore = 0;
        $totalEssayScore = 0;
        $totalCorrectPg = 0;

        foreach ($questions as $q) {
            $ans = UserAnswer::firstOrNew([
                'exam_id'     => $this->examId,
                'user_id'     => $result->user_id,
                'question_id' => $q->id,
            ]);

            if ($q->type === 'multiple_choice') {
                if ($ans->selectedOption && $ans->selectedOption->is_correct) {
                    $ans->score_given = $q->weight;
                    $totalPgScore += $q->weight;
                    $totalCorrectPg++;
                } else {
                    $ans->score_given = 0;
                }
                if ($ans->exists) {
                    $ans->save();
                }
            } else {
                // Essay
                $given = floatval($this->essayScores[$q->id] ?? 0);
                if ($given > $q->weight) {
                    $given = $q->weight;
                }
                $ans->score_given = $given;
                $ans->save();
                $totalEssayScore += $given;
            }
        }

        $finalScore = ($totalExamMaxWeight > 0) ? (($totalPgScore + $totalEssayScore) / $totalExamMaxWeight) * 100 : 0;
        $pgMaxWeight = $questions->where('type', 'multiple_choice')->sum('weight');
        $essayMaxWeight = $questions->where('type', 'essay')->sum('weight');
        $pgScorePct = ($pgMaxWeight > 0) ? ($totalPgScore / $pgMaxWeight) * 100 : 0;
        $essayScorePct = ($essayMaxWeight > 0) ? ($totalEssayScore / $essayMaxWeight) * 100 : 0;

        $result->update([
            'score'             => round($finalScore, 2),
            'total_correct_pg'  => $totalCorrectPg,
            'total_pg_score'    => round($pgScorePct, 2),
            'total_essay_score' => round($essayScorePct, 2),
            'is_graded'         => true,
        ]);

        session()->flash('message', "Nilai UTS untuk mahasiswa {$result->user->name} berhasil disimpan & dipublis!");
    }

    public function resetStudentExam(int $resultId): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || (!$user->isAdmin() && !$user->isDosen())) {
            session()->flash('error', 'Anda tidak memiliki akses untuk mereset ujian.');
            return;
        }

        $result = Result::with('user')->findOrFail($resultId);

        // Hapus semua jawaban mahasiswa
        UserAnswer::where('user_id', $result->user_id)
            ->where('exam_id', $result->exam_id)
            ->delete();

        // Reset result: hapus submitted_at, reset pelanggaran, reset skor, urutan soal
        $result->update([
            'submitted_at'     => null,
            'started_at'       => null,
            'violations_count' => 0,
            'score'            => 0,
            'total_correct_pg' => 0,
            'total_pg_score'   => 0,
            'total_essay_score'=> 0,
            'is_graded'        => false,
            'question_order'   => null,
        ]);

        if ($this->selectedResultId === $resultId) {
            $this->selectStudentResult($resultId);
        }

        session()->flash('message', "Status ujian {$result->user->name} berhasil dipulihkan & direset! Jawaban dihapus, batas waktu kembali utuh, dan pelanggaran dikosongkan.");
    }

    public function render()
    {
        $exam = Exam::with(['course', 'questions.options'])->findOrFail($this->examId);
        $results = Result::with('user')
            ->where('exam_id', $this->examId)
            ->latest()
            ->get();

        // Auto select first result if none selected and results exist
        if (!$this->selectedResultId && $results->isNotEmpty()) {
            $this->selectStudentResult($results->first()->id);
        }

        $selectedResult = null;
        $studentAnswers = collect();
        $studentCourseHistory = collect();

        if ($this->selectedResultId) {
            $selectedResult = Result::with('user')->find($this->selectedResultId);
            if ($selectedResult) {
                $studentAnswers = UserAnswer::with(['question.options', 'selectedOption'])
                    ->where('exam_id', $this->examId)
                    ->where('user_id', $selectedResult->user_id)
                    ->get()
                    ->keyBy('question_id');

                // Riwayat pengerjaan mahasiswa untuk mata kuliah ini
                $studentCourseHistory = Result::with('exam')
                    ->whereHas('exam', fn($q) => $q->where('course_id', $exam->course_id))
                    ->where('user_id', $selectedResult->user_id)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }

        return view('livewire.admin.exam-grading', [
            'exam'                 => $exam,
            'results'              => $results,
            'selectedResult'       => $selectedResult,
            'selectedResultId'     => $this->selectedResultId,
            'studentAnswers'       => $studentAnswers,
            'studentCourseHistory' => $studentCourseHistory,
        ]);
    }
}
