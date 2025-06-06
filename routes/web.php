<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Basic dashboard for non-admin users
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->role !== 'admin') {
            return redirect('/dashboard')->with('error', 'Unauthorized access.');
        }
        return view('admin.dashboard');
    })->name('dashboard');

    Route::resource('users', UserController::class);
});

require __DIR__.'/auth.php';
