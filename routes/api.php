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

Route::prefix('categories')->group(function () {
    Route::get('/', [ProdukController::class, 'getCategories']);
    Route::post('/', [ProdukController::class, 'storeCategory']);
    Route::get('/{id}', [ProdukController::class, 'getCategory']);
    Route::put('/{id}', [ProdukController::class, 'updateCategory']);
    Route::delete('/{id}', [ProdukController::class, 'destroyCategory']);
});