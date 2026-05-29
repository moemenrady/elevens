
<?php

use App\Http\Controllers\CapitalController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {

    Route::resource('capitals', CapitalController::class);

});
