<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

#[Layout('layouts.app')]
class UserMonitor extends Component
{
    public string $search = '';
    public string $roleFilter = 'all';
    public string $selectedProdi = 'all';
    public string $selectedSemester = 'all';

    public ?int $selectedUserId = null;
    public string $name = '';
    public string $email = '';
    public string $role = 'mahasiswa';
    public ?string $nim = '';
    public ?string $nip = '';
    public ?string $prodi = 'Komunikasi dan Penyiaran Islam';
    public ?int $semester = 1;
    public ?string $kelas = 'KPI-1A';
    public string $password = '';
    public bool $isEditing = false;

    public function mount(): void
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if ($currentUser && $currentUser->prodi) {
            $this->prodi = $currentUser->prodi;
            if ($currentUser->role !== 'superadmin') {
                $this->selectedProdi = $currentUser->prodi;
            }
        }
    }

    public function resetFields(): void
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        $this->selectedUserId = null;
        $this->name = '';
        $this->email = '';
        $this->role = 'mahasiswa';
        $this->nim = '';
        $this->nip = '';
        $this->prodi = ($currentUser && $currentUser->prodi) ? $currentUser->prodi : 'Komunikasi dan Penyiaran Islam';
        $this->semester = 1;
        $this->kelas = 'KPI-1A';
        $this->password = '';
        $this->isEditing = false;
    }

    public function editUser(int $id): void
    {
        $user = User::findOrFail($id);
        $this->selectedUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->nim = $user->nim;
        $this->nip = $user->nip;
        $this->prodi = $user->prodi;
        $this->semester = $user->semester;
        $this->kelas = $user->kelas;
        $this->isEditing = true;
    }

    public function saveUser(): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->selectedUserId,
            'role' => 'required|in:superadmin,admin,dosen,mahasiswa',
        ];

        if ($this->password) {
            $rules['password'] = 'min:6';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'is_admin' => in_array($this->role, ['superadmin', 'admin']),
            'nim' => $this->role === 'mahasiswa' ? $this->nim : null,
            'nip' => in_array($this->role, ['dosen', 'admin', 'superadmin']) ? $this->nip : null,
            'prodi' => $this->prodi,
            'semester' => $this->role === 'mahasiswa' ? $this->semester : null,
            'kelas' => $this->role === 'mahasiswa' ? $this->kelas : null,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        User::updateOrCreate(['id' => $this->selectedUserId], $data);

        session()->flash('message', $this->isEditing ? 'Data pengguna berhasil diperbarui!' : 'Pengguna baru berhasil ditambahkan!');
        $this->resetFields();
    }

    public function deleteUser(int $id): void
    {
        if ($id == Auth::id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
            return;
        }

        User::destroy($id);
        session()->flash('message', 'Pengguna berhasil dihapus!');
    }

    public function render()
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        $query = User::with(['results.exam.course']);

        // Scope check: Admin/Dosen can ONLY see users from their own Prodi
        if ($currentUser->role !== 'superadmin' && $currentUser->prodi) {
            $query->where('prodi', $currentUser->prodi);
        } else {
            // Superadmin can filter by any Prodi or see all
            if ($this->selectedProdi !== 'all') {
                $query->where('prodi', $this->selectedProdi);
            }
        }

        // Semester Filter (for both Superadmin and Admin)
        if ($this->selectedSemester !== 'all') {
            $query->where('semester', (int) $this->selectedSemester);
        }

        // Role Filter
        if ($this->roleFilter !== 'all') {
            $query->where('role', $this->roleFilter);
        }

        // Search Filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('nim', 'like', '%' . $this->search . '%')
                  ->orWhere('nip', 'like', '%' . $this->search . '%')
                  ->orWhere('kelas', 'like', '%' . $this->search . '%');
            });
        }

        $users = $query->latest()->get();

        return view('livewire.admin.user-monitor', [
            'users' => $users,
            'currentUser' => $currentUser,
        ]);
    }
}
