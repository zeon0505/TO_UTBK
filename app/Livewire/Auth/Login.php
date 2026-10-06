<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class Login extends Component
{
    public string $loginType = 'mahasiswa'; // 'mahasiswa' or 'admin'

    // Mahasiswa fields
    public string $name = '';
    public string $nim = '';
    public string $prodi = 'Komunikasi dan Penyiaran Islam';
    public int $semester = 1;
    public string $mahasiswaPassword = '';

    // Admin fields
    public string $email = '';
    public string $password = '';

    public function setLoginType(string $type): void
    {
        $this->loginType = $type;
        session()->forget('error');
    }

    public function loginMahasiswa()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'nim' => 'required|string|max:50',
            'prodi' => 'required|string',
            'semester' => 'required|integer|min:1|max:8',
            'mahasiswaPassword' => 'required|string|min:4',
        ], [
            'name.required' => 'Nama lengkap wajib diisi!',
            'nim.required' => 'NIM mahasiswa wajib diisi!',
            'prodi.required' => 'Program studi wajib dipilih!',
            'semester.required' => 'Semester wajib dipilih!',
            'mahasiswaPassword.required' => 'Kata sandi wajib diisi!',
        ]);

        $cleanNim = trim($this->nim);
        $email = $cleanNim . '@mahasiswa.ac.id';

        // Search by NIM or constructed email
        $user = User::where('nim', $cleanNim)
            ->orWhere('email', $email)
            ->first();

        if ($user) {
            // Update student's prodi, semester, and name if needed
            $shortProdi = match($this->prodi) {
                'Komunikasi dan Penyiaran Islam' => 'KPI',
                'Hukum Tata Negara' => 'HTN',
                'Pendidikan Agama Islam' => 'PAI',
                'Ekonomi Syariah' => 'ES',
                default => 'MHS',
            };

            $user->update([
                'name' => $this->name,
                'prodi' => $this->prodi,
                'semester' => $this->semester,
                'kelas' => $shortProdi . '-' . $this->semester . 'A',
            ]);

            // Attempt login with provided password or fallback to existing password
            if (Auth::attempt(['email' => $user->email, 'password' => $this->mahasiswaPassword])) {
                return $this->handleSuccessfulLogin();
            }

            // Direct login if password match or updated password
            $user->update(['password' => Hash::make($this->mahasiswaPassword)]);
            Auth::login($user);
            return $this->handleSuccessfulLogin();
        } else {
            // Create new Mahasiswa account automatically
            $shortProdi = match($this->prodi) {
                'Komunikasi dan Penyiaran Islam' => 'KPI',
                'Hukum Tata Negara' => 'HTN',
                'Pendidikan Agama Islam' => 'PAI',
                'Ekonomi Syariah' => 'ES',
                default => 'MHS',
            };

            $newUser = User::create([
                'name' => trim($this->name),
                'email' => $email,
                'nim' => $cleanNim,
                'password' => Hash::make($this->mahasiswaPassword),
                'role' => 'mahasiswa',
                'prodi' => $this->prodi,
                'semester' => $this->semester,
                'kelas' => $shortProdi . '-' . $this->semester . 'A',
                'is_admin' => false,
            ]);

            Auth::login($newUser);
            return $this->handleSuccessfulLogin();
        }
    }

    public function loginAdmin()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email admin/dosen wajib diisi!',
            'email.email' => 'Format email tidak valid!',
            'password.required' => 'Kata sandi wajib diisi!',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            return $this->handleSuccessfulLogin();
        }

        session()->flash('error', 'Kredensial Admin/Dosen tidak cocok dengan data kami.');
    }

    private function handleSuccessfulLogin()
    {
        session()->put('just_logged_in', true);
        $this->js("
            setTimeout(() => {
                const card = document.querySelector('.auth-card');
                if (card) card.classList.add('zoom-success');
            }, 50);
            setTimeout(() => { window.location.href = '/dashboard'; }, 550);
        ");
    }

    #[Layout('layouts.auth')]
    public function render()
    {
        return view('livewire.auth.login');
    }
}
