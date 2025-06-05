<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupervisorDashboardController;
use App\Http\Controllers\InternDashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Supervisor Routes
    Route::middleware(['role:supervisor'])->group(function () {
        Route::get('/supervisor/dashboard', [SupervisorDashboardController::class, 'index'])->name('supervisor.dashboard');
        Route::get('/supervisor/intern/{id}', [SupervisorDashboardController::class, 'viewIntern'])->name('supervisor.intern.view');
    });

    // Intern Routes
    Route::middleware(['role:intern'])->group(function () {
        Route::get('/intern/dashboard', [InternDashboardController::class, 'index'])->name('intern.dashboard');
        Route::get('/intern/profile', [InternDashboardController::class, 'profile'])->name('intern.profile');
    });
});
