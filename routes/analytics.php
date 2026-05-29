`<?php

use App\Http\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->group(function(){
    Route::resource('analytics', AnalyticsController::class);
  

});

