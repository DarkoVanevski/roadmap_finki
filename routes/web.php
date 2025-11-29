<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoadmapController;
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
});

require __DIR__.'/auth.php';
