document.addEventListener('DOMContentLoaded', () => {
    loadProducts();

    document.getElementById('sortField').addEventListener('change', loadProducts);
    document.getElementById('sortDirection').addEventListener('change', loadProducts);
    document.getElementById('applyFilter').addEventListener('click', loadProducts);

    document.getElementById('searchInput').addEventListener('keyup', e => {
        if (e.key === 'Enter') loadProducts();
    });
});

async function loadProducts(page = 1) {
    const params = new URLSearchParams();
    params.append('page', page);

    const search = document.getElementById('searchInput').value;
    const kategori = document.getElementById('kategoriInput').value;
    const status = document.getElementById('statusFilter').value;
    const stokMin = document.getElementById('stokMin').value;
    const stokMax = document.getElementById('stokMax').value;
    const sort = document.getElementById('sortField').value;
    const order = document.getElementById('sortDirection').value;

    if (search) params.append('search', search);
    if (kategori) params.append('kategori', kategori);
    if (status !== '') params.append('status', status);
    if (stokMin) params.append('stok_min', stokMin);
    if (stokMax) params.append('stok_max', stokMax);
    if (sort && order) {
        params.append('sort', sort);
        params.append('order', order);
    }

    try {
        const res = await fetch(`/api/products?${params.toString()}`);
        const json = await res.json();
        
        if (json.success) {
            renderTable(json.data.data);
            renderPagination(json.data);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function renderTable(products) {
    const tbody = document.getElementById('productTable');
    const template = document.getElementById('productRow');
    if (!tbody || !template) return;
    
    tbody.innerHTML = '';
    
    products.forEach(p => {
        const row = template.content.cloneNode(true);
        
        // Tambahkan gambar produk
        const img = row.querySelector('.product-image');
        if (img) {
            img.src = p.image_url
                ? `/storage/products/${p.image_url}`
                : '/images/no-image.png';
            img.alt = p.nama_produk;
            
            // Fallback jika gambar error
            img.onerror = function() {
                this.src = '/images/no-image.png';
            };
        }
        
        const idElement = row.querySelector('.id');
        const namaElement = row.querySelector('.nama');
        const hargaElement = row.querySelector('.harga');
        const kategoriElement = row.querySelector('.kategori span');
        const stokElement = row.querySelector('.stok');
        const statusBadge = row.querySelector('.status-badge');
        
        // Format ID
        if (idElement) idElement.textContent = p.id_produk;
        
        if (namaElement) namaElement.textContent = p.nama_produk;
        if (hargaElement) hargaElement.textContent = `Rp ${Number(p.harga).toLocaleString('id-ID')}`;
        if (kategoriElement) kategoriElement.textContent = p.kategori?.nama_kategori || '-';
        
        if (stokElement) {
            const stokValue = p.stok?.stok || 0;
            stokElement.textContent = stokValue;
            
            if (stokValue < 10) {
                stokElement.classList.add('stok-low');
                stokElement.classList.remove('stok-normal');
            } else {
                stokElement.classList.add('stok-normal');
                stokElement.classList.remove('stok-low');
            }
        }
        
        if (statusBadge) {
            if (p.status) {
                statusBadge.textContent = 'Aktif';
                statusBadge.classList.add('status-aktif');
                statusBadge.classList.remove('status-nonaktif');
            } else {
                statusBadge.textContent = 'Nonaktif';
                statusBadge.classList.add('status-nonaktif');
                statusBadge.classList.remove('status-aktif');
            }
        }
        
        tbody.appendChild(row);
    });
}

function renderPagination(meta) {
    const container = document.getElementById('pagination');
    const template = document.getElementById('paginationButton');
    
    if (!container || !template) return;
    
    container.innerHTML = '';
    
    if (!meta || !meta.links) return;
    
    meta.links.forEach(link => {
        if (!link.url) return;
        
        const btn = template.content.cloneNode(true).querySelector('button');
        if (!btn) return;
        
        btn.textContent = link.label.replace('&laquo;', '«').replace('&raquo;', '»');
        
        if (link.active) {
            btn.classList.add('pagination-active');
            btn.disabled = true;
        } else {
            btn.classList.add('pagination-inactive');
            btn.onclick = () => {
                const urlParams = new URLSearchParams(new URL(link.url).search);
                const page = urlParams.get('page');
                loadProducts(page);
            };
        }
        
        container.appendChild(btn);
    });
}