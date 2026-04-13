<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Result;
use Illuminate\Support\Facades\Auth;

class Certificate extends Component
{
    public $result;

    public function mount($resultId)
    {
        $this->result = Result::with(['exam', 'user'])->findOrFail($resultId);

        // Security: Hanya pemilik atau admin yang bisa buka
        if ($this->result->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }
    }

    public function render()
    {
        return view('livewire.certificate')->layout('layouts.blank'); // Gunakan layout tanpa sidebar
    }
}
