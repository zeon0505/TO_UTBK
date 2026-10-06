<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public string $email = '';
    public string $password = '';

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            session()->put('just_logged_in', true);
            $this->js("
                setTimeout(() => {
                    const card = document.querySelector('.auth-card');
                    if (card) card.classList.add('zoom-success');
                }, 50);
                setTimeout(() => { window.location.href = '/dashboard'; }, 550);
            ");
            return;
        }

        session()->flash('error', 'Kredensial tidak cocok dengan data kami.');
    }

    #[Layout('layouts.auth')]
    public function render()
    {
        return view('livewire.auth.login');
    }
}
