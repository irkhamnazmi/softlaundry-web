<?php

use App\Http\Controllers\API\CashierController;
use App\Http\Controllers\API\MemberController;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('register', [CashierController::class, 'register']);
Route::post('login', [CashierController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [CashierController::class, 'logout']);
    Route::get('profile', [CashierController::class, 'profile']);
    Route::get('cashiers', [CashierController::class, 'all']);
    Route::get('cashiers/get/{id}', [CashierController::class, 'get']);
    Route::put('cashiers/edit', [CashierController::class, 'edit']);
    Route::delete('cashiers/delete/{id}', [CashierController::class, 'delete']);

    Route::get('members', [MemberController::class, 'all']);
    Route::get('members/get/{id}', [MemberController::class, 'get']);
    Route::post('members/add', [MemberController::class, 'add']);
    Route::put('members/edit', [MemberController::class, 'edit']);
    Route::delete('members/delete/{id}', [MemberController::class, 'delete']);

    Route::get('services', [ServiceController::class, 'all']);
    Route::get('services/get/{id}', [ServiceController::class, 'get']);
    Route::post('services/add', [ServicesController::class, 'add']);
    Route::put('services/edit', [ServiceController::class, 'edit']);
    Route::delete('services/delete/{id}', [ServiceController::class, 'delete']);

    Route::get('transactions', [TransactionController::class, 'all']);
    Route::get('transactions/get/{id}', [TransactionController::class, 'get']);
    Route::post('transactions/checkout', [TransactionController::class, 'checkout']);
});
