<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseItemController;
use App\Http\Controllers\NfceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::resource('purchase', PurchaseController::class)->middleware('auth:sanctum');
Route::get('purchase-items', PurchaseItemController::class)->middleware('auth:sanctum');

Route::resource('nfce-key-or-url', NfceController::class)->middleware('auth:sanctum');
