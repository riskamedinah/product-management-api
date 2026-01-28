document.addEventListener('DOMContentLoaded', () => loadProducts())

async function loadProducts(page = 1) {
    const res = await fetch(`/api/products?page=${page}`)
    const json = await res.json()

    renderTable(json.data.data)
    renderPagination(json.data)
}

function renderTable(products) {
    const tbody = document.getElementById('productTable')
    const template = document.getElementById('productRow')
    tbody.innerHTML = ''

    products.forEach(p => {
        const row = template.content.cloneNode(true)

        row.querySelector('.id').textContent = p.id_produk
        row.querySelector('.nama').textContent = p.nama_produk
        row.querySelector('.harga').textContent = `Rp ${Number(p.harga).toLocaleString('id-ID')}`
        row.querySelector('.kategori').textContent = p.kategori?.nama_kategori ?? '-'
        row.querySelector('.stok').textContent = p.stok?.stok ?? 0
        row.querySelector('.status').textContent = p.status ? 'Aktif' : 'Nonaktif'

        tbody.appendChild(row)
    })
}

function renderPagination(meta) {
    const container = document.getElementById('pagination')
    if (!container) return

    container.innerHTML = ''

    meta.links.forEach(link => {
        if (!link.url) return

        const btn = document.createElement('button')
        btn.textContent = link.label.replace('&laquo;', '«').replace('&raquo;', '»')
        btn.className = 'px-3 py-1 border rounded'
        btn.disabled = link.active
        btn.onclick = () =>
            loadProducts(new URL(link.url).searchParams.get('page'))

        container.appendChild(btn)
    })
}

