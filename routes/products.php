<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('/products/add-quantity-page', [ProductController::class, 'addQuantityPage'])->name('products.addQuantityPage');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products/store', [ProductController::class, 'store'])->name('products.store');

    // 2. الراوتات التي تحتوي على متغيرات {product} (أخيراً)
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');

    Route::delete('/products/bulk-delete', [ProductController::class, 'bulkDelete'])
        ->name('products.bulkDelete');

    Route::delete('/products/{product}', [ProductController::class, 'destroy'])
        ->name('products.destroy');
    Route::patch('/products/{product}/update-name', [ProductController::class, 'updateName'])->name('products.updateName');
});
