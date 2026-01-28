document.addEventListener('DOMContentLoaded', () => {
    loadProducts();
    
    const sortField = document.getElementById('sortField');
    const sortDirection = document.getElementById('sortDirection');
    
    sortField.addEventListener('change', () => {
        loadProducts();
    });
    
    sortDirection.addEventListener('change', () => {
        loadProducts();
    });
});

async function loadProducts(page = 1) {
    const sortField = document.getElementById('sortField').value;
    const sortDirection = document.getElementById('sortDirection').value;
    
    let url = `/api/products?page=${page}`;
    
    if (sortField && sortDirection) {
        url += `&sort=${sortField}&order=${sortDirection}`;
    }
    
    try {
        const res = await fetch(url);
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
    tbody.innerHTML = '';
    
    products.forEach(p => {
        const row = template.content.cloneNode(true);
        
        row.querySelector('.id').textContent = p.id_produk;
        row.querySelector('.nama').textContent = p.nama_produk;
        row.querySelector('.harga').textContent = `Rp ${Number(p.harga).toLocaleString('id-ID')}`;
        row.querySelector('.kategori span').textContent = p.kategori?.nama_kategori || '-';
        
        const stokElement = row.querySelector('.stok');
        const stokValue = p.stok?.stok || 0;
        stokElement.textContent = stokValue;
        
        if (stokValue < 10) {
            stokElement.classList.add('stok-low');
            stokElement.classList.remove('stok-normal');
        } else {
            stokElement.classList.add('stok-normal');
            stokElement.classList.remove('stok-low');
        }
        
        const statusBadge = row.querySelector('.status-badge');
        if (p.status) {
            statusBadge.textContent = 'Aktif';
            statusBadge.classList.add('status-aktif');
            statusBadge.classList.remove('status-nonaktif');
        } else {
            statusBadge.textContent = 'Nonaktif';
            statusBadge.classList.add('status-nonaktif');
            statusBadge.classList.remove('status-aktif');
        }
        
        tbody.appendChild(row);
    });
}

function renderPagination(meta) {
    const container = document.getElementById('pagination');
    const template = document.getElementById('paginationButton');
    
    if (!container || !template) return;
    
    container.innerHTML = '';
    
    meta.links.forEach(link => {
        if (!link.url) return;
        
        const btn = template.content.cloneNode(true).querySelector('button');
        btn.textContent = link.label.replace('&laquo;', '«').replace('&raquo;', '»');
        
        if (link.active) {
            btn.classList.add('pagination-active');
            btn.disabled = true;
        } else {
            btn.classList.add('pagination-inactive');
            btn.onclick = () => {
                const urlParams = new URLSearchParams(new URL(link.url).search);
                loadProducts(urlParams.get('page'));
            };
        }
        
        container.appendChild(btn);
    });
}