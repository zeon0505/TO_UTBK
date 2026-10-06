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

    public string $selectedCourseId = 'all';
    public string $selectedExamId = 'all';
    public string $searchMahasiswa = '';

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

        $exam = Exam::where('token', $token)->first();

        if (!$exam) {
            $this->errorMessage = 'Token UTS tidak valid atau tidak ditemukan!';
            return;
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

            $coursesList = (clone $coursesQuery)->get();
            $coursesCount = $coursesQuery->count();
            $examsCount = $examsQuery->count();
            $studentsCount = $studentsQuery->count();
            
            $pendingGradingQuery = (clone $resultsQuery)->where('is_graded', false);
            $pendingGradingCount = $pendingGradingQuery->count();

            // Filter Sesi UTS (latestExams) by selected Mata Kuliah
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

            $studentResults = $resultsQuery->latest()->get();

            return view('livewire.dashboard', [
                'coursesCount' => $coursesCount,
                'examsCount' => $examsCount,
                'studentsCount' => $studentsCount,
                'pendingGradingCount' => $pendingGradingCount,
                'latestExams' => $latestExams,
                'studentResults' => $studentResults,
                'coursesList' => $coursesList,
            ]);
        }

        // Mahasiswa View
        $coursesQuery = Course::query();
        if ($user->prodi) {
            $coursesQuery->where('prodi', $user->prodi);
        }
        $coursesList = $coursesQuery->get();

        $activeExamsQuery = Exam::with(['course', 'lecturer'])
            ->withCount('questions')
            ->whereHas('course', function($q) use ($user) {
                if ($user->prodi) {
                    $q->where('prodi', $user->prodi);
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
        ]);
    }
}
