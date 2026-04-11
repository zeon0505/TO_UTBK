<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Result;
use Illuminate\Support\Facades\Auth;

class MyTryouts extends Component
{
    public function render()
    {
        $results = Result::with('exam')
            ->where('user_id', Auth::id())
            ->whereNotNull('finished_at')
            ->orderBy('finished_at', 'desc')
            ->get();

        return view('livewire.my-tryouts', [
            'results' => $results
        ])->layout('layouts.app');
    }
}
