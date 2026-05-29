<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->middleware('auth')->name('main.create');

    // الصفحة الرئيسية
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // راوتات جلب الأقسام (AJAX)
    Route::get('/dashboard/section/{section}', [DashboardController::class, 'loadSection']);
});
