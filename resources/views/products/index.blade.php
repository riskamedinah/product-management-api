@extends('layouts.app')

@section('title', 'Daftar Produk')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <h1 class="text-2xl font-semibold mb-6">Daftar Produk</h1>

    <div class="overflow-x-auto">
     <table class="w-full border-collapse">
    <thead>
        <tr class="border-b text-left text-gray-500 text-sm">
            <th class="py-3">ID</th>
            <th>Nama</th>
            <th>Harga</th>
            <th>Kategori</th>
            <th>Stok</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody id="productTable"></tbody>
</table>

<template id="productRow">
    <tr class="border-b hover:bg-gray-50">
        <td class="py-3 id"></td>
        <td class="nama"></td>
        <td class="harga"></td>
        <td class="kategori"></td>
        <td class="stok"></td>
        <td class="status"></td>
    </tr>
</template>
    </div>

   <div id="pagination" class="mt-6 flex justify-end gap-1"></div>

</div>

<script src="/js/products.js"></script>
@endsection
