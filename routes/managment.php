<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ExpenseTypeController;
use App\Http\Controllers\FullDayHoursController;
use App\Http\Controllers\HallController;
use App\Http\Controllers\ManagmentController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\SystemEditController;
use App\Http\Controllers\VenuePricingController;
Route::middleware(['auth', 'admin'])->group(function () {
  
  Route::get('/managment', [ManagmentController::class, 'create'])->name('managment.create');
  Route::get('/managment/system-edit', [SystemEditController::class, 'create'])->name('managment-system-edit.create');

  Route::get('/managment-expense-types/create', [ExpenseTypeController::class, 'create'])->name('expense-type.create');
 Route::post('/managment-expense-types/create', [ExpenseTypeController::class, 'store'])->name('expense-type.store');

});