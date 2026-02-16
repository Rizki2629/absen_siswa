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
            <button onclick="switchTab('parent')" id="tab-parent" class="tab-button text-gray-500 hover:text-purple-600 py-3 px-4 text-sm font-medium border-0">
                <div class="flex items-center space-x-2">
                    <span class="material-symbols-outlined text-base">school</span>
                    <span>Siswa</span>
                    <span id="count-parent" class="bg-gray-100 text-gray-600 ml-2 py-0.5 px-2 rounded-full text-xs font-semibold">0</span>
                </div>
            </button>
        </nav>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-body">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Nama</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Username</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Email</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">Role</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">Status</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody id="usersTable">
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr class="border-b border-gray-100 hover:bg-gray-50" data-role="<?= esc($user['role']) ?>">
                                <td class="py-3 px-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-primary-600 font-bold"><?= strtoupper(substr($user['full_name'] ?? $user['username'], 0, 1)) ?></span>
                                        </div>
                                        <span class="font-medium text-gray-900"><?= esc($user['full_name'] ?? $user['username']) ?></span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-gray-600"><?= esc($user['username']) ?></td>
                                <td class="py-3 px-4 text-gray-600"><?= esc($user['email'] ?? '-') ?></td>
                                <td class="py-3 px-4 text-center">
                                    <span class="badge-<?= $user['role'] === 'admin' ? 'primary' : 'secondary' ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="badge-<?= $user['is_active'] ? 'success' : 'danger' ?>">
                                        <?= $user['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <button onclick="editUser(<?= $user['id'] ?>)" class="text-primary-600 hover:text-primary-800 mr-2">
                                        <span class="material-symbols-outlined">edit</span>
                                    </button>
                                    <button onclick="resetPassword(<?= $user['id'] ?>)" class="text-warning-600 hover:text-warning-800 mr-2">
                                        <span class="material-symbols-outlined">key</span>
                                    </button>
                                    <?php if ($user['id'] != session()->get('user_id')): ?>
                                        <button onclick="deleteUser(<?= $user['id'] ?>)" class="text-danger-600 hover:text-danger-800">
                                            <span class="material-symbols-outlined">delete</span>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-12 text-gray-500">
                                <span class="material-symbols-outlined text-5xl text-gray-300 mb-2">manage_accounts</span>
                                <p>Belum ada data user</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
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
    const allUsers = <?= json_encode($users ?? []) ?>;

    function switchTab(role) {
        currentTab = role;

        // Update tab styling
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('active', 'text-purple-600');
            btn.classList.add('text-gray-500');
            // Reset count badge styling
            const countSpan = btn.querySelector('span[id^="count-"]');
            if (countSpan) {
                countSpan.classList.remove('bg-purple-100', 'text-purple-600');
                countSpan.classList.add('bg-gray-100', 'text-gray-600');
            }
        });

        const activeTab = document.getElementById(`tab-${role}`);
        activeTab.classList.remove('text-gray-500');
        activeTab.classList.add('active', 'text-purple-600');
        // Update active count badge styling
        const activeCount = activeTab.querySelector('span[id^="count-"]');
        if (activeCount) {
            activeCount.classList.remove('bg-gray-100', 'text-gray-600');
            activeCount.classList.add('bg-purple-100', 'text-purple-600');
        }

        // Filter table rows
        const rows = document.querySelectorAll('#usersTable tr[data-role]');
        rows.forEach(row => {
            if (row.dataset.role === role) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Update button text
        const buttonTexts = {
            'admin': 'Tambah Admin',
            'teacher': 'Tambah Guru',
            'parent': 'Tambah Siswa'
        };
        document.getElementById('addUserButtonText').textContent = buttonTexts[role];
    }

    function updateCounts() {
        const counts = {
            admin: 0,
            teacher: 0,
            parent: 0
        };

        allUsers.forEach(user => {
            if (counts.hasOwnProperty(user.role)) {
                counts[user.role]++;
            }
        });

        document.getElementById('count-admin').textContent = counts.admin;
        document.getElementById('count-teacher').textContent = counts.teacher;
        document.getElementById('count-parent').textContent = counts.parent;
    }

    function openAddUserModal() {
        const titles = {
            'admin': 'Tambah Admin Baru',
            'teacher': 'Tambah Guru Baru',
            'parent': 'Tambah Siswa Baru'
        };
        document.getElementById('userModalTitle').textContent = titles[currentTab] || 'Tambah User Baru';
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = '';
        document.getElementById('passwordField').style.display = 'block';
        document.getElementById('userPassword').required = true;
        document.getElementById('userRole').value = currentTab;
        document.getElementById('userModal').style.display = 'flex';
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateCounts();
        switchTab('admin');
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
                location.reload();
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
                location.reload();
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