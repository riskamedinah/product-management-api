<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProdukViewController extends Controller
{
    public function index()
    {
        return view('products.index');
    }
}