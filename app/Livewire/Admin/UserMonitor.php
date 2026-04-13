<?php
// b7439651-a26c-4cf4-b85f-a47e78b44bcf

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;

class UserMonitor extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $editingUserId = null;
    public $editingName = '';
    public $editingEmail = '';
    public $editingSchool = '';
    public $editingPassword = '';

    public function deleteUser($id)
    {
        if ($id === auth()->id()) {
            session()->flash('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
            return;
        }

        User::find($id)->delete();
        session()->flash('success', 'User berhasil dihapus.');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $this->editingUserId = $user->id;
        $this->editingName = $user->name;
        $this->editingEmail = $user->email;
        $this->editingSchool = $user->school;
        $this->editingPassword = ''; 
    }

    public function updateUser()
    {
        $this->validate([
            'editingName' => 'required',
            'editingEmail' => 'required|email|unique:users,email,' . $this->editingUserId,
            'editingSchool' => 'required',
        ]);

        $user = User::find($this->editingUserId);
        $user->update([
            'name' => $this->editingName,
            'email' => $this->editingEmail,
            'school' => $this->editingSchool,
        ]);

        if (!empty($this->editingPassword)) {
            $user->password = \Illuminate\Support\Facades\Hash::make($this->editingPassword);
            $user->save();
        }

        session()->flash('success', 'Data user berhasil diperbarui.');
        $this->editingUserId = null;
    }

    public function cancelEdit()
    {
        $this->editingUserId = null;
    }

    public function toggleAdmin($id)
    {
        if ($id === auth()->id()) {
            session()->flash('error', 'Anda tidak bisa mengubah status admin diri Anda sendiri.');
            return;
        }

        $user = User::find($id);
        $user->is_admin = !$user->is_admin;
        $user->save();

        $status = $user->is_admin ? 'Admin baru ditambahkan.' : 'Status Admin dicabut.';
        session()->flash('success', $status);
    }

    public function render()
    {
        $users = User::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.user-monitor', [
            'users' => $users,
            'totalUsers' => User::count(),
            'recentUsers' => User::where('created_at', '>=', now()->subDays(1))->count()
        ])->layout('layouts.app');
    }
}
