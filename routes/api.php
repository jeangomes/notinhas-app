<?php

use App\Http\Controllers\ListStoresController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseItemController;
use App\Http\Controllers\NfceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::middleware(['auth:sanctum'])->group(function () {
    Route::resource('purchase', PurchaseController::class);
    Route::get('purchase-items', PurchaseItemController::class);

    Route::resource('nfce-key-or-url', NfceController::class);
    Route::get('stores', ListStoresController::class);
});
