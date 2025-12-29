<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoadmapController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\StudyProgramController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Roadmap routes
    Route::get('/roadmap/create', [RoadmapController::class, 'create'])->name('roadmap.create');
    Route::post('/roadmap', [RoadmapController::class, 'store'])->name('roadmap.store');
    Route::get('/roadmap', [RoadmapController::class, 'show'])->name('roadmap.show');
    Route::get('/api/study-program/{programId}/subjects', [RoadmapController::class, 'getSubjectsByProgram'])->name('api.program.subjects');

    // Admin routes
    Route::resource('subjects', SubjectController::class)->middleware('admin');
    Route::resource('study-programs', StudyProgramController::class)->middleware('admin');
});

require __DIR__.'/auth.php';
