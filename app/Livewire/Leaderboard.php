<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Result;
use Illuminate\Support\Facades\DB;

class Leaderboard extends Component
{
    public function render()
    {
        $rankings = User::select('users.name', 'users.school', DB::raw('SUM(results.total_score) as total_score'))
            ->leftJoin('results', 'users.id', '=', 'results.user_id')
            ->whereNotNull('results.finished_at')
            ->groupBy('users.id', 'users.name', 'users.school')
            ->orderByDesc('total_score')
            ->limit(20)
            ->get();

        return view('livewire.leaderboard', [
            'rankings' => $rankings
        ])->layout('layouts.app');
    }
}
