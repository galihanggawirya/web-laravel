<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/items', [ItemController::class, 'index']);
Route::post('/items', [ItemController::class, 'store']);
Route::put('/items', [ItemController::class, 'update']);
Route::delete('/items', [ItemController::class, 'destroy']);

Route::get('/transactions', [TransactionController::class, 'index']);
Route::get('/transactions/create', [TransactionController::class, 'create']);
Route::get('/transactions/show/{id}', [TransactionController::class, 'show']);
Route::post('/transactions', [TransactionController::class, 'store']);
