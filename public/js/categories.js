document.addEventListener('DOMContentLoaded', () => {
    initHamburgerMenu();
    loadCategories();
    initEventListeners();
});

let currentPage = 1;
let lastPage = 1;

function initHamburgerMenu() {
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const closeSidebar = document.getElementById('closeSidebar');

    if (!hamburgerBtn || !sidebar || !overlay || !closeSidebar) return;

    hamburgerBtn.addEventListener('click', () => {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    });

    [closeSidebar, overlay].forEach(el => {
        el.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
    });
}

function initEventListeners() {
    document.getElementById('addCategoryBtn')?.addEventListener('click', openAddModal);
    document.getElementById('categoryForm')?.addEventListener('submit', handleSubmit);
    document.getElementById('applyFilter')?.addEventListener('click', () => {
        currentPage = 1;
        loadCategories();
    });
    document.getElementById('resetFilter')?.addEventListener('click', () => {
        document.getElementById('searchInput').value = '';
        document.getElementById('sortField').value = 'id_kategori';
        document.getElementById('sortDirection').value = 'asc';
        currentPage = 1;
        loadCategories();
    });

    document.getElementById('categoryModal')?.addEventListener('click', (e) => {
        if (e.target.id === 'categoryModal') closeModal();
    });
}

async function loadCategories() {
    hideError();
    // Kosongkan tabel sebelum memuat data baru untuk menunjukkan state loading dengan benar
    const categoryTable = document.getElementById('categoryTable');
    if (categoryTable) categoryTable.innerHTML = '';

    const search = document.getElementById('searchInput').value;
    const sortField = document.getElementById('sortField').value;
    const sortDir = document.getElementById('sortDirection').value;

    const params = new URLSearchParams({
        page: currentPage,
        search: search,
        sort: sortField,
        direction: sortDir
    });

    try {
        const res = await fetch(`/api/categories?${params.toString()}`);
        const json = await res.json();
        
        if (json.success) {
            renderTable(json.data);
            renderPagination(json.meta);
        } else {
            showError('Gagal memuat data');
        }
    } catch (error) {
        showError('Terjadi kesalahan koneksi');
    }
}

function renderTable(categories) {
    const tbody = document.getElementById('categoryTable');
    if (!tbody) return;
    tbody.innerHTML = '';

    if (!categories || categories.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center py-10 text-gray-500">Tidak ada kategori ditemukan</td></tr>';
        return;
    }

    categories.forEach(cat => {
        const row = document.createElement('tr');
        row.className = 'border-b hover:bg-gray-50 transition';
        row.innerHTML = `
            <td class="py-4 px-2 font-medium text-gray-900">${cat.id_kategori}</td>
            <td class="py-4 px-2 text-gray-700">${cat.nama_kategori}</td>
            <td class="py-4 px-2 text-right">
                <div class="flex justify-end gap-2">
                    <button onclick="openEditModal('${cat.id_kategori}', '${cat.nama_kategori}')" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button onclick="handleCategoryDelete('${cat.id_kategori}', '${cat.nama_kategori}')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function renderPagination(meta) {
    const container = document.getElementById('pagination');
    if (!container || !meta) return;
    container.innerHTML = '';

    const createBtn = (label, page, active = false, disabled = false) => {
        const btn = document.createElement('button');
        btn.textContent = label;
        btn.className = `px-3 py-1 border rounded transition ${active ? 'pagination-active' : 'bg-white hover:bg-gray-50'}`;
        if (disabled) btn.disabled = true;
        btn.onclick = () => {
            currentPage = page;
            loadCategories();
        };
        return btn;
    };

    if (meta.last_page > 1) {
        for (let i = 1; i <= meta.last_page; i++) {
            container.appendChild(createBtn(i, i, i === meta.current_page));
        }
    }
}

function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Kategori';
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryName').value = '';
    document.getElementById('categoryModal').classList.remove('hidden');
}

function openEditModal(id, name) {
    document.getElementById('modalTitle').textContent = 'Edit Kategori';
    document.getElementById('categoryId').value = id;
    document.getElementById('categoryName').value = name;
    document.getElementById('categoryModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('categoryModal').classList.add('hidden');
}

async function handleSubmit(e) {
    e.preventDefault();
    const id = document.getElementById('categoryId').value;
    const name = document.getElementById('categoryName').value.trim();
    
    if (!name) {
        alert('Nama Kategori wajib diisi.');
        return;
    }
    
    const isEdit = !!id;
    const url = isEdit ? `/api/categories/${id}` : '/api/categories';
    const method = isEdit ? 'PUT' : 'POST';

    let payload = { nama_kategori: name };

    try {
        const res = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const json = await res.json();
        if (json.success) {
            alert(isEdit ? 'Kategori berhasil diperbarui' : 'Kategori berhasil ditambahkan');
            closeModal();
            loadCategories();
        } else {
            let errorMessage = json.message || 'Terjadi kesalahan yang tidak diketahui.';
            if (json.errors) {
                errorMessage = Object.values(json.errors).flat().join('\n');
            }
            alert(`Gagal: ${errorMessage}`);
        }
    } catch (error) {
        console.error(error);
        alert('Terjadi kesalahan koneksi saat menyimpan kategori.');
    }
}

async function handleCategoryDelete(id, name) {
    if (!confirm(`Apakah Anda yakin ingin menghapus kategori "${name}"?`)) {
        return;
    }

    try {
        const res = await fetch(`/api/categories/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json'
            }
        });
        const json = await res.json();
        if (json.success) {
            alert('Kategori berhasil dihapus');
            loadCategories();
        } else {
            // Menampilkan pesan error dari server, misal: "Kategori tidak dapat dihapus karena masih digunakan"
            alert(`Gagal menghapus: ${json.message || 'Terjadi kesalahan.'}`);
        }
    } catch (error) {
        console.error(error);
        alert('Terjadi kesalahan koneksi saat menghapus kategori.');
    }
}

function showError(message) {
    const errorDiv = document.getElementById('error');
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.classList.remove('hidden');
    }
}

function hideError() {
    const errorDiv = document.getElementById('error');
    if (errorDiv) errorDiv.classList.add('hidden');
}