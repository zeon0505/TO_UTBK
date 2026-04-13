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
    public $subtestStats = [];

    public function mount()
    {
        $this->latestResult = Result::where('user_id', Auth::id())
            ->whereNotNull('finished_at')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($this->latestResult) {
            $score = $this->latestResult->total_score;
            
            // 1. Analisis Kelemahan (Breakdown per Sub-tes)
            $this->subtestStats = \App\Models\UserAnswer::where('result_id', $this->latestResult->id)
                ->with('question.subTest')
                ->get()
                ->groupBy(function($answer) {
                    return $answer->question->subTest->title ?? 'Lainnya';
                })
                ->map(function($answers) {
                    $totalQuestions = $answers->count();
                    $correctAnswers = $answers->filter(fn($ans) => $ans->option && $ans->option->point > 0)->count();
                    return [
                        'score' => $answers->sum('score_obtained'),
                        'correct_count' => $correctAnswers,
                        'total' => $totalQuestions,
                        'percentage' => round(($correctAnswers / $totalQuestions) * 100),
                    ];
                });

            // 2. Rekomendasi Jurusan (Rationalization)
            $this->recommendations = Major::where('passing_grade', '<=', $score + 100)
                ->orderBy('passing_grade', 'desc')
                ->take(6)
                ->get()
                ->map(function($major) use ($score) {
                    $diff = $score - $major->passing_grade;
                    if ($diff >= 30) {
                        $major->status = 'AMAN';
                        $major->probability = '95%';
                        $major->color = 'success';
                    } elseif ($diff >= 0) {
                        $major->status = 'BERPELUANG';
                        $major->probability = '75%';
                        $major->color = 'primary';
                    } else {
                        $major->status = 'BERESIKO';
                        $major->probability = '45%';
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
