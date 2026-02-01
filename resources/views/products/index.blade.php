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
            <input id="kategoriInput" type="text" placeholder="Nama Kategori"
                class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500 outline-none">
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

        <div class="flex items-end gap-2">
            <button id="applyFilter"
                class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                Terapkan Filter
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