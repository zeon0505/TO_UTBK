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
    public $course_id = null;
    public $title = '';
    public $description = '';
    public $duration_minutes = 90;
    public $token = '';
    public $start_time = '';
    public $end_time = '';
    public $randomize_questions = false;
    
    public $selectedExamId = null;
    public $isEditing = false;
    public $filterCourseId = 'all';

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

    public function updated($property): void
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
        $examsQuery = Exam::with(['course', 'lecturer'])
            ->withCount(['questions', 'results']);

        if ($this->filterCourseId !== 'all') {
            $examsQuery->where('course_id', $this->filterCourseId);
        }

        $exams = $examsQuery->latest()->get();
        $courses = Course::all();

        return view('livewire.admin.exam-manager', [
            'exams' => $exams,
            'courses' => $courses,
        ]);
    }
}
