<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileSettings extends Component
{
    public $name, $school, $email, $password, $password_confirmation;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->school = $user->school;
        $this->email = $user->email;
    }

    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'school' => 'required|string|max:255',
        ]);

        $user = User::find(Auth::id());
        $user->update([
            'name' => $this->name,
            'school' => $this->school,
        ]);

        session()->flash('message', 'Profile updated successfully.');
    }

    public function updatePassword()
    {
        $this->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::find(Auth::id());
        $user->update([
            'password' => Hash::make($this->password),
        ]);

        $this->password = '';
        $this->password_confirmation = '';
        session()->flash('password_message', 'Password updated successfully.');
    }

    public function render()
    {
        return view('livewire.profile-settings')->layout('layouts.app');
    }
}
