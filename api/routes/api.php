<?php

use App\Http\Controllers\PlanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/', function () {
    return response()->json(['message' => 'ok']);
});

Route::apiResource('plans', PlanController::class, ['only' => 'index']);

Route::apiSingleton('user', UserController::class, ['only' => 'show']);

// Contract routes
Route::post('contracts', [App\Http\Controllers\ContractController::class, 'store']);
Route::get('contracts/{userId}', [App\Http\Controllers\ContractController::class, 'show']);
Route::get('contracts/{userId}/history', [App\Http\Controllers\ContractController::class, 'history']);

// Payment routes
Route::post('payments/pix', [App\Http\Controllers\PaymentController::class, 'generatePix']);
Route::post('payments/confirm', [App\Http\Controllers\PaymentController::class, 'confirmPayment']);
Route::get('payments/{paymentId}', [App\Http\Controllers\PaymentController::class, 'show']);
