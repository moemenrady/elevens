
<?php

use App\Http\Controllers\PartnerController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::resource('partners', PartnerController::class);
    Route::post('/partners/calculate', [PartnerController::class, 'calculate'])->name('partners.calculate');
});
