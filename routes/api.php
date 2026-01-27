<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdukController;

Route::prefix('products')->group(function () {
    Route::get('/', [ProdukController::class, 'index']);
    Route::post('/', [ProdukController::class, 'store']);
    Route::get('/{id}', [ProdukController::class, 'show']);
    Route::put('/{id}', [ProdukController::class, 'update']);
    Route::delete('/{id}', [ProdukController::class, 'destroy']);
});