<?php

use App\Http\Controllers\AuthController;
use App\Livewire\AttendanceLogs;
use App\Livewire\Dashboard;
use App\Livewire\Kiosk;
use App\Livewire\Students;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');
Route::get('/login', [AuthController::class,'show'])->name('login');
Route::post('/login', [AuthController::class,'login'])->name('login.submit');
Route::get('/kiosk', Kiosk::class)->name('kiosk');
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/students', Students::class)->name('students');
    Route::get('/attendance-logs', AttendanceLogs::class)->name('attendance.logs');
    Route::post('/logout', [AuthController::class,'logout'])->name('logout');
});
