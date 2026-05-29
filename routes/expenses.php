<?php

use App\Http\Controllers\ExpenseController;
use Illuminate\Support\Facades\Route;



Route::middleware('auth')->group(function () {
    Route::resource('expenses', ExpenseController::class);
});