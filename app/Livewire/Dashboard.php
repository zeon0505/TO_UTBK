<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Exam;
use App\Models\Result;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $activeExams;
    public $filterCategory = 'All';
    public $stats = [
        'total_exams' => 0,
        'average_score' => 0,
        'global_rank' => '-',
    ];
    public $scoreHistory = [];
    public $scoreDates = [];
    public $selectedPracticeSubject = '';

    public function mount()
    {
        $this->loadExams();
        
        $myResults = Result::where('user_id', Auth::id())
            ->whereNotNull('finished_at')
            ->get();

        $this->stats['total_exams'] = $myResults->count();
        $this->stats['average_score'] = $myResults->avg('total_score') ?? 0;
        
        // Simple rank logic: sort all users by sum of total_score
        $rankings = Result::groupBy('user_id')
            ->selectRaw('user_id, sum(total_score) as total')
            ->orderByDesc('total')
            ->pluck('user_id')
            ->toArray();
            
        $myRankIndex = array_search(Auth::id(), $rankings);
        if ($myRankIndex !== false) {
            $this->stats['global_rank'] = '#' . ($myRankIndex + 1);
        }

        // Prepare History Chart Data
        $historyData = Result::where('user_id', Auth::id())
            ->whereNotNull('finished_at')
            ->orderBy('finished_at', 'asc')
            ->take(10)
            ->get();
            
        $this->scoreHistory = $historyData->pluck('total_score')->toArray();
        $this->scoreDates = $historyData->map(fn($r) => optional($r->finished_at)->format('d/m') ?? '-')->toArray();
    }

    public function loadExams()
    {
        $query = Exam::where('is_active', true);
        
        if ($this->filterCategory != 'All') {
            $query->where('category', $this->filterCategory);
        }
        
        $exams = $query->get();
        
        // Cek status pengerjaan untuk tiap ujian
        $this->activeExams = $exams->map(function($exam) {
            $result = Result::where('user_id', Auth::id())
                ->where('exam_id', $exam->id)
                ->first();

            if (!$result) {
                $exam->user_status = 'NOT_STARTED';
            } elseif ($result->finished_at) {
                $exam->user_status = 'FINISHED';
                $exam->result_id = $result->id;
            } else {
                $exam->user_status = 'IN_PROGRESS';
                // Deteksi sub-tes terakhir
                $lastAnswer = \App\Models\UserAnswer::where('result_id', $result->id)
                    ->with('question')
                    ->latest()
                    ->first();
                $exam->last_subject_id = $lastAnswer->question->sub_test_id ?? $exam->subTests->first()->id ?? null;
            }
            return $exam;
        });
    }

    public function getPracticeSubjectsProperty()
    {
        // Get unique subjects from active exams, grouping by title to avoid duplicates
        return \App\Models\SubTest::whereHas('exam', function($q) {
            $q->where('is_active', true);
        })->get()->unique('title');
    }

    public function startPractice()
    {
        if (!$this->selectedPracticeSubject) {
            session()->flash('error', 'Silakan pilih materi terlebih dahulu.');
            return;
        }

        $subTest = \App\Models\SubTest::find($this->selectedPracticeSubject);
        if ($subTest) {
            return redirect('/exam/' . $subTest->exam_id . '?subject=' . $subTest->id);
        }
    }

    public function setFilter($category)
    {
        $this->filterCategory = $category;
        $this->loadExams();
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('layouts.app');
    }
}
