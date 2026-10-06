<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Result;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class ExamReportController extends Controller
{
    public function exportPdf(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $examId = $request->query('exam_id');
        $prodiFilter = $request->query('prodi', $user->prodi);

        $exam = null;
        if ($examId) {
            $exam = Exam::with(['course', 'lecturer'])->find($examId);
        }

        $resultsQuery = Result::with(['user', 'exam.course', 'exam.lecturer']);

        if ($examId) {
            $resultsQuery->where('exam_id', $examId);
        } elseif ($prodiFilter) {
            $resultsQuery->whereHas('exam.course', function($q) use ($prodiFilter) {
                $q->where('prodi', $prodiFilter);
            });
        } elseif ($user->isDosen()) {
            $resultsQuery->whereHas('exam', function($q) use ($user) {
                $q->where('lecturer_id', $user->id);
            });
        }

        $results = $resultsQuery->latest()->get();

        $data = [
            'title' => 'REKAPITULASI NILAI UJIAN TENGAH SEMESTER (UTS)',
            'tahunAkademik' => '2026/2027 (Ganjil)',
            'exam' => $exam,
            'prodi' => $prodiFilter ?? 'Semua Program Studi',
            'results' => $results,
            'printedBy' => $user->name,
            'printedAt' => now()->translatedFormat('d F Y H:i'),
        ];

        $pdf = Pdf::loadView('reports.exam-pdf', $data)
            ->setPaper('a4', 'landscape');

        $filename = 'Rekap_Nilai_UTS_' . ($exam ? Str::slug($exam->title) : 'Keseluruhan') . '_' . date('Ymd_His') . '.pdf';

        return $pdf->stream($filename);
    }
}
