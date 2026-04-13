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
    public $aiInsight = "";

    public function mount()
    {
        $this->latestResult = Result::where('user_id', Auth::id())
            ->whereNotNull('finished_at')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($this->latestResult) {
            $score = $this->latestResult->total_score;
            
            // 1. Analisis Kelemahan
            $this->subtestStats = \App\Models\UserAnswer::where('result_id', $this->latestResult->id)
                ->with('question.subTest')
                ->get()
                ->groupBy(fn($answer) => $answer->question->subTest->title ?? 'Lainnya')
                ->map(function($answers) {
                    $totalQuestions = $answers->count();
                    $correctAnswers = $answers->filter(fn($ans) => $ans->option && ($ans->option->point ?? 0) > 0)->count();
                    return [
                        'correct_count' => $correctAnswers,
                        'total' => $totalQuestions,
                        'percentage' => round(($totalQuestions > 0) ? ($correctAnswers / $totalQuestions) * 100 : 0),
                    ];
                });

            // 2. AI Insight Generator (Simple logic for roadmap)
            $weakest = $this->subtestStats->sortBy('percentage')->keys()->first();
            $strongest = $this->subtestStats->sortByDesc('percentage')->keys()->first();
            
            if ($weakest) {
                $this->aiInsight = "Berdasarkan analisismu, kamu sangat kuat di **{$strongest}**, pertahankan! Namun, **{$weakest}** masih menjadi hambatan utama bagimu. Fokuslah mendalami {$weakest} selama 2 jam setiap hari selama minggu ini untuk menaikkan skor rata-ratamu secara signifikan.";
            }

            // 3. Rekomendasi Jurusan (Rationalization)
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
