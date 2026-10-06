<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CourseManager extends Component
{
    public $code = '';
    public $name = '';
    public $sks = 3;
    public $semester = 5;
    public $prodi = 'Teknik Informatika';
    public ?int $lecturer_id = null;
    public ?int $selectedCourseId = null;
    public bool $isEditing = false;

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
        if ($user && $user->isDosen()) {
            $this->lecturer_id = $user->id;
        }
        if ($user && $user->prodi) {
            $this->prodi = $user->prodi;
        }
    }

    public function resetFields(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->code = '';
        $this->name = '';
        $this->sks = 3;
        $this->semester = 5;
        $this->prodi = $user->prodi ?? 'Teknik Informatika';
        $this->lecturer_id = ($user && $user->isDosen()) ? $user->id : null;
        $this->selectedCourseId = null;
        $this->isEditing = false;
    }

    public function saveCourse(): void
    {
        $rules = [
            'code' => 'required|string|unique:courses,code,' . $this->selectedCourseId,
            'name' => 'required|string|max:255',
            'sks' => 'required|numeric|min:1|max:6',
            'semester' => 'required|numeric|min:1|max:8',
            'prodi' => 'required|string',
        ];

        $this->validate($rules);

        Course::updateOrCreate(
            ['id' => $this->selectedCourseId],
            [
                'code' => strtoupper($this->code),
                'name' => $this->name,
                'sks' => $this->sks,
                'semester' => $this->semester,
                'prodi' => $this->prodi,
                'lecturer_id' => $this->lecturer_id ?? Auth::id(),
            ]
        );

        session()->flash('message', $this->isEditing ? 'Mata Kuliah berhasil diperbarui!' : 'Mata Kuliah berhasil ditambahkan!');
        $this->resetFields();
    }

    public function editCourse(int $id): void
    {
        $course = Course::findOrFail($id);
        $this->selectedCourseId = $course->id;
        $this->code = $course->code;
        $this->name = $course->name;
        $this->sks = $course->sks;
        $this->semester = $course->semester;
        $this->prodi = $course->prodi;
        $this->lecturer_id = $course->lecturer_id;
        $this->isEditing = true;
    }

    public function deleteCourse(int $id): void
    {
        Course::destroy($id);
        session()->flash('message', 'Mata Kuliah berhasil dihapus!');
    }

    public function render()
    {
        $user = Auth::user();
        $coursesQuery = Course::with('lecturer');
        $lecturersQuery = User::whereIn('role', ['dosen', 'admin']);

        if ($user->prodi) {
            $coursesQuery->where('prodi', $user->prodi);
            $lecturersQuery->where(function($q) use ($user) {
                $q->where('prodi', $user->prodi)->orWhereNull('prodi');
            });
        }

        $courses = $coursesQuery->latest()->get();
        $lecturers = $lecturersQuery->get();

        return view('livewire.admin.course-manager', [
            'courses' => $courses,
            'lecturers' => $lecturers,
        ]);
    }
}
