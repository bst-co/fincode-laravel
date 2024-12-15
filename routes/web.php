<?php

use Fincode\Laravel\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/fincode', 'as' => 'fincode.'], function () {
    Route::post('webhook/{prefix}/{event}', Controllers\FincodeWebhookController::class)->name('webhook');
});
