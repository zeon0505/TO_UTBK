<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

#[Layout('layouts.app')]
class ProfileSettings extends Component
{
    public ?string $name = '';
    public ?string $email = '';
    public ?string $nim = '';
    public ?string $nip = '';
    public ?string $prodi = '';
    public ?int $semester = null;
    public ?string $kelas = '';
    public ?string $password = '';
    public ?string $password_confirmation = '';

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->nim = $user->nim;
        $this->nip = $user->nip;
        $this->prodi = $user->prodi;
        $this->semester = $user->semester;
        $this->kelas = $user->kelas;
    }

    public function updateProfile()
    {
        $user = User::find(Auth::id());

        $rules = [
            'name' => 'required|string|max:255',
            'prodi' => 'nullable|string',
        ];

        if ($user->role === 'mahasiswa') {
            $rules['nim'] = 'nullable|string|unique:users,nim,' . $user->id;
            $rules['semester'] = 'nullable|numeric|min:1|max:14';
            $rules['kelas'] = 'nullable|string';
        } else {
            $rules['nip'] = 'nullable|string|unique:users,nip,' . $user->id;
        }

        $this->validate($rules);

        $user->update([
            'name' => $this->name,
            'nim' => $user->role === 'mahasiswa' ? $this->nim : null,
            'nip' => $user->role === 'dosen' ? $this->nip : null,
            'prodi' => $this->prodi,
            'semester' => $user->role === 'mahasiswa' ? $this->semester : null,
            'kelas' => $user->role === 'mahasiswa' ? $this->kelas : null,
        ]);

        session()->flash('message', 'Profil berhasil diperbarui.');
    }

    public function updatePassword()
    {
        $this->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::find(Auth::id());
        $user->update([
            'password' => Hash::make($this->password),
        ]);

        $this->password = '';
        $this->password_confirmation = '';
        session()->flash('password_message', 'Kata sandi berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.profile-settings');
    }
}
