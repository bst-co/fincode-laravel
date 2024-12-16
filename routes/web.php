<?php

use Fincode\Laravel\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::as('fincode.')->group(function () {
    Route::post('webhook/{shop}/{event}', Controllers\FincodeWebhookController::class)->name('webhook');
});
