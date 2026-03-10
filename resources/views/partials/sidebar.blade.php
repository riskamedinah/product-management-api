{{-- resources/views/partials/sidebar.blade.php --}}
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden transition-opacity"></div>

<div id="sidebar" class="fixed top-0 left-0 w-64 h-full bg-white shadow-lg z-50 transform -translate-x-full transition-transform duration-300 ease-in-out">
    <div class="p-4 flex justify-between items-center border-b">
        <h2 class="text-lg font-semibold text-gray-800">Menu</h2>
        <button id="closeSidebar" class="text-gray-500 hover:text-gray-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <nav class="p-4">
        <ul class="space-y-2">
            @php
                $menus = [
                    ['label' => 'Produk', 'url' => '/products', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                    ['label' => 'Kategori', 'url' => '/categories', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z'],
                ];
            @endphp

            @foreach ($menus as $menu)
                @php
                    $isActive = request()->is(ltrim($menu['url'], '/') . '*');
                @endphp
                <li>
                    <a href="{{ $menu['url'] }}" 
                       class="flex items-center space-x-2 p-2 rounded-lg transition 
                       {{ $isActive ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $menu['icon'] }}"/>
                        </svg>
                        <span>{{ $menu['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</div>