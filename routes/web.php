<?php

use App\Livewire\Dashboard;
use App\Livewire\ExamControl;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Admin\QuestionGenerator;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', Login::class)->name('login')->middleware('guest');
Route::get('/register', Register::class)->name('register')->middleware('guest');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/profile', \App\Livewire\ProfileSettings::class)->name('profile');
    Route::get('/tryouts', \App\Livewire\MyTryouts::class)->name('tryouts');
    Route::get('/rationalization', \App\Livewire\Rationalization::class)->name('rationalization');
    Route::get('/leaderboard', \App\Livewire\Leaderboard::class)->name('leaderboard');
    Route::get('/exam/{examId}', ExamControl::class)->name('exam.show');
    Route::get('/review/{resultId}', \App\Livewire\ExamReview::class)->name('exam.review');
    
    // Admin Routes
    Route::middleware('can:admin')->group(function() {
        Route::get('/admin/generator', QuestionGenerator::class)->name('admin.generator');
        Route::get('/admin/exams', \App\Livewire\Admin\ExamManager::class)->name('admin.exams');
    });

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});
