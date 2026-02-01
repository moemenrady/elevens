<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SaleProccess;

// الاشتراكات 

Route::middleware('auth')->group(function () {

    Route::get('/invoices', [InvoiceController::class, 'index'])->name("invoice.index");
    Route::get('/invoices-test/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices-admin/{invoice}', [InvoiceController::class, 'admin_show'])->name('invoices.admin_show');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'client_show'])->name('invoices.client.show');
    Route::get('/clients/{clientId}/invoices', [InvoiceController::class, 'clientInvoices'])->name('client.invoices');
    Route::get('/ajax/search', [InvoiceController::class, 'ajaxSearch'])->name('invoices.ajaxSearch');


    Route::post('/invoices/preview', [InvoiceController::class, 'preview'])->name('invoices.preview');
    Route::post('/invoice/store', [InvoiceController::class, 'store'])->name('invoices.store');

    Route::post('/invoices/print', [InvoiceController::class, 'print'])->name('invoices.print');

    Route::get('/sale-proccess/create', [SaleProccess::class, 'create'])->name('sale_proccess.create')->middleware("auth");
});
