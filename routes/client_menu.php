<?php

use App\Http\Controllers\Api\ApiSystemActionController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DynamicMenuController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\SystemActionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;



Route::get('/menu', [DynamicMenuController::class, 'index'])->name('client_menu');
Route::post('/order', [DynamicMenuController::class, 'store']);
Route::post('/dynamic-menu/unlock', [DynamicMenuController::class, 'unlock']);
