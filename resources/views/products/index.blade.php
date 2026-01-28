@extends('layouts.app')

@section('title', 'Daftar Produk')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <h1 class="text-2xl font-semibold mb-6">Daftar Produk</h1>

    <div class="mb-6 bg-gray-50 rounded-lg p-4">
        <div class="flex flex-wrap gap-4 items-center">
            <div class="flex items-center gap-2">
                <span class="text-gray-700 font-medium">Urutkan berdasarkan:</span>
                <select id="sortField" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                    <option value="id_produk">ID Produk</option>
                    <option value="nama_produk">Nama Produk</option>
                    <option value="harga">Harga</option>
                    <option value="nama_kategori">Kategori</option>
                    <option value="stok">Stok</option>
                    <option value="status">Status</option>
                    <option value="tgl_penerimaan">Tanggal Penerimaan</option>
                    <option value="tgl_kadaluwarsa">Tanggal Kadaluwarsa</option>
                </select>
                
                <select id="sortDirection" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                    <option value="asc">Naik (A-Z / 0-9)</option>
                    <option value="desc">Turun (Z-A / 9-0)</option>
                </select>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="border-b text-left text-gray-500 text-sm">
                    <th class="py-3 font-medium">ID</th>
                    <th class="font-medium">Nama</th>
                    <th class="font-medium">Harga</th>
                    <th class="font-medium">Kategori</th>
                    <th class="font-medium">Stok</th>
                    <th class="font-medium">Status</th>
                </tr>
            </thead>
            <tbody id="productTable"></tbody>
        </table>

        <template id="productRow">
            <tr class="border-b hover:bg-gray-50">
                <td class="py-3 id font-medium text-gray-900"></td>
                <td class="nama text-gray-700"></td>
                <td class="harga text-gray-700"></td>
                <td class="kategori text-gray-700">
                    <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded"></span>
                </td>
                <td class="stok text-gray-700"></td>
                <td class="status">
                    <span class="status-badge px-3 py-1 rounded-full text-sm font-medium inline-block"></span>
                </td>
            </tr>
        </template>
    </div>

    <div id="pagination" class="mt-6 flex justify-end gap-1"></div>

    <template id="paginationButton">
        <button class="px-3 py-1 border rounded transition"></button>
    </template>
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