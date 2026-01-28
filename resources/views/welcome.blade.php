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
            <tbody>
                @foreach ($products as $p)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3">{{ $p->id_produk }}</td>
                    <td>{{ $p->nama_produk }}</td>
                    <td>Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
                    <td>{{ $p->kategori->nama_kategori ?? '-' }}</td>
                    <td>{{ $p->stok->stok ?? 0 }}</td>
                    <td>
                        @if ($p->status)
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded">Aktif</span>
                        @else
                            <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded">Nonaktif</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $products->links() }}
    </div>
</div>
@endsection
