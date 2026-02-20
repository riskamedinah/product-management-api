@extends('layouts.app')

@section('title', 'Daftar Produk')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Daftar Produk</h1>

<div class="bg-white border rounded-lg p-5 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">
                Pencarian
            </label>
            <input id="searchInput" type="text" placeholder="ID / Nama Produk"
                class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">
                Kategori
            </label>
             <select id="kategoriFilter"
        class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
        <option value="">Semua Kategori</option>
    </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">
                Status
            </label>
            <select id="statusFilter"
                class="w-full px-3 py-2 border rounded bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                <option value="">Semua</option>
                <option value="1">Aktif</option>
                <option value="0">Nonaktif</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">
                Stok Minimum
            </label>
            <input id="stokMin" type="number" placeholder="0"
                class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">
                Stok Maksimum
            </label>
            <input id="stokMax" type="number" placeholder="100"
                class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
        </div>

<div>
    <label class="block text-sm font-medium text-gray-600 mb-1">
        Tanggal Penerimaan (Bulan-Tahun)
    </label>
    <input id="penerimaanFilter" type="month" 
        class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
</div>

<div>
    <label class="block text-sm font-medium text-gray-600 mb-1">
        Tanggal Kadaluwarsa (Bulan-Tahun)
    </label>
    <input id="kadaluwarsaFilter" type="month"
        class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
</div>

        <div class="flex items-end gap-2">
            <button id="applyFilter"
                class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                Terapkan Filter
            </button>
        <button id="resetFilter"
            class="w-full px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition">
            Reset Filter
        </button>
        </div>

    </div>
</div>

<div class="bg-gray-50 border rounded-lg p-4 mb-6">
    <div class="flex flex-wrap items-center gap-4">
        <span class="text-sm font-medium text-gray-600">Urutkan:</span>

        <select id="sortField"
            class="px-4 py-2 border rounded bg-white focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="id_produk">ID Produk</option>
            <option value="nama_produk">Nama Produk</option>
            <option value="harga">Harga</option>
            <option value="nama_kategori">Kategori</option>
            <option value="stok">Stok</option>
            <option value="status">Status</option>
            <option value="tgl_penerimaan">Tanggal Penerimaan</option>
            <option value="tgl_kadaluwarsa">Tanggal Kadaluwarsa</option>
        </select>

        <select id="sortDirection"
            class="px-4 py-2 border rounded bg-white focus:ring-2 focus:ring-blue-500 outline-none">
            <option value="asc">Naik</option>
            <option value="desc">Turun</option>
        </select>
    </div>
</div>

  <div class="mt-4 mb-4 flex">
    <button id="addProductBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
    Tambah Produk
    </button>
    </div>

   <div class="overflow-x-auto">
    <table class="w-full border-collapse">
       <thead>
    <tr class="border-b text-left text-gray-500 text-sm">
        <th class="font-medium">ID</th>
        <th class="font-medium">Nama</th>
        <th class="py-3 font-medium">Gambar</th>
        <th class="font-medium">Harga</th>
        <th class="font-medium">Kategori</th>
        <th class="font-medium">Stok</th>
        <th class="font-medium">Status</th>
        <th class="font-medium">Tanggal Penerimaan</th>
        <th class="font-medium">Tanggal Kadaluwarsa</th>
        <th class="font-medium">Aksi</th>
    </tr>
</thead>
        <tbody id="productTable"></tbody>
    </table>

    <template id="productRow">
        <tr class="border-b hover:bg-gray-50">
    <td class="id font-medium text-gray-900"></td>
        <td class="nama text-gray-700"></td>
        <td class="py-3">
            <img class="product-image w-28 h-28 object-cover rounded border">
        </td>
        <td class="harga text-gray-700"></td>
        <td class="kategori text-gray-700">
            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded"></span>
        </td>
        <td class="stok text-gray-700"></td>
        <td class="status">
            <span class="status-badge px-3 py-1 rounded-full text-sm font-medium inline-block"></span>
        </td>
        <td class="tgl-penerimaan text-gray-700"></td>
        <td class="tgl-kadaluwarsa text-gray-700"></td>
        <td class="actions py-3">
            <div class="flex gap-2">
                <button class="view-btn p-2 text-blue-600 hover:bg-blue-50 rounded transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
                <button class="edit-btn p-2 text-yellow-600 hover:bg-yellow-50 rounded transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </button>
                <button class="delete-btn p-2 text-red-600 hover:bg-red-50 rounded transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        </td>
    </tr>
</template>

<div id="pagination" class="mt-6 flex justify-end gap-1"></div>

<template id="paginationButton">
    <button class="px-3 py-1 border rounded transition"></button>
</template>
</div>

<!-- Modal Edit Produk -->
<div id="editProductModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4">
            <div class="p-6 border-b flex justify-between">
                <h3 class="text-xl font-bold">Edit Produk</h3>
                <button onclick="closeEditModal()" class="text-gray-400">&times;</button>
            </div>
            <form id="editProductForm" class="p-6">
                <input type="hidden" name="id_produk_old" id="edit_id_old">
                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium">Nama Produk</label>
                        <input type="text" name="nama_produk" id="edit_nama" class="w-full border rounded p-2">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium">Harga</label>
                        <input type="number" name="harga" id="edit_harga" class="w-full border rounded p-2">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium">Kategori</label>
                        <select name="id_kategori" id="edit_kategori" class="w-full border rounded p-2"></select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium">Stok</label>
                        <input type="number" name="stok" id="edit_stok" class="w-full border rounded p-2">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium">Tgl Penerimaan</label>
                        <input type="date" name="tgl_penerimaan" id="edit_tgl_penerimaan" class="w-full border rounded p-2">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium">Tgl Kadaluwarsa</label>
                        <input type="date" name="tgl_kadaluwarsa" id="edit_tgl_kadaluwarsa" class="w-full border rounded p-2">
                    </div>
                   <div class="mb-4">
    <label class="block text-sm font-medium">Foto Saat Ini</label>
    <img id="edit_preview_image" class="w-24 h-24 object-cover rounded border mb-2" src="" alt="Preview">
    
    <label class="block text-sm font-medium">Ganti Foto (Opsional)</label>
    <input type="file" id="edit_image_input" class="w-full border rounded p-2">
</div>
                    <div class="mb-4 flex items-center">
                        <input type="checkbox" name="status" id="edit_status" value="1" class="mr-2">
                        <label class="text-sm font-medium">Status Aktif</label>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 border rounded">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Produk -->
<div id="detailProductModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md overflow-hidden">
            <div class="p-6 border-b flex justify-between items-center bg-gray-50">
                <h3 class="text-xl font-bold text-gray-800">Informasi Produk</h3>
                <button onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            
            <div class="p-6 text-center">
                <img id="detImg" class="w-40 h-40 object-cover mx-auto rounded-lg border shadow-sm mb-4" src="" alt="">
                <h2 id="detNama" class="text-2xl font-bold text-gray-900 mb-1"></h2>
                <p id="detId" class="text-sm text-gray-500 mb-4"></p>
                
                <div class="grid grid-cols-2 gap-4 text-left border-t pt-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-semibold">Harga</p>
                        <p id="detHarga" class="font-bold text-green-600"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-semibold">Stok</p>
                        <p id="detStok" class="font-bold"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-semibold">Kategori</p>
                        <p id="detKategori" class="font-medium text-blue-600"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-semibold">Status</p>
                        <p id="detStatus" class="font-medium"></p>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-blue-50 rounded-lg text-left text-sm">
                    <p class="mb-1"><strong>Penerimaan:</strong> <span id="detTerima"></span></p>
                    <p><strong>Kadaluwarsa:</strong> <span id="detExp"></span></p>
                </div>
            </div>

            <div class="p-4 bg-gray-50 text-right">
                <button onclick="closeDetailModal()" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-700 transition">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Produk -->
<div id="addProductModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b sticky top-0 bg-white">
                <h3 class="text-xl font-semibold text-gray-900">Tambah Produk Baru</h3>
                <button id="closeModalBtn" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div class="p-6">
                <form id="addProductForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">ID Produk *</label>
                                <input type="text" name="id_produk" required maxlength="10"
                                    class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk *</label>
                                <input type="text" name="nama_produk" required maxlength="100"
                                    class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Harga *</label>
                                <input type="number" name="harga" required min="0"
                                    class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                        <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Foto Produk *</label>
                        <input type="file" id="imageInput" accept="image/jpeg,image/png,image/webp" required
                            class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
                        <p class="text-xs text-gray-500 mt-1">Gambar akan otomatis di-resize & dikonversi ke JPG.</p>
                    </div>
                        </div>
                        
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
                              <select name="id_kategori" required
    class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500 outline-none"
    id="id_kategori"> <option value="">Pilih Kategori</option>
</select>
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Stok *</label>
                                <input type="number" name="stok" required min="0"
                                    class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Penerimaan *</label>
                                <input type="date" name="tgl_penerimaan" required
                                    class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kadaluwarsa</label>
                                <input type="date" name="tgl_kadaluwarsa"
                                    class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            
                            <div class="mb-4">
                                <label class="flex items-center">
                                    <input type="checkbox" name="status" value="1" checked
                                        class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-700">Status Aktif</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div id="formErrors" class="mb-4 text-red-600 text-sm hidden"></div>
                    
                    <div class="flex justify-end gap-3 pt-6 border-t sticky bottom-0 bg-white">
                        <button type="button" id="cancelModalBtn"
                            class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit" id="submitBtn"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                            Simpan Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="/js/products.js"></script>

<style>
    .stok-low {
        color: #dc2626;
        font-weight: 600;
    }
    
    .stok-normal {
        color: #4b5563;
    }
    
    .status-aktif {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .status-nonaktif {
        background-color: #fee2e2;
        color: #991b1b;
    }
    
    .pagination-active {
        background-color: #2563eb;
        color: white;
        cursor: default;
    }
    
    .pagination-inactive {
        background-color: white;
        color: #374151;
    }
    
    .pagination-inactive:hover {
        background-color: #f9fafb;
    }
</style>
@endsection