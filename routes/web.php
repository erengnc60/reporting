<?php

use Illuminate\Support\Facades\Route;

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
    return view('login');
});


Route::get('login', [\App\Http\Controllers\LoginController::class, 'login'])->name('login');
Route::post('login', [\App\Http\Controllers\LoginController::class,'loginApi'])->name('loginApi');
Route::get('dashboard',[\App\Http\Controllers\TransactionReportController::class,'transactionView'])->name('Transactionview');
Route::post('dashboard-post',[\App\Http\Controllers\TransactionReportController::class,'Transaction'])->name('Transaction');
Route::get('transaction-query',[\App\Http\Controllers\TransactionQueryController::class, 'queryView'])->name('TransactionQuery');
Route::post('transaction-query-post', [\App\Http\Controllers\TransactionQueryController::class,'query'])->name('query');
