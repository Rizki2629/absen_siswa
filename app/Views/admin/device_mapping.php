<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_admin') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Mapping ID Mesin</h2>
        <p class="text-gray-600 mt-1">Hubungkan ID fingerprint di mesin dengan data siswa</p>
    </div>
    <button onclick="openAddMappingModal()" class="btn-primary flex items-center space-x-2">
        <span class="material-symbols-outlined">add</span>
        <span>Tambah Mapping</span>
    </button>
</div>

<!-- Alert Messages -->
<?= $this->include('partials/flash_messages') ?>

<!-- Filter Section -->
<div class="card mb-6">
    <div class="card-body">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Mesin</label>
                <select id="filterDevice" onchange="loadMappings()" class="w-full px-4 py-2 border border-gray-300 rounded-xl">
                    <option value="">Semua Mesin</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Kelas</label>
                <select id="filterClass" onchange="loadMappings()" class="w-full px-4 py-2 border border-gray-300 rounded-xl">
                    <option value="">Semua Kelas</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari Siswa</label>
                <input type="text" id="searchStudent" placeholder="Nama atau NIS..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl">
            </div>
        </div>
    </div>
</div>

<!-- Mappings Table -->
<div class="card">
    <div class="card-body">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">ID Mesin</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">NIS</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Nama Siswa</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Kelas</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Mesin</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">Aksi</th>
                    </tr>
                </thead>
                <tbody id="mappingsTable">
                    <tr>
                        <td colspan="6" class="text-center py-12">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                            <p class="text-gray-500 mt-4">Memuat data mapping...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Mapping Modal -->
<div id="mappingModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center rounded-t-2xl">
            <h3 class="text-xl font-bold text-gray-900">Tambah Mapping Baru</h3>
            <button onclick="closeMappingModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form id="mappingForm" class="p-6 space-y-6">
            <!-- Device Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih Mesin <span class="text-danger-500">*</span>
                </label>
                <select id="mappingDevice" name="device_id" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
                    <option value="">-- Pilih Mesin --</option>
                </select>
            </div>

            <!-- Student Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih Siswa <span class="text-danger-500">*</span>
                </label>
                <select id="mappingStudent" name="student_id" required onchange="updateDeviceUserId()"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
                    <option value="">-- Pilih Siswa --</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Pilih siswa yang akan dimapping</p>
            </div>

            <!-- Device User ID -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    ID User di Mesin <span class="text-danger-500">*</span>
                </label>
                <input type="number" id="mappingDeviceUserId" name="device_user_id" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500"
                    placeholder="Contoh: 1">
                <p class="text-xs text-gray-500 mt-1">
                    <strong>Penting:</strong> ID ini harus sama dengan ID yang didaftarkan di mesin fingerprint!<br>
                    Biasanya berdasarkan NIS atau nomor urut siswa.
                </p>
            </div>

            <!-- Privilege Level -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Privilege Level
                </label>
                <select id="mappingPrivilege" name="privilege_level"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
                    <option value="0">User (Siswa)</option>
                    <option value="14">Administrator</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Pilih User untuk siswa biasa</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button type="button" onclick="closeMappingModal()" class="btn-secondary">
                    Batal
                </button>
                <button type="submit" class="btn-primary flex items-center space-x-2">
                    <span class="material-symbols-outlined text-sm">save</span>
                    <span>Simpan Mapping</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
        <div class="text-center mb-6">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-danger-100 mb-4">
                <span class="material-symbols-outlined text-danger-600 text-4xl">delete</span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Mapping?</h3>
            <p class="text-gray-600">Mapping akan dihapus dan siswa tidak akan bisa absen menggunakan mesin ini.</p>
        </div>

        <div class="flex space-x-3">
            <button onclick="closeDeleteModal()" class="flex-1 btn-secondary">
                Batal
            </button>
            <button onclick="confirmDelete()" class="flex-1 bg-danger-600 text-white px-4 py-2 rounded-xl hover:bg-danger-700 font-medium">
                Hapus
            </button>
        </div>
    </div>
</div>

<script>
    let deleteMappingId = null;

    document.addEventListener('DOMContentLoaded', function() {
        loadDevices();
        loadStudents();
        loadClasses();
        loadMappings();

        // Search functionality
        document.getElementById('searchStudent').addEventListener('input', function() {
            loadMappings();
        });
    });

    // Load devices for filter and form
    function loadDevices() {
        fetch('<?= base_url('api/admin/devices') ?>')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const filterSelect = document.getElementById('filterDevice');
                    const formSelect = document.getElementById('mappingDevice');

                    data.data.forEach(device => {
                        filterSelect.innerHTML += `<option value="${device.id}">${device.name} (${device.sn})</option>`;
                        formSelect.innerHTML += `<option value="${device.id}">${device.name} (${device.sn})</option>`;
                    });
                }
            });
    }

    // Load students for form
    function loadStudents() {
        fetch('<?= base_url('api/admin/students') ?>')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const select = document.getElementById('mappingStudent');
                    data.data.forEach(student => {
                        select.innerHTML += `<option value="${student.id}" data-nis="${student.nis}">${student.nis} - ${student.name}</option>`;
                    });
                }
            });
    }

    // Load classes for filter
    function loadClasses() {
        fetch('<?= base_url('api/admin/classes') ?>')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const select = document.getElementById('filterClass');
                    data.data.forEach(cls => {
                        select.innerHTML += `<option value="${cls.id}">${cls.name}</option>`;
                    });
                }
            });
    }

    // Auto-fill device user ID based on NIS
    function updateDeviceUserId() {
        const select = document.getElementById('mappingStudent');
        const selectedOption = select.options[select.selectedIndex];
        const nis = selectedOption.getAttribute('data-nis');

        if (nis) {
            document.getElementById('mappingDeviceUserId').value = nis;
        }
    }

    // Load mappings
    function loadMappings() {
        const deviceFilter = document.getElementById('filterDevice').value;
        const classFilter = document.getElementById('filterClass').value;
        const search = document.getElementById('searchStudent').value;

        let url = '<?= base_url('api/admin/device-mappings') ?>?';
        if (deviceFilter) url += `device_id=${deviceFilter}&`;
        if (classFilter) url += `class_id=${classFilter}&`;
        if (search) url += `search=${search}&`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('mappingsTable');

                if (data.status === 'success' && data.data.length > 0) {
                    tbody.innerHTML = data.data.map(mapping => `
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="py-3 px-4">
                            <span class="font-mono text-primary-600 font-semibold">${mapping.device_user_id}</span>
                        </td>
                        <td class="py-3 px-4">${mapping.student_nis}</td>
                        <td class="py-3 px-4">
                            <div class="font-medium text-gray-900">${mapping.student_name}</div>
                        </td>
                        <td class="py-3 px-4">${mapping.class_name || '-'}</td>
                        <td class="py-3 px-4">
                            <div class="text-sm text-gray-600">${mapping.device_name}</div>
                            <div class="text-xs text-gray-400">${mapping.device_sn}</div>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <button onclick="openDeleteModal(${mapping.id})" 
                                class="text-danger-600 hover:bg-danger-50 p-2 rounded-lg">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        </td>
                    </tr>
                `).join('');
                } else {
                    tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-12">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                <span class="material-symbols-outlined text-gray-400 text-4xl">link_off</span>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Mapping</h3>
                            <p class="text-gray-600 mb-6">Tambahkan mapping untuk menghubungkan siswa dengan mesin fingerprint</p>
                            <button onclick="openAddMappingModal()" class="btn-primary inline-flex items-center space-x-2">
                                <span class="material-symbols-outlined">add</span>
                                <span>Tambah Mapping Pertama</span>
                            </button>
                        </td>
                    </tr>
                `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    // Open add mapping modal
    function openAddMappingModal() {
        document.getElementById('mappingForm').reset();
        const modal = document.getElementById('mappingModal');
        modal.style.display = 'flex';
    }

    // Close mapping modal
    function closeMappingModal() {
        const modal = document.getElementById('mappingModal');
        modal.style.display = 'none';
    }

    // Submit mapping form
    document.getElementById('mappingForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('<?= base_url('api/admin/device-mappings') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(Object.fromEntries(formData))
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    closeMappingModal();
                    loadMappings();
                    showAlert('success', data.message || 'Mapping berhasil ditambahkan');
                } else {
                    showAlert('error', data.message || 'Gagal menambahkan mapping');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Terjadi kesalahan saat menyimpan mapping');
            });
    });

    // Open delete modal
    function openDeleteModal(id) {
        deleteMappingId = id;
        const modal = document.getElementById('deleteModal');
        modal.style.display = 'flex';
    }

    // Close delete modal
    function closeDeleteModal() {
        deleteMappingId = null;
        const modal = document.getElementById('deleteModal');
        modal.style.display = 'none';
    }

    // Confirm delete
    function confirmDelete() {
        if (!deleteMappingId) return;

        fetch(`<?= base_url('api/admin/device-mappings/') ?>${deleteMappingId}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    closeDeleteModal();
                    loadMappings();
                    showAlert('success', 'Mapping berhasil dihapus');
                } else {
                    showAlert('error', data.message || 'Gagal menghapus mapping');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Terjadi kesalahan saat menghapus mapping');
            });
    }

    // Show alert
    function showAlert(type, message) {
        const colors = {
            success: 'bg-success-50 border-success-500 text-success-800',
            error: 'bg-danger-50 border-danger-500 text-danger-800'
        };

        const icons = {
            success: 'check_circle',
            error: 'error'
        };

        const alert = document.createElement('div');
        alert.className = `${colors[type]} border-l-4 p-4 rounded-lg flex items-center fixed top-4 right-4 z-50 shadow-lg max-w-md`;
        alert.innerHTML = `
        <span class="material-symbols-outlined mr-3">${icons[type]}</span>
        <span>${message}</span>
    `;

        document.body.appendChild(alert);

        setTimeout(() => {
            alert.remove();
        }, 5000);
    }
</script>

<?= $this->endSection() ?>