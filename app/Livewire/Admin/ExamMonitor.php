<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Result;
use App\Models\User;
use Carbon\Carbon;

class ExamMonitor extends Component
{
    public function kickUser($resultId)
    {
        $result = Result::find($resultId);
        if ($result) {
            // Paksa selesai saat ini juga
            $result->update([
                'finished_at' => now(),
                'total_score' => 0, // Hukuman skor 0
            ]);
            session()->flash('success', 'Peserta berhasil didiskualifikasi (Kicked).');
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
                $totalViolations = 0;
                $currentSubTest = "Menunggu...";
                
                if ($res->section_data) {
                    foreach ($res->section_data as $secId => $data) {
                        $totalViolations += ($data['violations'] ?? 0);
                        // Ambil subtest terakhir yang aktif
                        $currentSubTest = \App\Models\SubTest::find($secId)->title ?? $currentSubTest;
                    }
                }

                $res->violation_count = $totalViolations;
                $res->active_module = $currentSubTest;
                return $res;
            });

        return view('livewire.admin.exam-monitor', [
            'sessions' => $activeSessions
        ])->layout('layouts.app');
    }
}
