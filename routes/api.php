<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::match( ['get','post'], '/verifyCallbackRequest', [Controller::class, 'verifyCallbackRequest'])->name('verify_callback_request');

Route::match( ['get','post'], '/verifyReturnRequest', [Controller::class, 'verifyReturnRequest'])->name('verify_return_request');
