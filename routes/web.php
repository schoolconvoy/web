<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\PaymentController;
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
