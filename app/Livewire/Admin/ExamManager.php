<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Exam;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ExamManager extends Component
{
    public ?int $course_id = null;
    public string $title = '';
    public string $description = '';
    public int $duration_minutes = 90;
    public string $token = '';
    public string $start_time = '';
    public string $end_time = '';
    public bool $randomize_questions = false;
    
    public ?int $selectedExamId = null;
    public bool $isEditing = false;
    public string $filterCourseId = 'all';

    public function mount(): void
    {
        $this->generateToken();
        $this->start_time = now()->format('Y-m-d\TH:i');
        $this->duration_minutes = 90;
        $this->updateEndTime();

        $firstCourse = Course::first();
        if ($firstCourse) {
            $this->course_id = $firstCourse->id;
        }
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['duration_minutes', 'start_time'])) {
            $this->updateEndTime();
        }
    }

    public function updateEndTime(): void
    {
        if (!empty($this->start_time) && is_numeric($this->duration_minutes)) {
            try {
                $start = Carbon::parse($this->start_time);
                $this->end_time = $start->copy()->addMinutes((int)$this->duration_minutes)->format('Y-m-d\TH:i');
            } catch (\Throwable $e) {
                // Ignore parse errors
            }
        }
    }

    public function generateToken(): void
    {
        $this->token = strtoupper(Str::random(6));
    }

    public function resetFields(): void
    {
        $this->title = '';
        $this->description = '';
        $this->duration_minutes = 90;
        $this->generateToken();
        $this->start_time = now()->format('Y-m-d\TH:i');
        $this->updateEndTime();
        $this->randomize_questions = false;
        $this->selectedExamId = null;
        $this->isEditing = false;

        $firstCourse = Course::first();
        if ($firstCourse) {
            $this->course_id = $firstCourse->id;
        }
    }

    public function saveExam(): void
    {
        $this->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'duration_minutes' => 'required|numeric|min:15|max:300',
            'token' => 'required|string|max:10',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $course = Course::findOrFail($this->course_id);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user && $user->role === 'dosen') {
            if ($course->lecturer_id !== $user->id) {
                abort(403, 'Akses Ditolak: Anda hanya dapat mengelola sesi UTS untuk mata kuliah yang Anda ampu.');
            }
        }

        Exam::updateOrCreate(
            ['id' => $this->selectedExamId],
            [
                'course_id' => $this->course_id,
                'lecturer_id' => $course->lecturer_id ?? Auth::id(),
                'title' => $this->title,
                'description' => $this->description,
                'duration_minutes' => $this->duration_minutes,
                'token' => strtoupper($this->token),
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'randomize_questions' => $this->randomize_questions,
            ]
        );

        session()->flash('message', $this->isEditing ? 'Sesi UTS berhasil diperbarui!' : 'Sesi UTS berhasil dibuat!');
        $this->resetFields();
    }

    public function editExam(int $id): void
    {
        $exam = Exam::findOrFail($id);
        $this->selectedExamId = $exam->id;
        $this->course_id = $exam->course_id;
        $this->title = $exam->title;
        $this->description = $exam->description;
        $this->duration_minutes = $exam->duration_minutes;
        $this->token = $exam->token;
        $this->start_time = $exam->start_time ? $exam->start_time->format('Y-m-d\TH:i') : '';
        $this->end_time = $exam->end_time ? $exam->end_time->format('Y-m-d\TH:i') : '';
        $this->randomize_questions = $exam->randomize_questions;
        $this->isEditing = true;
    }

    public function deleteExam(int $id): void
    {
        Exam::destroy($id);
        session()->flash('message', 'Sesi UTS berhasil dihapus!');
    }

    public function render()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $examsQuery = Exam::with(['course', 'lecturer'])
            ->withCount(['questions', 'results']);
        $coursesQuery = Course::query();

        if ($user && $user->role === 'dosen') {
            // Dosen HANYA melihat mata kuliah dan sesi ujian yang diampunya
            $coursesQuery->where('lecturer_id', $user->id);
            $examsQuery->where(function($q) use ($user) {
                $q->where('lecturer_id', $user->id)
                  ->orWhereHas('course', fn($cq) => $cq->where('lecturer_id', $user->id));
            });
        } elseif ($user && $user->prodi) {
            $coursesQuery->where('prodi', $user->prodi);
            $examsQuery->whereHas('course', fn($q) => $q->where('prodi', $user->prodi));
        }

        if ($this->filterCourseId !== 'all') {
            $examsQuery->where('course_id', $this->filterCourseId);
        }

        $exams = $examsQuery->latest()->get();
        $courses = $coursesQuery->get();

        return view('livewire.admin.exam-manager', [
            'exams' => $exams,
            'courses' => $courses,
        ]);
    }
}
