
<?php
//الفواتير 

use App\Http\Controllers\VariantStockController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::post(
        '/variants/{variant}/stock',
        [VariantStockController::class, 'add']
    )->name('variants.stock.add');
});
