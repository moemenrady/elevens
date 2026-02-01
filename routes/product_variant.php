<?php

use App\Http\Controllers\ProductVariantController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::get('/', function () {

        return view('main.create');
    })->middleware('auth')->name('main.create');



    Route::get(
        '/products/{product}/variants/create',
        [ProductVariantController::class, 'create']
    )->name('variants.create');

    Route::post(
        '/products/{product}/variants',
        [ProductVariantController::class, 'store']
    )->name('variants.store');

    Route::get(
        'product/{product}/variants',
        [ProductVariantController::class, 'variants']
    )->name('product.variants');
    Route::post(
        '/variants/{variant}/add-stock',
        [ProductVariantController::class, 'addStock']
    );


    Route::post(
        '/variants/{variant}/add-quantity',
        [ProductVariantController::class, 'addQuantity']
    )->name('variants.addQuantity');



    Route::post(
        '/products/{product}/add-color',
        [ProductVariantController::class, 'storeColor']
    )->name('variants.storeColor');
});
