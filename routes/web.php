<?php

use Fincode\Laravel\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::group(['as' => 'fincode.'], function () {
    Route::post('webhook/{hash}/{event}', Controllers\FincodeWebhookController::class)->name('webhook');
});
