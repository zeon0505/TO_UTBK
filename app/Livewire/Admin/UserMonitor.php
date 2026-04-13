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
        $user = User::find($id);
        $this->editingUserId = $id;
        $this->editingName = $user->name;
        $this->editingEmail = $user->email;
        $this->editingSchool = $user->school;
    }

    public function updateUser()
    {
        $this->validate([
            'editingName' => 'required|min:3',
            'editingEmail' => 'required|email|unique:users,email,' . $this->editingUserId,
        ]);

        $user = User::find($this->editingUserId);
        $user->update([
            'name' => $this->editingName,
            'email' => $this->editingEmail,
            'school' => $this->editingSchool,
        ]);

        $this->editingUserId = null;
        session()->flash('success', 'Data user berhasil diperbarui.');
    }

    public function cancelEdit()
    {
        $this->editingUserId = null;
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
