<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_admin') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Manajemen User</h2>
        <p class="text-gray-600 mt-1">Kelola akun pengguna sistem</p>
    </div>
    <button onclick="openAddUserModal()" class="btn-primary flex items-center space-x-2">
        <span class="material-symbols-outlined">add</span>
        <span id="addUserButtonText">Tambah User</span>
    </button>
</div>

<!-- Tabs -->
<div class="mb-6">
    <div>
        <nav class="flex space-x-1">
            <button onclick="switchTab('admin')" id="tab-admin" class="tab-button active text-purple-600 py-3 px-4 text-sm font-medium border-0">
                <div class="flex items-center space-x-2">
                    <span class="material-symbols-outlined text-base">shield_person</span>
                    <span>Admin</span>
                    <span id="count-admin" class="bg-purple-100 text-purple-600 ml-2 py-0.5 px-2 rounded-full text-xs font-semibold">0</span>
                </div>
            </button>
            <button onclick="switchTab('teacher')" id="tab-teacher" class="tab-button text-gray-500 hover:text-purple-600 py-3 px-4 text-sm font-medium border-0">
                <div class="flex items-center space-x-2">
                    <span class="material-symbols-outlined text-base">person</span>
                    <span>Guru</span>
                    <span id="count-teacher" class="bg-gray-100 text-gray-600 ml-2 py-0.5 px-2 rounded-full text-xs font-semibold">0</span>
                </div>
            </button>
            <button onclick="switchTab('siswa')" id="tab-siswa" class="tab-button text-gray-500 hover:text-purple-600 py-3 px-4 text-sm font-medium border-0">
                <div class="flex items-center space-x-2">
                    <span class="material-symbols-outlined text-base">school</span>
                    <span>Siswa</span>
                    <span id="count-siswa" class="bg-gray-100 text-gray-600 ml-2 py-0.5 px-2 rounded-full text-xs font-semibold">0</span>
                </div>
            </button>
        </nav>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-body">
        <div id="studentSearchWrapper" class="mb-4" style="display: none;">
            <div class="relative max-w-md">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                <input
                    type="text"
                    id="studentSearchInput"
                    placeholder="Cari siswa (nama, username, email)..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-primary-50">
                    <tr class="border-b border-primary-100">
                        <th class="text-left py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap">No</th>
                        <th onclick="setUserSort('name')" class="text-left py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap cursor-pointer select-none hover:bg-primary-100 transition-colors">Nama<span id="userSortIndicator_name" class="ml-1 text-primary-500"></span></th>
                        <th onclick="setUserSort('username')" class="text-left py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap cursor-pointer select-none hover:bg-primary-100 transition-colors">Username<span id="userSortIndicator_username" class="ml-1 text-primary-500"></span></th>
                        <th onclick="setUserSort('email')" class="text-left py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap cursor-pointer select-none hover:bg-primary-100 transition-colors">Email<span id="userSortIndicator_email" class="ml-1 text-primary-500"></span></th>
                        <th onclick="setUserSort('role')" class="text-center py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap cursor-pointer select-none hover:bg-primary-100 transition-colors">Role<span id="userSortIndicator_role" class="ml-1 text-primary-500"></span></th>
                        <th onclick="setUserSort('is_active')" class="text-center py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap cursor-pointer select-none hover:bg-primary-100 transition-colors">Status<span id="userSortIndicator_is_active" class="ml-1 text-primary-500"></span></th>
                        <th class="text-center py-3.5 px-4 text-xs font-bold uppercase tracking-wide text-primary-700 whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody id="usersTable"></tbody>
            </table>
        </div>

        <?= view('partials/pagination_soft', [
            'ariaLabel' => 'Users pagination',
            'infoId' => 'paginationInfo',
            'numbersId' => 'usersPaginationNumbers',
            'prevId' => 'prevPageBtn',
            'nextId' => 'nextPageBtn',
            'prevHandler' => 'goToPage(currentPage - 1)',
            'nextHandler' => 'goToPage(currentPage + 1)',
            'infoText' => 'Memuat data...',
            'containerClass' => 'mt-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3',
        ]) ?>
    </div>
</div>

<!-- Add/Edit User Modal -->
<div id="userModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full">
        <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-900" id="userModalTitle">Tambah User Baru</h3>
            <button onclick="closeUserModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form id="userForm" class="p-6 space-y-4">
            <input type="hidden" id="userId" name="user_id">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                <input type="text" id="userName" name="name" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500"
                    placeholder="Nama lengkap">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
                <input type="text" id="userUsername" name="username" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500"
                    placeholder="Username untuk login">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email" id="userEmail" name="email"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500"
                    placeholder="email@contoh.com">
            </div>

            <div id="passwordField">
                <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                <input type="password" id="userPassword" name="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500"
                    placeholder="Minimal 6 karakter">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                <select id="userRole" name="role" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
                    <option value="admin">Admin</option>
                    <option value="teacher">Guru Piket</option>
                    <option value="parent">Orang Tua</option>
                </select>
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="userActive" name="is_active" checked
                    class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
                <label for="userActive" class="ml-2 text-sm text-gray-700">User Aktif</label>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeUserModal()" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">
                    <span class="material-symbols-outlined mr-2">save</span>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentTab = 'admin';
    let currentPage = 1;
    let totalPages = 1;
    let currentSearch = '';
    let searchDebounceTimer = null;
    const currentUserId = <?= (int) session()->get('user_id') ?>;
    let userSortCol = '';
    let userSortDir = '';
    let cachedUsersForSort = [];

    function getPaginationElement(preferredId, fallbackId) {
        return document.getElementById(preferredId) || document.getElementById(fallbackId);
    }

    function switchTab(role) {
        currentTab = role;
        currentPage = 1;
        if (currentTab !== 'siswa') {
            currentSearch = '';
            const searchInput = document.getElementById('studentSearchInput');
            if (searchInput) {
                searchInput.value = '';
            }
        }

        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('active', 'text-purple-600');
            btn.classList.add('text-gray-500');
            const countSpan = btn.querySelector('span[id^="count-"]');
            if (countSpan) {
                countSpan.classList.remove('bg-purple-100', 'text-purple-600');
                countSpan.classList.add('bg-gray-100', 'text-gray-600');
            }
        });

        const activeTab = document.getElementById(`tab-${role}`);
        activeTab.classList.remove('text-gray-500');
        activeTab.classList.add('active', 'text-purple-600');
        const activeCount = activeTab.querySelector('span[id^="count-"]');
        if (activeCount) {
            activeCount.classList.remove('bg-gray-100', 'text-gray-600');
            activeCount.classList.add('bg-purple-100', 'text-purple-600');
        }

        document.getElementById('studentSearchWrapper').style.display = role === 'siswa' ? 'block' : 'none';

        const buttonTexts = {
            'admin': 'Tambah Admin',
            'teacher': 'Tambah Guru',
            'siswa': 'Tambah Siswa'
        };
        document.getElementById('addUserButtonText').textContent = buttonTexts[role];

        loadUsers();
    }

    function getRoleParam() {
        if (currentTab === 'teacher') return 'teacher';
        if (currentTab === 'siswa') return 'siswa';
        return 'admin';
    }

    function updateCounts(counts) {
        document.getElementById('count-admin').textContent = counts?.admin ?? 0;
        document.getElementById('count-teacher').textContent = counts?.teacher ?? 0;
        document.getElementById('count-siswa').textContent = counts?.siswa ?? 0;
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getRoleLabel(role) {
        const normalizedRole = String(role || '').toLowerCase();
        if (normalizedRole === 'guru_piket' || normalizedRole === 'teacher') return 'Guru';
        if (normalizedRole === 'siswa' || normalizedRole === 'student') return 'Siswa';
        if (normalizedRole === 'admin') return 'Admin';
        return normalizedRole || '-';
    }

    function getRoleBadge(role) {
        return String(role || '').toLowerCase() === 'admin' ? 'badge-primary' : 'badge-secondary';
    }

    function renderUsers(users) {
        cachedUsersForSort = Array.isArray(users) ? [...users] : [];
        renderSortedUsers();
    }

    function renderSortedUsers() {
        const tableBody = document.getElementById('usersTable');
        let users = [...cachedUsersForSort];

        if (userSortCol) {
            users.sort((a, b) => {
                let va, vb;
                if (userSortCol === 'name') {
                    va = String(a.full_name || a.username || '').toLowerCase();
                    vb = String(b.full_name || b.username || '').toLowerCase();
                } else if (userSortCol === 'is_active') {
                    return userSortDir === 'asc' ? Number(a.is_active) - Number(b.is_active) : Number(b.is_active) - Number(a.is_active);
                } else {
                    va = String(a[userSortCol] || '').toLowerCase();
                    vb = String(b[userSortCol] || '').toLowerCase();
                }
                if (va < vb) return userSortDir === 'asc' ? -1 : 1;
                if (va > vb) return userSortDir === 'asc' ? 1 : -1;
                return 0;
            });
        }

        if (!Array.isArray(users) || users.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-12 text-gray-500">
                        <span class="material-symbols-outlined text-5xl text-gray-300 mb-2">manage_accounts</span>
                        <p>Belum ada data user</p>
                    </td>
                </tr>
            `;
            return;
        }

        tableBody.innerHTML = users.map((user, index) => {
            const displayName = user.full_name || user.username || '-';
            const initial = String(displayName).charAt(0).toUpperCase();
            const canDelete = Number(user.id) !== currentUserId;
            const rowNumber = ((currentPage - 1) * 25) + index + 1;

            return `
                <tr class="bg-white border-b border-gray-100 hover:bg-gray-50">
                    <td class="py-3 px-4 text-gray-500 font-medium">${rowNumber}</td>
                    <td class="py-3 px-4 font-medium text-gray-900">${escapeHtml(displayName)}</td>
                    <td class="py-3 px-4 text-gray-600">${escapeHtml(user.username)}</td>
                    <td class="py-3 px-4 text-gray-600">${escapeHtml(user.email || '-')}</td>
                    <td class="py-3 px-4 text-center">
                        <span class="${getRoleBadge(user.role)}">${escapeHtml(getRoleLabel(user.role))}</span>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <span class="${Number(user.is_active) === 1 ? 'badge-success' : 'badge-danger'}">${Number(user.is_active) === 1 ? 'Aktif' : 'Nonaktif'}</span>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <button onclick="editUser(${Number(user.id)})" class="text-primary-600 hover:text-primary-800 mr-2 p-1 rounded focus:outline-none" style="border:none;background:none;box-shadow:none;">
                            <span class="material-symbols-outlined">edit</span>
                        </button>
                        <button onclick="resetPassword(${Number(user.id)})" class="text-warning-600 hover:text-warning-800 mr-2 p-1 rounded focus:outline-none" style="border:none;background:none;box-shadow:none;">
                            <span class="material-symbols-outlined">key</span>
                        </button>
                        ${canDelete ? `
                            <button onclick="deleteUser(${Number(user.id)})" class="text-danger-600 hover:text-danger-800 p-1 rounded focus:outline-none" style="border:none;background:none;box-shadow:none;">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        ` : ''}
                    </td>
                </tr>
            `;
        }).join('');
    }

    function setUserSort(col) {
        if (userSortCol === col) {
            if (userSortDir === 'asc') userSortDir = 'desc';
            else if (userSortDir === 'desc') {
                userSortCol = '';
                userSortDir = '';
            } else userSortDir = 'asc';
        } else {
            userSortCol = col;
            userSortDir = 'asc';
        }
        updateUserSortIndicators();
        renderSortedUsers();
    }

    function updateUserSortIndicators() {
        ['name', 'username', 'email', 'role', 'is_active'].forEach(col => {
            const el = document.getElementById('userSortIndicator_' + col);
            if (!el) return;
            if (userSortCol === col) {
                el.textContent = userSortDir === 'asc' ? '↑' : '↓';
            } else {
                el.textContent = '';
            }
        });
    }

    function updatePagination(meta) {
        currentPage = Number(meta?.page || 1);
        totalPages = Number(meta?.total_pages || 1);
        const total = Number(meta?.total || 0);
        const perPage = Number(meta?.per_page || 25);
        const start = total === 0 ? 0 : ((currentPage - 1) * perPage) + 1;
        const end = Math.min(currentPage * perPage, total);

        const infoEl = getPaginationElement('paginationInfo', 'studentsPaginationInfo');
        const prevEl = getPaginationElement('prevPageBtn', 'studentsPrevBtn');
        const nextEl = getPaginationElement('nextPageBtn', 'studentsNextBtn');

        if (infoEl) {
            infoEl.textContent = `Menampilkan ${start}-${end} dari ${total} user`;
        }
        if (prevEl) {
            prevEl.disabled = currentPage <= 1;
        }
        if (nextEl) {
            nextEl.disabled = currentPage >= totalPages;
        }

        renderUsersPaginationNumbers();
    }

    function renderUsersPaginationNumbers() {
        const container = getPaginationElement('usersPaginationNumbers', 'paginationNumbers');
        if (!container) {
            return;
        }
        const pages = [];

        if (totalPages <= 7) {
            for (let i = 1; i <= totalPages; i++) {
                pages.push(i);
            }
        } else {
            pages.push(1);
            if (currentPage > 3) {
                pages.push('ellipsis-start');
            }

            const start = Math.max(2, currentPage - 1);
            const end = Math.min(totalPages - 1, currentPage + 1);
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }

            if (currentPage < totalPages - 2) {
                pages.push('ellipsis-end');
            }
            pages.push(totalPages);
        }

        container.innerHTML = pages.map(item => {
            if (typeof item !== 'number') {
                const jumpPage = item === 'ellipsis-start' ? Math.max(1, currentPage - 5) : Math.min(totalPages, currentPage + 5);
                const tooltip = item === 'ellipsis-start' ? '5 halaman sebelumnya' : '5 halaman berikutnya';
                return `
                    <button type="button" onclick="goToPage(${jumpPage})" title="${tooltip}"
                        class="w-10 h-10 rounded-xl border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 flex items-center justify-center">
                        <span class="material-symbols-outlined text-base">more_horiz</span>
                    </button>
                `;
            }

            const isCurrent = item === currentPage;
            const activeClass = isCurrent ?
                'bg-primary-600 text-white border-primary-600' :
                'bg-white text-gray-700 border-gray-200 hover:bg-gray-50';
            const ariaCurrent = isCurrent ? 'aria-current="page"' : '';

            return `
                <button type="button" onclick="goToPage(${item})"
                    ${ariaCurrent}
                    class="w-10 h-10 rounded-xl border text-sm font-semibold transition ${activeClass}">
                    ${item}
                </button>
            `;
        }).join('');
    }

    function goToPage(page) {
        if (page < 1 || page > totalPages || page === currentPage) {
            return;
        }
        currentPage = page;
        loadUsers();
    }

    async function loadUsers() {
        try {
            const params = new URLSearchParams({
                role: getRoleParam(),
                page: String(currentPage),
                per_page: '25'
            });

            if (currentTab === 'siswa' && currentSearch.trim() !== '') {
                params.set('search', currentSearch.trim());
            }

            const response = await fetch(`<?= base_url('api/admin/users') ?>?${params.toString()}`);
            const result = await response.json();

            if (result.status === 'success') {
                renderUsers(result.data || []);
                updatePagination(result.meta || {});
                updateCounts((result.meta || {}).counts || {});
                return;
            }

            alert('Gagal memuat data user: ' + (result.message || 'Unknown error'));
            renderUsers([]);
            updatePagination({
                page: 1,
                total_pages: 1,
                total: 0,
                per_page: 25
            });
        } catch (error) {
            console.error('Error:', error);
            alert('Gagal memuat data user');
            renderUsers([]);
            updatePagination({
                page: 1,
                total_pages: 1,
                total: 0,
                per_page: 25
            });
        }
    }

    function openAddUserModal() {
        const titles = {
            'admin': 'Tambah Admin Baru',
            'teacher': 'Tambah Guru Baru',
            'siswa': 'Tambah Siswa Baru'
        };
        document.getElementById('userModalTitle').textContent = titles[currentTab] || 'Tambah User Baru';
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = '';
        document.getElementById('passwordField').style.display = 'block';
        document.getElementById('userPassword').required = true;
        const roleForForm = currentTab === 'siswa' ? 'siswa' : currentTab;
        document.getElementById('userRole').value = roleForForm;
        document.getElementById('userModal').style.display = 'flex';
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        switchTab('admin');

        const searchInput = document.getElementById('studentSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(() => {
                    currentSearch = searchInput.value || '';
                    currentPage = 1;
                    if (currentTab === 'siswa') {
                        loadUsers();
                    }
                }, 350);
            });
        }
    });

    function closeUserModal() {
        document.getElementById('userModal').style.display = 'none';
    }

    async function editUser(id) {
        try {
            const response = await fetch(`<?= base_url('api/admin/users') ?>/${id}`);
            const result = await response.json();

            if (result.status === 'success') {
                document.getElementById('userModalTitle').textContent = 'Edit User';
                document.getElementById('userId').value = result.data.id;
                document.getElementById('userName').value = result.data.full_name || result.data.name || '';
                document.getElementById('userUsername').value = result.data.username;
                document.getElementById('userEmail').value = result.data.email || '';
                document.getElementById('userRole').value = result.data.role;
                document.getElementById('userActive').checked = result.data.is_active == 1;
                document.getElementById('passwordField').style.display = 'none';
                document.getElementById('userPassword').required = false;
                document.getElementById('userModal').style.display = 'flex';
            } else {
                alert('Gagal memuat data user: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Gagal memuat data user');
        }
    }

    function resetPassword(id) {
        if (confirm('Reset password user ini ke password default?')) {
            // TODO: Implement reset password endpoint
            alert('Fitur reset password akan segera tersedia');
        }
    }

    async function deleteUser(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus user ini?')) {
            return;
        }

        try {
            const response = await fetch(`<?= base_url('api/admin/users') ?>/${id}`, {
                method: 'DELETE'
            });
            const result = await response.json();

            if (result.status === 'success') {
                alert('User berhasil dihapus');
                loadUsers();
            } else {
                alert('Gagal menghapus user: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Gagal menghapus user');
        }
    }

    document.getElementById('userForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const userId = document.getElementById('userId').value;
        const formData = {
            name: document.getElementById('userName').value,
            username: document.getElementById('userUsername').value,
            email: document.getElementById('userEmail').value,
            role: document.getElementById('userRole').value,
            is_active: document.getElementById('userActive').checked ? 1 : 0
        };

        // Only include password when adding new user or when password field is visible
        if (!userId || document.getElementById('passwordField').style.display !== 'none') {
            const password = document.getElementById('userPassword').value;
            if (password) {
                formData.password = password;
            }
        }

        try {
            let url = '<?= base_url('api/admin/users') ?>';
            let method = 'POST';

            if (userId) {
                url += '/' + userId;
                method = 'PUT';
            }

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (result.status === 'success') {
                alert(userId ? 'User berhasil diupdate' : 'User berhasil ditambahkan');
                closeUserModal();
                currentPage = 1;
                loadUsers();
            } else {
                alert('Gagal menyimpan user: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Gagal menyimpan user');
        }
    });
</script>

<?= $this->endSection() ?>