<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Result;
use App\Models\UserAnswer;

#[Layout('layouts.app')]
class ExamMonitor extends Component
{
    public function kickUser(int $resultId): void
    {
        $result = Result::find($resultId);
        if ($result) {
            // Paksa selesai saat ini juga
            $result->update([
                'submitted_at' => now(),
                'finished_at'  => now(),
                'score'        => 0, // Hukuman skor 0
            ]);
            session()->flash('success', 'Peserta berhasil didiskualifikasi (Kicked).');
        }
    }

    public function resetUserSession(int $resultId): void
    {
        $result = Result::find($resultId);
        if ($result) {
            UserAnswer::where('user_id', $result->user_id)
                ->where('exam_id', $result->exam_id)
                ->delete();

            $result->update([
                'submitted_at'     => null,
                'finished_at'      => null,
                'violations_count' => 0,
                'score'            => 0,
                'total_correct_pg' => 0,
                'total_essay_score'=> 0,
                'is_graded'        => false,
                'started_at'       => null,
            ]);

            session()->flash('success', "Status ujian {$result->user->name} berhasil di-reset & dipulihkan!");
        }
    }

    public function render()
    {
        // Ambil ujian yang belum selesai & aktif dikerjakan dalam 30 menit terakhir
        $activeSessions = Result::whereNull('finished_at')
            ->where('updated_at', '>=', now()->subMinutes(30))
            ->with(['user', 'exam'])
            ->latest('updated_at')
            ->get()
            ->map(function($res) {
                // Parsing data violation dari JSON section_data
                $totalViolations = $res->violations_count ?? 0;
                $currentSubTest = $res->exam->title ?? "Soal UTS";
                
                if ($res->section_data && is_array($res->section_data)) {
                    foreach ($res->section_data as $secId => $data) {
                        $totalViolations += ($data['violations'] ?? 0);
                        if (class_exists('\\App\\Models\\SubTest')) {
                            $st = '\\App\\Models\\SubTest'::find($secId);
                            if ($st && isset($st->title)) {
                                $currentSubTest = $st->title;
                            }
                        }
                    }
                }

                $res->violation_count = $totalViolations;
                $res->active_module = $currentSubTest;
                return $res;
            });

        return view('livewire.admin.exam-monitor', [
            'sessions' => $activeSessions
        ]);
    }
}
