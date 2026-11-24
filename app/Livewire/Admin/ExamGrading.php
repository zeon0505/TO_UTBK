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

    // Custom Reset Confirmation Modal State
    public bool $showResetModal = false;
    public ?int $resetTargetResultId = null;
    public ?string $resetTargetStudentName = null;
    public ?string $resetTargetStudentNim = null;
    public ?string $resetTargetStudentKelas = null;

    public function mount(int $examId): void
    {
        $this->examId = $examId;

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $exam = Exam::with('course')->findOrFail($this->examId);

        // Keamanan: Dosen pengampu HANYA boleh mengakses dan menilai mata kuliah/ujian yang diampunya
        if ($user && $user->role === 'dosen') {
            $isPengampu = ($exam->lecturer_id === $user->id) || ($exam->course && $exam->course->lecturer_id === $user->id);
            if (!$isPengampu) {
                abort(403, 'Akses Ditolak: Anda bukan dosen pengampu untuk mata kuliah/sesi ujian ini.');
            }
        }

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

    public function confirmReset(int $resultId): void
    {
        $res = Result::with('user')->find($resultId);
        if (!$res) return;

        $this->resetTargetResultId = $resultId;
        $this->resetTargetStudentName = $res->user->name ?? 'Mahasiswa';
        $this->resetTargetStudentNim = $res->user->nim ?? '-';
        $this->resetTargetStudentKelas = $res->user->kelas ?? '-';
        $this->showResetModal = true;
    }

    public function closeResetModal(): void
    {
        $this->showResetModal = false;
        $this->resetTargetResultId = null;
        $this->resetTargetStudentName = null;
        $this->resetTargetStudentNim = null;
        $this->resetTargetStudentKelas = null;
    }

    public function executeReset(): void
    {
        if (!$this->resetTargetResultId) return;

        $targetId = $this->resetTargetResultId;
        $this->closeResetModal();
        $this->resetStudentExam($targetId);
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
        $studentName = $result->user->name ?? 'Mahasiswa';

        // 1. Hapus semua jawaban mahasiswa untuk ujian ini
        UserAnswer::where('user_id', $result->user_id)
            ->where('exam_id', $result->exam_id)
            ->delete();

        // 2. Hapus record hasil ujian agar bersih total dan riwayat di akun mahasiswa hilang
        $result->delete();

        $this->selectedResultId = null;
        $this->essayScores = [];

        session()->flash('message', "Status ujian {$studentName} berhasil direset total! Riwayat dan jawaban telah dihapus sehingga mahasiswa dapat memasukkan token dan memulai ujian kembali.");
        $this->dispatch('swal:modal', [
            'type' => 'success',
            'title' => 'Ujian Berhasil Direset!',
            'text' => "Data ujian mahasiswa {$studentName} telah dibersihkan. Mahasiswa kini dapat memulai kembali ujian dari awal.",
        ]);
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
