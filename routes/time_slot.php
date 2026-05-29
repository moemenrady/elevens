<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\PrivateSessionTimeSlotController;
use Illuminate\Support\Facades\Route;

// إضافة فترة جديدة لحجز معين
Route::post('bookings/{booking}/time-slots', [PrivateSessionTimeSlotController::class, 'store'])->name('time-slots.store');

// تعديل بيانات فترة (مثل عدد الأفراد)
Route::put('time-slots/{timeSlot}', [PrivateSessionTimeSlotController::class, 'update'])->name('time-slots.update');

// إنهاء الفترة وحساب الحساب
Route::put('time-slots/{timeSlot}/end', [PrivateSessionTimeSlotController::class, 'endSession'])->name('time-slots.endSession');

// إلغاء الفترة تماماً
Route::delete('time-slots/{timeSlot}', [PrivateSessionTimeSlotController::class, 'destroy'])->name('time-slots.destroy');
Route::post('estimate-time-slot', [PrivateSessionTimeSlotController::class, 'estimate_time_slot'])
    ->name('estimate-time-slot');
