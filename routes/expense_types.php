
<?php

use App\Http\Controllers\ExpenseTypeController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('expense-types', ExpenseTypeController::class);
});
