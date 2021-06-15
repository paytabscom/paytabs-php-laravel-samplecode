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

Route::get('/', [Controller::class, 'index'])->name('index');

Route::get('/hostedPayment', [Controller::class, 'hostedPayment'])->name('hosted_payment');
Route::get('/hostedPaymentLaravelPkg', [Controller::class, 'hostedPayment'])->name('hosted_payment_laravel_pkg');

Route::post('/hostedPayment', [Controller::class, 'doHostedPayment'])->name('do_hosted_payment');
Route::post('/hostedPaymentLaravelPkg', [Controller::class, 'doHostedPaymentLaravelPkg'])->name('do_hosted_payment_laravel_pkg');

// new route accepts both get & post for just verifing return request signature

Route::match( ['get', 'post'], '/managedForm', [Controller::class, 'managedForm'])->name('managed_form');

Route::match( ['post'], '/processManagedForm', [Controller::class, 'processManagedForm'])->name('process_managed_form');

Route::match( ['get', 'post'], '/ownForm', [Controller::class, 'ownForm'])->name('own_form');

Route::match( ['post'], '/processOwnForm', [Controller::class, 'processOwnForm'])->name('process_own_form');

Route::get( '/capture/{cartId}', [Controller::class, 'capture'])->name('capture');

Route::get( '/void/{cartId}', [Controller::class, 'void'])->name('void');

Route::get( '/refund/{cartId}', [Controller::class, 'refund'])->name('refund');

Route::get( '/paymentFinished', [Controller::class, 'paymentFinished'])->name('payment_finished');


