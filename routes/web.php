<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [PublicProfileController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard Media Routes
    Route::get('/dashboard/medios', [MediaController::class, 'index'])->name('dashboard.media.index');
    Route::post('/dashboard/medios', [MediaController::class, 'store'])->name('dashboard.media.store');
    Route::delete('/dashboard/medios/{id}', [MediaController::class, 'destroy'])->name('dashboard.media.destroy');

    // Dashboard TPV Routes
    Route::get('/dashboard/tpv', [ProfileController::class, 'showPreview'])->name('dashboard.tpv.show');
    Route::get('/dashboard/tpv/editar', [ProfileController::class, 'editArtist'])->name('dashboard.tpv.edit');
    Route::put('/dashboard/tpv/editar', [ProfileController::class, 'updateArtist'])->name('dashboard.tpv.update');

    // Dashboard Calendar Routes
    Route::get('/dashboard/calendario', [EventController::class, 'index'])->name('dashboard.calendar.index');
    Route::post('/dashboard/calendario/eventos', [EventController::class, 'store'])->name('dashboard.calendar.store');
    Route::delete('/dashboard/calendario/eventos/{id}', [EventController::class, 'destroy'])->name('dashboard.calendar.destroy');
});

require __DIR__.'/auth.php';

// Public dynamic TPV route (must be at the very bottom to avoid route hijacking)
Route::get('/{slug}', [ProfileController::class, 'show'])->name('profile.show');
