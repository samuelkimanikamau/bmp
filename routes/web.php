<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\TicketController;

Route::get('/', function () {
    return view('site.home'); // new view
})->name('home');

Route::post('/enter-code', [SiteController::class, 'detailsSubmit'])->name('site.details.submit');

Route::controller(SiteController::class)->group(function () {
    // Payment flow (place BEFORE the catch-all)
    Route::prefix('pay/{code}')->where(['code' => '[A-Za-z0-9]+'])->group(function () {
        Route::get('/', 'payForm')->name('site.pay.form');
        Route::post('/', 'paySubmit')->middleware('throttle:10,1')->name('site.pay.submit');
        Route::get('/success', 'paySuccess')->name('site.pay.success');
    });

    // Awaiting + polling + failed (binds MpesaLog $log)
    Route::get('pay/await/{log}', 'payAwait')->name('site.pay.await');
    Route::get('pay/status/{log}', 'payStatus')->name('site.pay.status');
    Route::get('pay/failed/{log}', 'payFailed')->name('site.pay.failed');

    // Decline flow (also BEFORE catch-all)
    Route::prefix('decline/{code}')->where(['code' => '[A-Za-z0-9]+'])->group(function () {
        Route::get('/', 'declineForm')->name('site.decline.form');
        Route::post('/', 'declineSubmit')->middleware('throttle:10,1')->name('site.decline.submit');
        Route::get('/done', 'declineDone')->name('site.decline.done');
    });

    // Finally: catch-all invite code
    Route::get('/{code}', 'details')
        ->where('code', '[A-Za-z0-9]+')
        ->name('site.details');
});
Route::get('/ticket/{number}.pdf', [TicketController::class, 'pdf'])->name('ticket.pdf');