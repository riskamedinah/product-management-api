<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProdukController extends Controller
{
    // GET /products
    public function index(Request $request)
    {
        $query = Produk::with(['kategori', 'stok']);

        if ($request->has('kategori')) {
            $query->whereHas('kategori', function ($q) use ($request) {
                $q->where('nama_kategori', 'like', '%' . $request->kategori . '%');
            });
        }


        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

 
        if ($request->has('stok_min') || $request->has('stok_max')) {
            $query->whereHas('stok', function ($q) use ($request) {
                if ($request->has('stok_min')) {
                    $q->where('stok', '>=', $request->stok_min);
                }
                 if ($request->has('stok_max')) {
                    $q->where('stok', '<=', $request->stok_max);
                }
            });
        }


        if ($request->has('harga_min')) {
            $query->where('harga', '>=', $request->harga_min);
        }
        if ($request->has('harga_max')) {
            $query->where('harga', '<=', $request->harga_max);
        }

        if ($request->has('penerimaan')) {
            $tahunBulan = explode('-', $request->penerimaan);
            if (count($tahunBulan) == 2) {
                $query->whereHas('stok', function ($q) use ($tahunBulan) {
                    $q->whereYear('tgl_penerimaan', $tahunBulan[0])->whereMonth('tgl_penerimaan', $tahunBulan[1]);
                });
            }
        }


         if ($request->has('kadaluwarsa')) {
            $tahunBulan = explode('-', $request->kadaluwarsa);
            if (count($tahunBulan) == 2) {
                $query->whereHas('stok', function ($q) use ($tahunBulan) {
                    $q->whereYear('tgl_kadaluwarsa', $tahunBulan[0])->whereMonth('tgl_kadaluwarsa', $tahunBulan[1]);
                });
            }
        }


        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', '%' . $search . '%')->orWhere('id_produk', 'like', '%' . $search . '%');
            });
        }

        
        $query->orderBy('id_produk');

        $products = $query->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Data produk berhasil diambil',
            'data' => $products
        ]);
    }

    public function store(Request $request)
    {
    
    }

    public function show(string $id)
    {
        
    }

    public function update(Request $request, string $id)
    {
        
    }

    public function destroy(string $id)
    {
        
    }
}
