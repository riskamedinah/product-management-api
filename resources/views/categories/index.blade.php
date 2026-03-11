@extends('layouts.app')

@section('title', 'Daftar Kategori')

@section('content')
<div class="flex items-start gap-4">
    {{-- Tombol Hamburger --}}
    <button id="hamburgerBtn" class="bg-white shadow rounded-lg p-3 hover:bg-gray-50 focus:outline-none transition shrink-0">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    <div class="w-full">
        <div class="bg-white shadow rounded-lg p-6">
            <h1 class="text-2xl font-semibold text-gray-800 mb-6">Daftar Kategori</h1>

            {{-- Filter & Search Section --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                    
                    {{-- Pencarian --}}
                    <div class="md:col-span-8">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Cari Kategori</label>
                        <input id="searchInput" type="text" placeholder="ID / Nama Kategori"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>

                    {{-- Sorting --}}
                    <div class="md:col-span-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Urutan</label>
                        <div class="flex gap-2">
                            <select id="sortField" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg bg-gray-50 outline-none">
                                <option value="id_kategori">ID Kategori</option>
                                <option value="nama_kategori">Nama</option>
                            </select>
                            <select id="sortDirection" class="px-3 py-2.5 border border-gray-300 rounded-lg bg-gray-50 outline-none">
                                <option value="asc">Naik ↑</option>
                                <option value="desc">Turun ↓</option>
                            </select>
                        </div>
                    </div>

                    <div class="md:col-span-12 flex justify-end gap-3">
                        <button id="resetFilter" class="px-6 py-2 bg-gray-100 text-gray-600 font-semibold rounded-lg hover:bg-gray-200 transition">
                            Reset
                        </button>
                        <button id="applyFilter" class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 shadow-lg shadow-blue-100 transition">
                            Terapkan Filter
                        </button>
                    </div>
                </div>
            </div>

            {{-- Tombol Tambah --}}
            <div class="mb-4">
                <button id="addCategoryBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium shadow-sm transition">
                    + Tambah Kategori
                </button>
            </div>

            {{-- Tabel Kategori --}}
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="border-b text-left text-gray-500 text-sm uppercase tracking-wider">
                            <th class="py-4 font-semibold">ID</th>
                            <th class="py-4 font-semibold">Nama Kategori</th>
                            <th class="py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="categoryTable">
                        {{-- Data di-render oleh JS --}}
                    </tbody>
                </table>
            </div>

            {{-- Loading & Error States --}}
            <div id="loading" class="hidden text-center py-10">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <p class="mt-2 text-gray-500">Memuat data kategori...</p>
            </div>
            <div id="error" class="hidden text-center py-10 text-red-600 bg-red-50 rounded-lg"></div>

            {{-- Pagination --}}
            <div id="pagination" class="mt-6 flex justify-end gap-1"></div>
        </div>
    </div>
</div>

{{-- Modal Tambah/Edit Kategori --}}
<div id="categoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 transition-opacity">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all">
            <div class="p-5 border-b flex justify-between items-center bg-gray-50 rounded-t-xl">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-800">Tambah Kategori</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form id="categoryForm" class="p-6">
                <input type="hidden" id="categoryId">
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Kategori</label>
                    <input type="text" id="categoryName" required placeholder="Contoh: Elektronik"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="closeModal()" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 shadow-lg shadow-blue-100 transition">
                        Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Hapus --}}
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Hapus Kategori?</h3>
                <p class="text-gray-600">Anda akan menghapus kategori <span id="deleteCategoryName" class="font-bold text-gray-900"></span>.</p>
                <p class="text-xs text-red-500 mt-2 italic">Catatan: Kategori yang terhubung dengan produk tidak bisa dihapus.</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-b-xl flex justify-center gap-3">
                <button onclick="closeDeleteModal()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-white transition">Batal</button>
                <button id="confirmDeleteBtn" class="px-6 py-2 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700 shadow-lg shadow-red-100 transition">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/categories.js') }}"></script>

@include('partials.sidebar')
@endsection

@push('styles')
<style>
    .pagination-active {
        background-color: #2563eb !important;
        color: white !important;
        border-color: #2563eb !important;
    }
</style>
@endpush