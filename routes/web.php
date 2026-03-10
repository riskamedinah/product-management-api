<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdukViewController;

Route::get('/products', [ProdukViewController::class, 'index']);

Route::get('/categories', function () {
return view('categories.index');
})->name('categories.index');

