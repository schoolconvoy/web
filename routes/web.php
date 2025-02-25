<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Cache;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

/**
 * Stores a global data of the current ward the parent is looking at
 */
Route::get('/wards/{id}', function (Request $request) {
    Cache::put('ward', $request->id);

    return redirect()->back();
})
->name('wards.switch')
->middleware('role:Parent');

Route::get('/pay', [PaymentController::class, 'redirectToGateway'])->name('pay');
Route::get('/payment/callback', [PaymentController::class, 'handleGatewayCallback']);

/**
 * Public routes
 */
Route::get('/admission', [PublicController::class, 'index'])->name('admission.index');

Route::get('/{user_id}/generate-scoresheet', [App\Http\Controllers\PDFController::class, 'generateScoresheet']);

/**
 * Subscription routes
 */
Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
    Route::get('/', [App\Http\Controllers\SubscriptionController::class, 'index'])->name('index');
    Route::get('/plans/{plan}', [App\Http\Controllers\SubscriptionController::class, 'showSubscriptionForm'])->name('show-subscription-form');
    Route::post('/plans/{plan}/subscribe', [App\Http\Controllers\SubscriptionController::class, 'subscribe'])->name('subscribe');
    Route::get('/{subscription}', [App\Http\Controllers\SubscriptionController::class, 'show'])->name('show');
    Route::post('/{subscription}/cancel', [App\Http\Controllers\SubscriptionController::class, 'cancel'])->name('cancel');
    Route::get('/plans/{plan}/change', [App\Http\Controllers\SubscriptionController::class, 'showChangePlanForm'])->name('show-change-plan-form');
    Route::post('/plans/{plan}/change', [App\Http\Controllers\SubscriptionController::class, 'changePlan'])->name('change-plan');
});
