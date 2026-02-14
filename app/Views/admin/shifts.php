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
<a href="<?= base_url('admin/shifts') ?>" class="sidebar-item-active">
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
<a href="<?= base_url('admin/users') ?>" class="sidebar-item">
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
        <h2 class="text-2xl font-bold text-gray-900">Pengaturan Shift</h2>
        <p class="text-gray-600 mt-1">Kelola jam shift masuk dan pulang</p>
    </div>
    <button onclick="openAddShiftModal()" class="btn-primary flex items-center space-x-2">
        <span class="material-symbols-outlined">add</span>
        <span>Tambah Shift</span>
    </button>
</div>

<!-- Shifts Table -->
<div class="card">
    <div class="card-body">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Nama Shift</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">Jam Masuk</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">Toleransi Telat</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">Jam Pulang</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">Status</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody id="shiftsTable">
                    <?php if (!empty($shifts)): ?>
                        <?php foreach ($shifts as $shift): ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 px-4 font-medium text-gray-900"><?= esc($shift['name']) ?></td>
                            <td class="py-3 px-4 text-center"><?= esc($shift['check_in_time']) ?></td>
                            <td class="py-3 px-4 text-center"><?= esc($shift['late_tolerance']) ?> menit</td>
                            <td class="py-3 px-4 text-center"><?= esc($shift['check_out_time']) ?></td>
                            <td class="py-3 px-4 text-center">
                                <span class="badge-<?= $shift['is_active'] ? 'success' : 'danger' ?>">
                                    <?= $shift['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button onclick="editShift(<?= $shift['id'] ?>)" class="text-primary-600 hover:text-primary-800 mr-2">
                                    <span class="material-symbols-outlined">edit</span>
                                </button>
                                <button onclick="deleteShift(<?= $shift['id'] ?>)" class="text-danger-600 hover:text-danger-800">
                                    <span class="material-symbols-outlined">delete</span>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-12 text-gray-500">
                                <span class="material-symbols-outlined text-5xl text-gray-300 mb-2">schedule</span>
                                <p>Belum ada data shift</p>
                                <button onclick="openAddShiftModal()" class="btn-primary mt-4">Tambah Shift Pertama</button>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Shift Modal -->
<div id="shiftModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full">
        <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-900" id="shiftModalTitle">Tambah Shift Baru</h3>
            <button onclick="closeShiftModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <form id="shiftForm" class="p-6 space-y-4">
            <input type="hidden" id="shiftId" name="shift_id">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Shift *</label>
                <input type="text" id="shiftName" name="name" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500"
                    placeholder="Contoh: Pagi">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jam Masuk *</label>
                    <input type="time" id="checkInTime" name="check_in_time" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jam Pulang *</label>
                    <input type="time" id="checkOutTime" name="check_out_time" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Toleransi Telat (menit)</label>
                <input type="number" id="lateTolerance" name="late_tolerance" value="15" min="0"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" id="isActive" name="is_active" checked
                    class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
                <label for="isActive" class="ml-2 text-sm text-gray-700">Shift Aktif</label>
            </div>
            
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeShiftModal()" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">
                    <span class="material-symbols-outlined mr-2">save</span>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddShiftModal() {
    document.getElementById('shiftModalTitle').textContent = 'Tambah Shift Baru';
    document.getElementById('shiftForm').reset();
    document.getElementById('shiftId').value = '';
    document.getElementById('shiftModal').style.display = 'flex';
}

function closeShiftModal() {
    document.getElementById('shiftModal').style.display = 'none';
}

function editShift(id) {
    // TODO: Implement edit
    alert('Edit shift ' + id);
}

function deleteShift(id) {
    if (confirm('Apakah Anda yakin ingin menghapus shift ini?')) {
        // TODO: Implement delete
        alert('Delete shift ' + id);
    }
}

document.getElementById('shiftForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // TODO: Implement save
    alert('Save shift');
    closeShiftModal();
});
</script>

<?= $this->endSection() ?>
