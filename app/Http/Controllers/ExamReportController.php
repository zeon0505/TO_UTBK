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
        $semester = $request->query('semester');
        $courseId = $request->query('course_id');
        $examId = $request->query('exam_id');
        $search = $request->query('search');
        $prodiFilter = $request->query('prodi', $user->prodi);

        $exam = null;
        if ($examId && $examId !== 'all') {
            $exam = Exam::with(['course.lecturer', 'lecturer'])->find($examId);
        }

        $course = null;
        if ($courseId && $courseId !== 'all') {
            $course = Course::with('lecturer')->find($courseId);
        } elseif ($exam && $exam->course) {
            $course = $exam->course;
        }

        // Tentukan semester jika belum ada tetapi course/exam ada
        if ((!$semester || $semester === 'all') && $course && $course->semester) {
            $semester = (string) $course->semester;
        }

        $resultsQuery = Result::with(['user', 'exam.course', 'exam.lecturer'])
            ->where('is_practice', false);

        // Filter Keamanan Prodi / Dosen
        if ($user->prodi) {
            $resultsQuery->whereHas('exam.course', function($q) use ($user) {
                $q->where('prodi', $user->prodi);
            });
        } elseif ($user->isDosen()) {
            $resultsQuery->whereHas('exam', function($q) use ($user) {
                $q->where('lecturer_id', $user->id);
            });
        } elseif ($prodiFilter && $prodiFilter !== 'all') {
            $resultsQuery->whereHas('exam.course', function($q) use ($prodiFilter) {
                $q->where('prodi', $prodiFilter);
            });
        }

        // Filter Semester
        if ($semester && $semester !== 'all') {
            $resultsQuery->whereHas('exam.course', function($q) use ($semester) {
                $q->where('semester', (int) $semester);
            });
        }

        // Filter Mata Kuliah
        if ($courseId && $courseId !== 'all') {
            $resultsQuery->whereHas('exam', function($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        // Filter Sesi UTS
        if ($examId && $examId !== 'all') {
            $resultsQuery->where('exam_id', $examId);
        }

        // Filter Pencarian Mahasiswa jika ada
        if (!empty($search)) {
            $resultsQuery->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%")
                  ->orWhere('kelas', 'like', "%{$search}%");
            });
        }

        $results = $resultsQuery->latest()->get();

        $semText = ($semester && $semester !== 'all') ? 'Semester ' . $semester : null;

        $data = [
            'title' => 'REKAPITULASI NILAI UJIAN TENGAH SEMESTER (UTS)',
            'tahunAkademik' => '2026/2027 (Ganjil)',
            'semester' => $semText,
            'course' => $course,
            'exam' => $exam,
            'prodi' => $prodiFilter ?? 'Semua Program Studi',
            'results' => $results,
            'printedBy' => $user->name,
            'printedAt' => now()->translatedFormat('d F Y H:i'),
        ];

        $pdf = Pdf::loadView('reports.exam-pdf', $data)
            ->setPaper('a4', 'landscape');

        // Penamaan file yang rapi sesuai filter aktif
        $filenameParts = ['Rekap_Nilai_UTS'];
        if ($semText) {
            $filenameParts[] = Str::slug($semText);
        }
        if ($course) {
            $filenameParts[] = Str::slug($course->code ?? $course->name);
        }
        if ($exam) {
            $filenameParts[] = Str::slug($exam->title);
        }
        if (count($filenameParts) === 1) {
            $filenameParts[] = 'Keseluruhan';
        }
        $filenameParts[] = date('Ymd_His');

        $filename = implode('_', $filenameParts) . '.pdf';

        return $pdf->stream($filename);
    }
}
