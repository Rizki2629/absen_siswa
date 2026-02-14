<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<a href="<?= base_url('admin/dashboard') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">dashboard</span>
    <span>Dashboard</span>
</a>
<a href="<?= base_url('admin/devices') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">devices</span>
    <span>Mesin Fingerprint</span>
</a>
<a href="<?= base_url('admin/device-mapping') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">link</span>
    <span>Mapping ID Mesin</span>
</a>
<a href="<?= base_url('admin/attendance-logs') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">description</span>
    <span>Log Absensi</span>
</a>
<a href="<?= base_url('admin/shifts') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">schedule</span>
    <span>Pengaturan Shift</span>
</a>
<a href="<?= base_url('admin/students') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">groups</span>
    <span>Data Siswa</span>
</a>
<a href="<?= base_url('admin/classes') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">class</span>
    <span>Data Kelas</span>
</a>
<a href="<?= base_url('admin/users') ?>" class="sidebar-item-active">
    <span class="material-symbols-outlined mr-3">manage_accounts</span>
    <span>Manajemen User</span>
</a>
<a href="<?= base_url('admin/reports') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">assessment</span>
    <span>Laporan</span>
</a>
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
        <span>Tambah User</span>
    </button>
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
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-primary-600 font-bold"><?= strtoupper(substr($user['name'], 0, 1)) ?></span>
                                        </div>
                                        <span class="font-medium text-gray-900"><?= esc($user['name']) ?></span>
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
    function openAddUserModal() {
        document.getElementById('userModalTitle').textContent = 'Tambah User Baru';
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = '';
        document.getElementById('passwordField').style.display = 'block';
        document.getElementById('userPassword').required = true;
        document.getElementById('userModal').style.display = 'flex';
    }

    function closeUserModal() {
        document.getElementById('userModal').style.display = 'none';
    }

    function editUser(id) {
        document.getElementById('userModalTitle').textContent = 'Edit User';
        document.getElementById('passwordField').style.display = 'none';
        document.getElementById('userPassword').required = false;
        // TODO: Load user data
        alert('Edit user ' + id);
    }

    function resetPassword(id) {
        if (confirm('Reset password user ini?')) {
            alert('Reset password user ' + id);
        }
    }

    function deleteUser(id) {
        if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
            alert('Delete user ' + id);
        }
    }

    document.getElementById('userForm').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Save user');
        closeUserModal();
    });
</script>

<?= $this->endSection() ?>