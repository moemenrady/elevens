<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth', 'verified')->group(function () {
    Route::resource('system-accounts', UserController::class);
    Route::put('/system-accounts/{user}/block', [UserController::class, 'blockAccount'])
        ->name('system-accounts.block')
        ->middleware(['auth', 'verified']); // تأكد من وجود الـ middleware المناسب

});
