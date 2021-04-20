<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/initiate', [Controller::class, 'initiateHostedPayment'])->name('initiate_hosted_payment');

Route::get('/collectRequest', [Controller::class, 'collectRequestDetails'])->name('collect_request_details');

Route::post('/verifyRequest', [Controller::class, 'verifyRequest'])->name('verify_request');
