<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.auth')]
class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'mahasiswa';
    public ?string $nim = null;
    public ?string $nip = null;
    public string $prodi = 'Teknik Informatika';
    public int $semester = 5;
    public string $kelas = 'TI-5A';

    public function register()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:mahasiswa,dosen',
            'prodi' => 'required|string',
        ];

        if ($this->role === 'mahasiswa') {
            $rules['nim'] = 'required|string|unique:users,nim';
            $rules['semester'] = 'required|numeric|min:1|max:14';
            $rules['kelas'] = 'required|string';
        } else {
            $rules['nip'] = 'required|string|unique:users,nip';
        }

        $this->validate($rules);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'is_admin' => false,
            'nim' => $this->role === 'mahasiswa' ? $this->nim : null,
            'nip' => $this->role === 'dosen' ? $this->nip : null,
            'prodi' => $this->prodi,
            'semester' => $this->role === 'mahasiswa' ? $this->semester : null,
            'kelas' => $this->role === 'mahasiswa' ? $this->kelas : null,
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
