document.addEventListener('DOMContentLoaded', () => {
    loadProducts();
    loadCategories();
    initEventListeners();
});

let currentEditId = null;

function initEventListeners() {
    const listen = (id, event, callback) => {
        const el = document.getElementById(id);
        if (el) el.addEventListener(event, callback);
    };

    listen('sortField', 'change', () => loadProducts());
    listen('sortDirection', 'change', () => loadProducts());
    listen('applyFilter', 'click', () => loadProducts());
    listen('resetFilter', 'click', resetFilters);
    
    listen('addProductBtn', 'click', openAddModal);
    listen('closeModalBtn', 'click', closeAddModal);
    listen('cancelModalBtn', 'click', closeAddModal);
    listen('addProductForm', 'submit', handleAddProduct);
    listen('editProductForm', 'submit', handleEditProduct);

    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', e => {
            if (e.key === 'Enter') loadProducts();
        });
    }

    const addProductModal = document.getElementById('addProductModal');
    if (addProductModal) {
        addProductModal.addEventListener('click', e => {
            if (e.target.id === 'addProductModal') closeAddModal();
        });
    }
}

function openAddModal() {
    const modal = document.getElementById('addProductModal');
    if (modal) modal.classList.remove('hidden');
    
    const tglInput = document.querySelector('input[name="tgl_penerimaan"]');
    if (tglInput) {
        tglInput.value = new Date().toISOString().split('T')[0];
    }
    loadCategories();
}

function closeAddModal() {
    const modal = document.getElementById('addProductModal');
    if (modal) modal.classList.add('hidden');
    resetAddForm();
}

function resetAddForm() {
    const form = document.getElementById('addProductForm');
    if (form) form.reset();

    const errorDiv = document.getElementById('formErrors');
    if (errorDiv) {
        errorDiv.classList.add('hidden');
        errorDiv.innerHTML = '';
    }

    const statusRadio = document.querySelector('input[name="status"]');
    if (statusRadio) statusRadio.checked = true;

    const tglInput = document.querySelector('input[name="tgl_penerimaan"]');
    if (tglInput) {
        tglInput.value = new Date().toISOString().split('T')[0];
    }
}

async function loadCategories() {
    try {
        const res = await fetch('/api/categories');
        const json = await res.json();
        
        if (json.success) {
            const options = json.data.map(c => 
                `<option value="${c.id_kategori}">${c.nama_kategori}</option>`
            ).join('');
            
            const filterSelect = document.getElementById('kategoriFilter');
            if (filterSelect) {
                filterSelect.innerHTML = '<option value="">Semua Kategori</option>' + options;
            }

            const addSelect = document.getElementById('id_kategori');
            if (addSelect) {
                addSelect.innerHTML = '<option value="">Pilih Kategori</option>' + options;
            }

            const editSelect = document.getElementById('edit_kategori');
            if (editSelect) {
                editSelect.innerHTML = options;
            }
        }
    } catch (error) {
        console.error("Gagal memuat kategori ke filter:", error);
    }
}

async function loadProducts(page = 1) {
    const getVal = (id) => document.getElementById(id)?.value || '';
    
    const params = new URLSearchParams({
        page: page,
        search: getVal('searchInput'),
        kategori: getVal('kategoriFilter'),
        status: getVal('statusFilter'),
        stok_min: getVal('stokMin'),
        stok_max: getVal('stokMax'),
        penerimaan: getVal('penerimaanFilter'),
        kadaluwarsa: getVal('kadaluwarsaFilter'),
        sort: getVal('sortField') || 'id_produk',
        order: getVal('sortDirection') || 'asc'
    });

    try {
        const res = await fetch(`/api/products?${params.toString()}`);
        const json = await res.json();
        
        if (json.success) {
            renderTable(json.data.data);
            renderPagination(json.data);
        }
    } catch (error) {
        console.error(error);
    }
}

async function handleAddProduct(e) {
    e.preventDefault();
    
    const form = e.target;
    const submitBtn = document.getElementById('submitBtn');
    const imageInput = document.getElementById('imageInput');
    const formData = new FormData(form);

    try {
        if (submitBtn) {
            submitBtn.innerHTML = 'Memproses Gambar...';
            submitBtn.disabled = true;
        }

        if (imageInput && imageInput.files[0]) {
            const resizedBlob = await resizeImage(imageInput.files[0], 800, 800);
            formData.delete('image_url');
            formData.append('image', resizedBlob, 'product.jpg');
        }

        const response = await fetch('/api/products', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert('Produk berhasil ditambahkan!');
            closeAddModal();
            loadProducts();
        } else {
            displayFormErrors(result);
        }
    } catch (error) {
        console.error(error);
        alert('Terjadi kesalahan saat menyimpan produk');
    } finally {
        if (submitBtn) {
            submitBtn.innerHTML = 'Simpan Produk';
            submitBtn.disabled = false;
        }
    }
}

function displayFormErrors(result) {
    const errorDiv = document.getElementById('formErrors');
    if (!errorDiv) return;

    errorDiv.classList.remove('hidden');
    let errorHtml = '';
    
    if (result.errors) {
        Object.values(result.errors).flat().forEach(error => {
            errorHtml += `<p>${error}</p>`;
        });
    } else {
        errorHtml = `<p>${result.message || 'Terjadi kesalahan'}</p>`;
    }
    errorDiv.innerHTML = errorHtml;
}

function resetFilters() {
    const fields = [
        'searchInput', 
        'kategoriFilter',
        'statusFilter', 
        'stokMin', 
        'stokMax', 
        'penerimaanFilter', 
        'kadaluwarsaFilter'
    ];
    
    fields.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    
    const sortField = document.getElementById('sortField');
    const sortDir = document.getElementById('sortDirection');
    if (sortField) sortField.value = 'id_produk';
    if (sortDir) sortDir.value = 'asc';
    
    loadProducts(1);
}

function renderTable(products) {
    const tbody = document.getElementById('productTable');
    const template = document.getElementById('productRow');
    if (!tbody || !template) return;
    
    tbody.innerHTML = '';
    
    products.forEach(p => {
        const row = template.content.cloneNode(true);
        const stokValue = p.stok?.stok || 0;
        
        const img = row.querySelector('.product-image');
        if (img) {
            img.src = p.image_url ? `/storage/products/${p.image_url}` : '/images/no-image.png';
            img.onerror = function() { this.src = '/images/no-image.png'; };
        }
        
        const updateText = (cls, text) => {
            const el = row.querySelector(cls);
            if (el) el.textContent = text;
        };

        updateText('.id', p.id_produk);
        updateText('.nama', p.nama_produk);
        updateText('.harga', `Rp ${Number(p.harga).toLocaleString('id-ID')}`);
        updateText('.kategori span', p.kategori?.nama_kategori || '-');
        updateText('.stok', stokValue);

        const editBtn = row.querySelector('.edit-btn');
        if (editBtn) {
            editBtn.onclick = () => openEditModal(p.id_produk);
        }

        const viewBtn = row.querySelector('.view-btn');
        if (viewBtn) {
            viewBtn.onclick = () => openDetailModal(p.id_produk);
        }

        const deleteBtn = row.querySelector('.delete-btn');
        if (deleteBtn) {
            deleteBtn.onclick = () => handleDelete(p.id_produk);
        }

        const stokElement = row.querySelector('.stok');
        if (stokElement) {
            stokElement.classList.add(stokValue < 10 ? 'stok-low' : 'stok-normal');
        }

        const statusBadge = row.querySelector('.status-badge');
        if (statusBadge) {
            statusBadge.textContent = p.status ? 'Aktif' : 'Nonaktif';
            statusBadge.classList.add(p.status ? 'status-aktif' : 'status-nonaktif');
        }

        const tglPenerimaan = p.stok?.tgl_penerimaan;
        updateText('.tgl-penerimaan', tglPenerimaan ? new Date(tglPenerimaan).toLocaleDateString('id-ID') : '-');

        const tglKdl = row.querySelector('.tgl-kadaluwarsa');
        if (tglKdl && p.stok?.tgl_kadaluwarsa) {
            const date = new Date(p.stok.tgl_kadaluwarsa);
            const diffDays = Math.ceil((date - new Date()) / (1000 * 60 * 60 * 24));
            tglKdl.textContent = date.toLocaleDateString('id-ID');
            if (diffDays <= 0) tglKdl.classList.add('text-red-600');
            else if (diffDays <= 7) tglKdl.classList.add('text-orange-500');
        }
        
        tbody.appendChild(row);
    });
}

function renderPagination(meta) {
    const container = document.getElementById('pagination');
    const template = document.getElementById('paginationButton');
    if (!container || !template || !meta?.links) return;
    
    container.innerHTML = '';
    meta.links.forEach(link => {
        if (!link.url) return;
        const btn = template.content.cloneNode(true).querySelector('button');
        if (!btn) return;
        
        btn.innerHTML = link.label;
        if (link.active) {
            btn.classList.add('pagination-active');
            btn.disabled = true;
        } else {
            btn.onclick = () => {
                const url = new URL(link.url);
                loadProducts(url.searchParams.get('page'));
            };
        }
        container.appendChild(btn);
    });
}

function resizeImage(file, maxWidth, maxHeight) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = (event) => {
            const img = new Image();
            img.src = event.target.result;
            img.onload = () => {
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;

                if (width > height) {
                    if (width > maxWidth) {
                        height *= maxWidth / width;
                        width = maxWidth;
                    }
                } else {
                    if (height > maxHeight) {
                        width *= maxHeight / height;
                        height = maxHeight;
                    }
                }

                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                canvas.toBlob((blob) => {
                    resolve(blob);
                }, 'image/jpeg', 0.85);
            };
        };
        reader.onerror = (e) => reject(e);
    });
}

async function handleDelete(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus produk ini? Semua data stok terkait juga akan dihapus.')) {
        return;
    }

    try {
        const response = await fetch(`/api/products/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            alert('Produk berhasil dihapus');
            loadProducts(); 
        } else {
            alert('Gagal menghapus: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan koneksi saat menghapus produk');
    }
}

async function openDetailModal(id) {
    try {
        const res = await fetch(`/api/products/${id}`);
        const json = await res.json();

        if (json.success) {
            const p = json.data;
            
            const formatDateFull = (dateString) => {
                if (!dateString) return '-';
                const d = new Date(dateString);
                if (isNaN(d.getTime())) return dateString;
                return d.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
            };

            document.getElementById('detImg').src = p.image_url ? `/storage/products/${p.image_url}` : '/images/no-image.png';
            document.getElementById('detNama').textContent = p.nama_produk;
            document.getElementById('detId').textContent = `ID: ${p.id_produk}`;
            document.getElementById('detHarga').textContent = `Rp ${Number(p.harga).toLocaleString('id-ID')}`;
            document.getElementById('detKategori').textContent = p.kategori?.nama_kategori || '-';
            document.getElementById('detStok').textContent = p.stok?.stok || 0;
            document.getElementById('detStatus').textContent = p.status ? 'Aktif' : 'Nonaktif';
            
            document.getElementById('detTerima').textContent = formatDateFull(p.stok?.tgl_penerimaan);
            document.getElementById('detExp').textContent = formatDateFull(p.stok?.tgl_kadaluwarsa);

            document.getElementById('detailProductModal').classList.remove('hidden');
        }
    } catch (error) {
        alert('Gagal memuat detail produk');
    }
}

function closeDetailModal() {
    document.getElementById('detailProductModal').classList.add('hidden');
}

async function openEditModal(id) {
    currentEditId = id;
    try {
        const res = await fetch(`/api/products/${id}`);
        const json = await res.json();

        if (json.success) {
            const p = json.data;
            
            document.getElementById('edit_nama').value = p.nama_produk;
            document.getElementById('edit_harga').value = p.harga;
            document.getElementById('edit_stok').value = p.stok?.stok || 0;
            
            const catSelect = document.getElementById('edit_kategori');
            if (catSelect) catSelect.value = p.id_kategori;

            const tglTerima = document.getElementById('edit_tgl_penerimaan');
            const tglExp = document.getElementById('edit_tgl_kadaluwarsa');

            if (p.stok?.tgl_penerimaan) {
                tglTerima.value = p.stok.tgl_penerimaan.substring(0, 10);
            } else {
                tglTerima.value = '';
            }
            
            if (p.stok?.tgl_kadaluwarsa) {
                tglExp.value = p.stok.tgl_kadaluwarsa.substring(0, 10);
            } else {
                tglExp.value = '';
            }

            const previewImg = document.getElementById('edit_preview_image');
            if (previewImg) {
                previewImg.src = p.image_url ? `/storage/products/${p.image_url}` : '/images/no-image.png';
            }

            document.getElementById('edit_status').checked = (p.status == 1);
            document.getElementById('editProductModal').classList.remove('hidden');
        }
    } catch (error) {
        alert('Gagal mengambil data produk');
    }
}

async function handleEditProduct(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    
    formData.append('_method', 'PUT');

    const imageInput = document.getElementById('edit_image_input');
    if (imageInput && imageInput.files[0]) {
        formData.append('image', imageInput.files[0]);
    }

    try {
        const response = await fetch(`/api/products/${currentEditId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert('Produk berhasil diperbarui');
            closeEditModal();
            loadProducts();
        } else {
            alert('Gagal: ' + (result.message || 'Terjadi kesalahan'));
        }
    } catch (error) {
        console.error(error);
        alert('Terjadi kesalahan koneksi');
    }
}

function closeEditModal() {
    document.getElementById('editProductModal').classList.add('hidden');
    document.getElementById('editProductForm').reset();
}

const editImageInput = document.getElementById('edit_image_input');
if (editImageInput) {
    editImageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('edit_preview_image').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
}