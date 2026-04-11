<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Result;
use App\Models\Major;
use Illuminate\Support\Facades\Auth;

class Rationalization extends Component
{
    public $latestResult;
    public $recommendations = [];

    public function mount()
    {
        $this->latestResult = Result::where('user_id', Auth::id())
            ->whereNotNull('finished_at')
            ->orderBy('total_score', 'desc')
            ->first();

        if ($this->latestResult) {
            $score = $this->latestResult->total_score;
            
            // Find majors where user score is close or above passing grade
            $this->recommendations = Major::where('passing_grade', '<=', $score + 50)
                ->orderBy('passing_grade', 'desc')
                ->get()
                ->map(function($major) use ($score) {
                    $diff = $score - $major->passing_grade;
                    if ($diff >= 20) {
                        $major->status = 'AMAN';
                        $major->probability = '90%';
                        $major->color = 'success';
                    } elseif ($diff >= 0) {
                        $major->status = 'BERPELUANG';
                        $major->probability = '70%';
                        $major->color = 'primary';
                    } else {
                        $major->status = 'BERESIKO';
                        $major->probability = '40%';
                        $major->color = 'warning';
                    }
                    return $major;
                });
        }
    }

    public function render()
    {
        return view('livewire.rationalization')->layout('layouts.app');
    }
}
