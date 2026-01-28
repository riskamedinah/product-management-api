<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdukViewController;

Route::get('/products', [ProdukViewController::class, 'index']);

