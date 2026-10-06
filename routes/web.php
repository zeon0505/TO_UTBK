<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\ExamControl;
use App\Livewire\ExamResult;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\ProfileSettings;
use App\Livewire\Admin\QuestionGenerator;
use App\Livewire\Admin\ExamManager;
use App\Livewire\Admin\UserMonitor;
use App\Livewire\Admin\CourseManager;
use App\Livewire\Admin\ExamGrading;
use App\Http\Controllers\ExamReportController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', Login::class)->name('login')->middleware('guest');
Route::get('/register', Register::class)->name('register')->middleware('guest');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/profile', ProfileSettings::class)->name('profile');
    Route::get('/exam/{examId}', ExamControl::class)->name('exam.show');
    Route::get('/exam/{examId}/result', ExamResult::class)->name('exam.result');
    
    // Dosen & Admin Routes
    Route::middleware('can:dosen')->group(function() {
        Route::get('/admin/courses', CourseManager::class)->name('admin.courses');
        Route::get('/admin/exams', ExamManager::class)->name('admin.exams');
        Route::get('/admin/generator', QuestionGenerator::class)->name('admin.generator');
        Route::get('/admin/grading/{examId}', ExamGrading::class)->name('admin.grading');
        Route::get('/admin/users', UserMonitor::class)->name('admin.users');
        Route::get('/admin/reports/pdf', [ExamReportController::class, 'exportPdf'])->name('admin.reports.pdf');
    });

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});
