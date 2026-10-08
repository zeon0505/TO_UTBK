<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Exam;
use App\Models\Course;
use App\Models\Result;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public string $tokenInput = '';
    public string $errorMessage = '';

    public string $selectedSemester = 'all';
    public string $selectedCourseId = 'all';
    public string $selectedExamId = 'all';
    public string $searchMahasiswa = '';

    public function mount(): void
    {
        $semFromQuery = request()->query('semester');
        if ($semFromQuery) {
            $this->selectedSemester = (string) $semFromQuery;
        }
    }

    public function updatedSelectedSemester(): void
    {
        $this->selectedCourseId = 'all';
        $this->selectedExamId = 'all';
    }

    public function updatedSelectedCourseId(): void
    {
        $this->selectedExamId = 'all';
    }

    public function startExamWithToken()
    {
        $this->errorMessage = '';
        $token = strtoupper(trim($this->tokenInput));

        if (empty($token)) {
            $this->errorMessage = 'Silakan masukkan kode token UTS!';
            return;
        }

        $exam = Exam::with('course')->where('token', $token)->first();

        if (!$exam) {
            $this->errorMessage = 'Token UTS tidak valid atau tidak ditemukan!';
            return;
        }

        /** @var User $user */
        $user = Auth::user();
        if ($user && $user->isMahasiswa()) {
            if ($user->prodi && $exam->course && $exam->course->prodi && $user->prodi !== $exam->course->prodi) {
                $this->errorMessage = "🚫 Gagal Akses: Token UTS ini khusus untuk Program Studi {$exam->course->prodi}!";
                return;
            }
            if ($user->semester && $exam->course && $exam->course->semester && (int)$user->semester !== (int)$exam->course->semester) {
                $this->errorMessage = "🚫 Gagal Akses: Token UTS ini untuk Ujian Semester {$exam->course->semester} ({$exam->course->name})! Akun Anda berada di Semester {$user->semester}.";
                return;
            }
        }

        return redirect()->route('exam.show', ['examId' => $exam->id]);
    }

    public function render()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isDosen() || $user->isAdmin()) {
            $coursesQuery = Course::query();
            $examsQuery = Exam::query();
            $studentsQuery = User::where('role', 'mahasiswa');
            $resultsQuery = Result::with(['user', 'exam.course', 'exam.lecturer']);

            if ($user->prodi) {
                $coursesQuery->where('prodi', $user->prodi);
                $examsQuery->whereHas('course', fn($q) => $q->where('prodi', $user->prodi));
                $studentsQuery->where('prodi', $user->prodi);
                $resultsQuery->whereHas('exam.course', fn($q) => $q->where('prodi', $user->prodi));
            } elseif ($user->isDosen()) {
                $examsQuery->where('lecturer_id', $user->id);
                $resultsQuery->whereHas('exam', fn($q) => $q->where('lecturer_id', $user->id));
            }

            if ($this->selectedSemester !== 'all') {
                $sem = (int) $this->selectedSemester;
                $coursesQuery->where('semester', $sem);
                $examsQuery->whereHas('course', fn($q) => $q->where('semester', $sem));
                $studentsQuery->where('semester', $sem);
                $resultsQuery->whereHas('exam.course', fn($q) => $q->where('semester', $sem));
            }

            $coursesList = (clone $coursesQuery)->get();
            $coursesCount = $coursesQuery->count();
            $examsCount = $examsQuery->count();
            $studentsCount = $studentsQuery->count();
            
            $pendingGradingQuery = (clone $resultsQuery)->where('is_graded', false);
            $pendingGradingCount = $pendingGradingQuery->count();

            // Filter Sesi UTS (latestExams) by selected Mata Kuliah & Semester
            $latestExamsQuery = clone $examsQuery;
            if ($this->selectedCourseId !== 'all') {
                $latestExamsQuery->where('course_id', $this->selectedCourseId);
            }

            $latestExams = $latestExamsQuery->with(['course', 'lecturer'])
                ->withCount(['questions', 'results'])
                ->latest()
                ->get();

            // Filter Student Results by Mata Kuliah, Sesi UTS & Search
            if ($this->selectedCourseId !== 'all') {
                $resultsQuery->whereHas('exam', fn($q) => $q->where('course_id', $this->selectedCourseId));
            }

            if ($this->selectedExamId !== 'all') {
                $resultsQuery->where('exam_id', $this->selectedExamId);
            }

            if ($this->searchMahasiswa) {
                $resultsQuery->whereHas('user', function($q) {
                    $q->where('name', 'like', '%' . $this->searchMahasiswa . '%')
                      ->orWhere('nim', 'like', '%' . $this->searchMahasiswa . '%')
                      ->orWhere('kelas', 'like', '%' . $this->searchMahasiswa . '%');
                });
            }

            $studentResults = $resultsQuery->where('is_practice', false)->latest()->get();

            // ===== STATISTIK DASHBOARD =====
            // Distribusi nilai (A, B, C, D, E)
            $allGradedResults = Result::whereHas('exam.course', fn($q) => $user->prodi ? $q->where('prodi', $user->prodi) : $q)
                ->where('is_graded', true)
                ->where('is_practice', false)
                ->get();

            $gradeDistribution = [
                'A' => $allGradedResults->where('score', '>=', 85)->count(),
                'B' => $allGradedResults->whereBetween('score', [70, 84.99])->count(),
                'C' => $allGradedResults->whereBetween('score', [60, 69.99])->count(),
                'D' => $allGradedResults->whereBetween('score', [50, 59.99])->count(),
                'E' => $allGradedResults->where('score', '<', 50)->count(),
            ];

            // Rata-rata nilai per exam (5 terbaru)
            $avgPerExam = $latestExams->map(function($ex) {
                $avg = Result::where('exam_id', $ex->id)->where('is_practice', false)->where('is_graded', true)->avg('score');
                return ['label' => $ex->course->code ?? 'MK', 'avg' => round($avg ?? 0, 1)];
            })->take(5);

            return view('livewire.dashboard', [
                'coursesCount'       => $coursesCount,
                'examsCount'         => $examsCount,
                'studentsCount'      => $studentsCount,
                'pendingGradingCount' => $pendingGradingCount,
                'latestExams'        => $latestExams,
                'studentResults'     => $studentResults,
                'coursesList'        => $coursesList,
                'gradeDistribution'  => $gradeDistribution,
                'avgPerExam'         => $avgPerExam,
            ]);
        }

        // Mahasiswa View - Strict Prodi & Semester Isolation
        $coursesQuery = Course::query();
        if ($user->prodi) {
            $coursesQuery->where('prodi', $user->prodi);
        }

        $targetSemester = ($this->selectedSemester !== 'all') 
            ? (int) $this->selectedSemester 
            : (int) ($user->semester ?? 1);

        if ($targetSemester) {
            $coursesQuery->where('semester', $targetSemester);
        }

        $coursesList = $coursesQuery->get();

        $activeExamsQuery = Exam::with(['course', 'lecturer'])
            ->withCount('questions')
            ->whereHas('course', function($q) use ($user, $targetSemester) {
                if ($user->prodi) {
                    $q->where('prodi', $user->prodi);
                }
                if ($targetSemester) {
                    $q->where('semester', $targetSemester);
                }
            })
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now());

        if ($this->selectedCourseId !== 'all') {
            $activeExamsQuery->where('course_id', $this->selectedCourseId);
        }

        $activeExams = $activeExamsQuery->get();

        $myResultsQuery = Result::with(['exam.course', 'exam.lecturer'])
            ->where('user_id', $user->id);

        if ($this->selectedCourseId !== 'all') {
            $myResultsQuery->whereHas('exam', fn($q) => $q->where('course_id', $this->selectedCourseId));
        }

        $myResults = $myResultsQuery->latest()->get();

        return view('livewire.dashboard', [
            'activeExams' => $activeExams,
            'myResults' => $myResults,
            'coursesList' => $coursesList,
            'targetSemester' => $targetSemester,
        ]);
    }
}
