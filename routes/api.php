<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\MpesaCallbackController;

Route::post('/570aa5d6-f27d-45d4-8b65-2868c0e2f297', [MpesaCallbackController::class, 'stkCallback'])
    ->name('mpesa.callback');
