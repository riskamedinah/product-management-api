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

    // POST /products
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_produk' => 'required|string|max:10|unique:produk',
            'nama_produk' => 'required|string|max:100',
            'harga'  => 'required|numeric|min:0',
            'status'  => 'boolean',
            'image_url'  => 'nullable|url',
            'id_kategori'  => 'required|exists:kategori,id_kategori',
            'stok' => 'required|integer|min:0',
            'tgl_penerimaan' => 'required|date',
            'tgl_kadaluwarsa' => 'nullable|date',
        ]);

        if($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try{
            $product = Produk::create([
                'id_produk' => $request->id_produk,
                'nama_produk' => $request->nama_produk,
                'harga' => $request->harga,
                'status' => $request->status ?? 1,
                'image_url' => $request->image_url,
                'id_kategori' => $request->id_kategori
            ]);

            $product->stok()->create([
                'stok' => $request->stok,
                'tgl_penerimaan' => $request->tgl_penerimaan,
                'tgl_kadaluwarsa' => $request->tgl_kadaluwarsa
            ]);

            DB::commit();

            $product->load(['kategori', 'stok']);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan',
                'data' => $product
            ], 201);
        }

        catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // GET /products/{id} -> detail
    public function show(string $id)
    {
        $product = Produk::with(['kategori', 'stok'])->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail produk berhaisl diambil',
            'data' => $product
        ]);
    }

    // PUT /products/{id}
    public function update(Request $request, string $id)
    {
        $product = Produk::with(['kategori', 'stok'])->find($id);

        if(!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_produk' => 'sometimes|required|string|max:100',
            'harga'  => 'sometimes|required|numeric|min:0',
            'status'  => 'sometimes|boolean',
            'image_url'  => 'nullable|url',
            'id_kategori'  => 'sometimes|required|exists:kategori,id_kategori',
            'stok' => 'sometimes|integer|min:0',
            'tgl_penerimaan' => 'sometimes|date',
            'tgl_kadaluwarsa' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
        $product->update([
            'nama_produk' => $request->nama_produk ?? $product->nama_produk,
            'harga' => $request->harga ?? $product->harga,
            'status' => $request->has('status') ? $request->status : $product->status,
            'image_url' => $request->image_url ?? $product->image_url,
            'id_kategori' => $request->id_kategori ?? $product->id_kategori
        ]);

        if ($request->has('stok') || $request->has('tgl_penerimaan') || $request->has('tgl_kadaluwwarsa')) 
            {
            $stokData =[];
            if ($request->has('stok')) $stokData['stok'] = $request->stok;
            if ($request->has('tgl_penerimaan')) $stokData['tgl_penerimaan'] = $request->tgl_penerimaan;
            if ($request->has('tgl_kadaluwarsa')) $stokData['tgl_kadaluwarsa'] = $request->tgl_kadaluwarsa;

            $product->stok()->update($stokData);
        }

        DB::commit();

        $product->refresh();
        $product->load(['kategori', 'stok']);

        return response()->json([
            'success' => true,
            'messages' => 'Produk berhasil diperbarui',
            'data' => $product
        ]);

        }   catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui produk',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    // DELETE /products/{id}
    public function destroy(string $id)
    {
       $product = Produk::find($id);
       
       if (!$product) {
            return response()->json([
            'success' => false,
            'message' => 'Produk tidak ditemukan'
        ], 404);
       }

       DB::beginTransaction();

       try {
        $product->stok()->delete();
        $product->delete();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus'
        ]);

       }  catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui produk',
                'error' => $e->getMessage()
            ], 500);
        }
}
}


