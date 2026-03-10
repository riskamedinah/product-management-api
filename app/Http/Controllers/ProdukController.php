<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProdukController extends Controller
{
     // GET /categories
       public function getCategories(Request $request)
{
    try {
        $query = Kategori::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kategori', 'like', '%' . $search . '%')
                  ->orWhere('id_kategori', 'like', '%' . $search . '%');
            });
        }

        $sort = $request->get('sort', 'id_kategori');
        $direction = $request->get('direction', 'asc');
        $validSortFields = ['id_kategori', 'nama_kategori'];
        $field = in_array($sort, $validSortFields) ? $sort : 'id_kategori';
        
        $query->orderBy($field, $direction);

        $categories = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $categories->items(),
            'meta' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'total' => $categories->total(),
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengambil data kategori',
            'error' => $e->getMessage()
        ], 500);
    }
}

    // GET /categories/{id}
public function getCategory($id)
{
    $category = Kategori::find($id);
    if (!$category) {
        return response()->json([
            'success' => false,
            'message' => 'Kategori tidak ditemukan'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => $category
    ]);
}

 // POST /categories
public function storeCategory(Request $request)
{
    $validator = Validator::make($request->all(), [
        'nama_kategori' => 'required|string|max:50|unique:kategori,nama_kategori',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    }
    try {
        $lastCategory = Kategori::orderBy('id_kategori', 'desc')->first();
        $nextIdNumber = 1;
        if ($lastCategory) {
            $lastIdNumber = (int) substr($lastCategory->id_kategori, 1);
            $nextIdNumber = $lastIdNumber + 1;
        }
        $newId = 'K' . str_pad($nextIdNumber, 2, '0', STR_PAD_LEFT);

        $category = new Kategori;
        $category->id_kategori = $newId;
        $category->nama_kategori = $request->nama_kategori;
        $category->timestamps = false; // Nonaktifkan timestamps untuk operasi ini
        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan',
            'data' => $category
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal menambah kategori',
            'error' => $e->getMessage()
        ], 500);
    }
}

 // PUT /categories/{id}
public function updateCategory(Request $request, $id)
{
    $category = Kategori::find($id);
    if (!$category) {
        return response()->json([
            'success' => false,
            'message' => 'Kategori tidak ditemukan'
        ], 404);
    }

    $validator = Validator::make($request->all(), [
        'nama_kategori' => 'required|string|max:50|unique:kategori,nama_kategori,' . $id . ',id_kategori'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    try {
        $category->nama_kategori = $request->nama_kategori;
        $category->timestamps = false; // Nonaktifkan timestamps untuk operasi ini
        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui',
            'data' => $category
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal memperbarui kategori',
            'error' => $e->getMessage()
        ], 500);
    }
}

// DELETE /categories/{id}
public function destroyCategory($id)
{
    $category = Kategori::find($id);
    if (!$category) {
        return response()->json([
            'success' => false,
            'message' => 'Kategori tidak ditemukan'
        ], 404);
    }

    $used = Produk::where('id_kategori', $id)->exists();
    if ($used) {
        return response()->json([
            'success' => false,
            'message' => 'Kategori tidak dapat dihapus karena masih digunakan oleh produk'
        ], 400);
    }

    try {
        $category->delete();
        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal menghapus kategori',
            'error' => $e->getMessage()
        ], 500);
    }
}

    // GET /products
   public function index(Request $request)
    {
        try {
            $query = Produk::with(['kategori', 'stok']);

            if ($request->filled('kategori')) {
                $query->where('id_kategori', $request->kategori);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('stok_min') || $request->filled('stok_max')) {
                $query->whereHas('stok', function ($q) use ($request) {
                    if ($request->filled('stok_min')) {
                        $q->where('stok', '>=', $request->stok_min);
                    }
                    if ($request->filled('stok_max')) {
                        $q->where('stok', '<=', $request->stok_max);
                    }
                });
            }

            if ($request->filled('harga_min')) {
                $query->where('harga', '>=', $request->harga_min);
            }
            if ($request->filled('harga_max')) {
                $query->where('harga', '<=', $request->harga_max);
            }

            if ($request->filled('penerimaan')) {
                $tahunBulan = explode('-', $request->penerimaan);
                if (count($tahunBulan) == 2) {
                    $query->whereHas('stok', function ($q) use ($tahunBulan) {
                        $q->whereYear('tgl_penerimaan', $tahunBulan[0])
                          ->whereMonth('tgl_penerimaan', $tahunBulan[1]);
                    });
                }
            }

            if ($request->filled('kadaluwarsa')) {
                $tahunBulan = explode('-', $request->kadaluwarsa);
                if (count($tahunBulan) == 2) {
                    $query->whereHas('stok', function ($q) use ($tahunBulan) {
                        $q->whereYear('tgl_kadaluwarsa', $tahunBulan[0])
                          ->whereMonth('tgl_kadaluwarsa', $tahunBulan[1]);
                    });
                }
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nama_produk', 'like', '%' . $search . '%')
                      ->orWhere('id_produk', 'like', '%' . $search . '%');
                });
            }

            $sort = $request->get('sort', 'id_produk');
            $order = $request->get('order', 'asc');

            if ($sort === 'stok') {
                $query->leftJoin('stok', 'produk.id_produk', '=', 'stok.id_produk')
                      ->select('produk.*')
                      ->orderBy('stok.stok', $order);
            } elseif ($sort === 'nama_kategori') {
                $query->leftJoin('kategori', 'produk.id_kategori', '=', 'kategori.id_kategori')
                      ->select('produk.*')
                      ->orderBy('kategori.nama_kategori', $order);
            } else {
                $validSortFields = ['id_produk', 'nama_produk', 'harga', 'status'];
                $field = in_array($sort, $validSortFields) ? $sort : 'id_produk';
                $query->orderBy($field, $order);
            }

            $products = $query->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'Data produk berhasil diambil',
                'data' => $products
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // POST /products
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_produk' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'stok' => 'required|integer|min:0',
            'tgl_penerimaan' => 'required|date',
            'tgl_kadaluwarsa' => 'nullable|date',
            'image' => 'nullable|image|max:2048',
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
            $imageName = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $imageName = Str::slug($request->nama_produk) . '_' . time() . '.jpg';
                $file->move(public_path('storage/products'), $imageName);
            }
            
            // Generate new sequential ID like P001, P002
            $lastProduct = Produk::orderBy('id_produk', 'desc')->first();
            $nextIdNumber = 1;
            if ($lastProduct) {
                $lastIdNumber = (int) substr($lastProduct->id_produk, 1);
                $nextIdNumber = $lastIdNumber + 1;
            }
            $newId = 'P' . str_pad($nextIdNumber, 3, '0', STR_PAD_LEFT);
            
            $product = Produk::create([
                'id_produk' => $newId,
                'nama_produk' => $request->nama_produk,
                'harga' => $request->harga,
                'status' => $request->status ? 1 : 0,
                'image_url' => $imageName,
                'id_kategori' => $request->id_kategori
            ]);

            $product->stok()->create([
                'stok' => $request->stok,
                'tgl_penerimaan' => $request->tgl_penerimaan,
                'tgl_kadaluwarsa' => $request->tgl_kadaluwarsa
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan',
                'data' => $product->load(['kategori', 'stok'])
            ], 201);

        } catch (\Exception $e) {
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
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail produk berhasil diambil',
            'data' => $product
        ]);
    }

    // PUT /products/{id}
   public function update(Request $request, string $id)
{
    $product = Produk::with(['stok'])->find($id);

    if(!$product) {
        return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
    }

    $validator = Validator::make($request->all(), [
        'nama_produk' => 'sometimes|required|string|max:100',
        'harga'  => 'sometimes|required|numeric|min:0',
        'status'  => 'sometimes',
        'image'   => 'nullable|image|max:2048',
        'id_kategori' => 'sometimes|required|exists:kategori,id_kategori',
        'stok' => 'sometimes|integer|min:0',
        'tgl_penerimaan' => 'sometimes|date',
        'tgl_kadaluwarsa' => 'nullable|date',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    }

    DB::beginTransaction();
    try {
        $dataProduk = $request->only(['nama_produk', 'harga', 'id_kategori']);
        
        $dataProduk['status'] = $request->has('status') ? 1 : 0;

        if ($request->hasFile('image')) {
            if ($product->image_url) {
                $oldPath = public_path('storage/products/' . $product->image_url);
                if (file_exists($oldPath)) unlink($oldPath);
            }
            $file = $request->file('image');
            $fileName = Str::slug($request->nama_produk ?? $product->nama_produk) . '_' . time() . '.jpg';
            $file->move(public_path('storage/products'), $fileName);
            $dataProduk['image_url'] = $fileName;
        }

        $product->update($dataProduk);

        $stokData = $request->only(['stok', 'tgl_penerimaan', 'tgl_kadaluwarsa']);
        if (!empty($stokData)) {
            $product->stok()->update($stokData);
        }

        DB::commit();
        return response()->json(['success' => true, 'message' => 'Produk diperbarui']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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
        if ($product->image_url) {
            $path = public_path('storage/products/' . $product->image_url);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $product->stok()->delete();
        $product->delete();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => 'Gagal menghapus produk',
            'error' => $e->getMessage()
        ], 500);
    }
}
}