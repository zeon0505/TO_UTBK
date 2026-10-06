<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CourseManager extends Component
{
    public $code = '';
    public $name = '';
    public $sks = 3;
    public $semester = 5;
    public $prodi = 'Komunikasi dan Penyiaran Islam';
    public ?int $lecturer_id = null;
    public ?int $selectedCourseId = null;
    public bool $isEditing = false;

    // Quick Add Dosen Modal State
    public bool $showDosenModal = false;
    public string $newDosenName = '';
    public string $newDosenNip = '';
    public string $newDosenEmail = '';
    public string $newDosenPassword = 'password';

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
        $this->prodi = $user->prodi ?? 'Komunikasi dan Penyiaran Islam';
        $this->lecturer_id = ($user && $user->isDosen()) ? $user->id : null;
        $this->selectedCourseId = null;
        $this->isEditing = false;
    }

    public function openDosenModal(): void
    {
        $this->newDosenName = '';
        $this->newDosenNip = '';
        $this->newDosenEmail = '';
        $this->newDosenPassword = 'password';
        $this->showDosenModal = true;
    }

    public function closeDosenModal(): void
    {
        $this->showDosenModal = false;
    }

    public function saveNewDosen(): void
    {
        $this->validate([
            'newDosenName' => 'required|string|max:255',
            'newDosenEmail' => 'required|email|unique:users,email',
            'newDosenNip' => 'nullable|string',
        ], [
            'newDosenName.required' => 'Nama dosen wajib diisi!',
            'newDosenEmail.required' => 'Email dosen wajib diisi!',
            'newDosenEmail.unique' => 'Email dosen tersebut sudah terdaftar!',
        ]);

        /** @var User $currentUser */
        $currentUser = Auth::user();

        $dosen = User::create([
            'name' => trim($this->newDosenName),
            'email' => trim($this->newDosenEmail),
            'nip' => trim($this->newDosenNip) ?: null,
            'role' => 'dosen',
            'prodi' => $this->prodi ?: ($currentUser->prodi ?? 'Komunikasi dan Penyiaran Islam'),
            'password' => Hash::make($this->newDosenPassword ?: 'password'),
            'is_admin' => false,
        ]);

        $this->lecturer_id = $dosen->id;
        $this->showDosenModal = false;
        session()->flash('message', 'Dosen Pengampu baru (' . $dosen->name . ') berhasil ditambahkan!');
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
        $lecturersQuery = User::whereIn('role', ['dosen', 'admin', 'superadmin']);

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
