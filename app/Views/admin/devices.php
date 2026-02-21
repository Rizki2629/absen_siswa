<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_admin') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header with Add Button -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Mesin Fingerprint</h2>
        <p class="text-gray-600 mt-1">Kelola perangkat fingerprint untuk absensi siswa</p>
    </div>
    <button onclick="openAddDeviceModal()" class="btn-primary flex items-center space-x-2">
        <span class="material-symbols">add</span>
        <span>Tambah Mesin</span>
    </button>
</div>

<!-- Alert Messages -->
<?= $this->include('partials/flash_messages') ?>

<!-- Devices Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="devicesContainer">
    <!-- Loading state will be replaced by actual devices -->
    <div class="text-center col-span-3 py-12">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
        <p class="text-gray-500 mt-4">Memuat data mesin...</p>
    </div>
</div>

<!-- Add/Edit Device Modal -->
<div id="deviceModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center rounded-t-2xl">
            <h3 class="text-xl font-bold text-gray-900" id="modalTitle">Tambah Mesin Baru</h3>
            <button onclick="closeDeviceModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols">close</span>
            </button>
        </div>

        <form id="deviceForm" class="p-6 space-y-6">
            <input type="hidden" id="deviceId" name="device_id">

            <!-- Serial Number -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Serial Number (SN) <span class="text-danger-500">*</span>
                </label>
                <input type="text" id="deviceSn" name="sn" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    placeholder="Contoh: DEV001">
                <p class="text-xs text-gray-500 mt-1">Serial number unik untuk identifikasi mesin</p>
            </div>

            <!-- Device Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Mesin <span class="text-danger-500">*</span>
                </label>
                <input type="text" id="deviceName" name="name" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    placeholder="Contoh: Mesin Gerbang Utama">
            </div>

            <!-- IP Address -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    IP Address <span class="text-danger-500">*</span>
                </label>
                <input type="text" id="deviceIp" name="ip_address" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    placeholder="192.168.1.100">
            </div>

            <!-- Port -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Port
                </label>
                <input type="number" id="devicePort" name="port" value="4370"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    placeholder="4370">
                <p class="text-xs text-gray-500 mt-1">Default port: 4370</p>
            </div>

            <!-- Communication Key -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Communication Key
                </label>
                <input type="text" id="deviceCommKey" name="comm_key"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    placeholder="Opsional">
            </div>

            <!-- Location -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Lokasi <span class="text-danger-500">*</span>
                </label>
                <input type="text" id="deviceLocation" name="location" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    placeholder="Contoh: Gerbang Utama">
            </div>

            <!-- Push URL -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Push URL (Webhook)
                </label>
                <input type="url" id="devicePushUrl" name="push_url"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    placeholder="https://example.com/api/attendance">
                <p class="text-xs text-gray-500 mt-1">URL untuk menerima data absensi dari mesin</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button type="button" onclick="closeDeviceModal()" class="btn-secondary">
                    Batal
                </button>
                <button type="submit" class="btn-primary flex items-center space-x-2">
                    <span class="material-symbols text-sm">save</span>
                    <span>Simpan</span>
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
                <span class="material-symbols text-danger-600 text-4xl">delete</span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Mesin?</h3>
            <p class="text-gray-600">Mesin akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
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

<!-- Action Buttons Helper -->
<script src="<?= base_url('assets/js/action-buttons-helper.js') ?>"></script>

<script>
    let deleteDeviceId = null;

    // Load devices on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadDevices();
    });

    // Load all devices
    function loadDevices() {
        fetch('<?= base_url('api/admin/devices') ?>')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('devicesContainer');

                if (data.status === 'success' && data.data.length > 0) {
                    container.innerHTML = data.data.map(device => `
                    <div class="card hover:shadow-xl transition-shadow duration-300">
                        <div class="card-body">
                            <!-- Status Badge -->
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-bold text-gray-900">${device.name}</h3>
                                <span class="badge-${device.status === 'online' ? 'success' : 'danger'} text-xs">
                                    ${device.status === 'online' ? 'Online' : 'Offline'}
                                </span>
                            </div>
                            
                            <!-- Device Info -->
                            <div class="space-y-3 mb-6">
                                <div class="flex items-center text-sm text-gray-600">
                                    <span class="material-symbols text-lg mr-2">tag</span>
                                    <span>SN: ${device.sn}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <span class="material-symbols text-lg mr-2">router</span>
                                    <span>${device.ip_address}:${device.port}</span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <span class="material-symbols text-lg mr-2">location_on</span>
                                    <span>${device.location}</span>
                                </div>
                                ${device.last_seen_at ? `
                                <div class="flex items-center text-sm text-gray-600">
                                    <span class="material-symbols text-lg mr-2">schedule</span>
                                    <span>Terakhir: ${formatDateTime(device.last_seen_at)}</span>
                                </div>
                                ` : ''}
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex space-x-2 pt-4 border-t border-gray-200">
                                <button onclick="testConnection(${device.id}, '${device.ip_address}', ${device.port})" 
                                    title="Test koneksi perangkat"
                                    class="flex-1 btn-secondary text-sm py-2">
                                    <span class="material-symbols text-sm">wifi_tethering</span>
                                    Test
                                </button>
                                <button onclick="editDevice(${device.id})" 
                                    title="Edit perangkat"
                                    class="flex-1 bg-primary-600 text-white hover:bg-primary-700 rounded-lg transition-colors font-medium text-sm py-2">
                                    Edit
                                </button>
                                <button onclick="openDeleteModal(${device.id})" 
                                    title="Hapus perangkat"
                                    class="bg-red-600 text-white hover:bg-red-700 rounded-lg transition-colors font-medium px-3 py-2 text-sm">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
                } else {
                    container.innerHTML = `
                    <div class="col-span-3 text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                            <span class="material-symbols text-gray-400 text-4xl">devices</span>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Mesin</h3>
                        <p class="text-gray-600 mb-6">Tambahkan mesin fingerprint untuk mulai mencatat absensi</p>
                        <button onclick="openAddDeviceModal()" class="btn-primary inline-flex items-center space-x-2">
                            <span class="material-symbols">add</span>
                            <span>Tambah Mesin Pertama</span>
                        </button>
                    </div>
                `;
                }
            })
            .catch(error => {
                console.error('Error loading devices:', error);
                document.getElementById('devicesContainer').innerHTML = `
                <div class="col-span-3 text-center py-12">
                    <span class="material-symbols text-danger-500 text-5xl mb-4">error</span>
                    <p class="text-danger-600">Gagal memuat data mesin</p>
                </div>
            `;
            });
    }

    // Open add device modal
    function openAddDeviceModal() {
        document.getElementById('modalTitle').textContent = 'Tambah Mesin Baru';
        document.getElementById('deviceForm').reset();
        document.getElementById('deviceId').value = '';
        document.getElementById('deviceModal').style.display = 'flex';
    }

    // Close device modal
    function closeDeviceModal() {
        document.getElementById('deviceModal').style.display = 'none';
    }

    // Edit device
    function editDevice(id) {
        fetch(`<?= base_url('api/admin/devices/') ?>${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const device = data.data;
                    document.getElementById('modalTitle').textContent = 'Edit Mesin';
                    document.getElementById('deviceId').value = device.id;
                    document.getElementById('deviceSn').value = device.sn;
                    document.getElementById('deviceName').value = device.name;
                    document.getElementById('deviceIp').value = device.ip_address;
                    document.getElementById('devicePort').value = device.port;
                    document.getElementById('deviceCommKey').value = device.comm_key || '';
                    document.getElementById('deviceLocation').value = device.location;
                    document.getElementById('devicePushUrl').value = device.push_url || '';
                    document.getElementById('deviceModal').style.display = 'flex';
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Submit device form
    document.getElementById('deviceForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const deviceId = document.getElementById('deviceId').value;
        const url = deviceId ?
            `<?= base_url('api/admin/devices/') ?>${deviceId}` :
            '<?= base_url('api/admin/devices') ?>';

        fetch(url, {
                method: deviceId ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(Object.fromEntries(formData))
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    closeDeviceModal();
                    loadDevices();
                    showAlert('success', data.message || 'Mesin berhasil disimpan');
                } else {
                    showAlert('error', data.message || 'Gagal menyimpan mesin');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Terjadi kesalahan saat menyimpan mesin');
            });
    });

    // Open delete modal
    function openDeleteModal(id) {
        deleteDeviceId = id;
        document.getElementById('deleteModal').style.display = 'flex';
    }

    // Close delete modal
    function closeDeleteModal() {
        deleteDeviceId = null;
        document.getElementById('deleteModal').style.display = 'none';
    }

    // Confirm delete
    function confirmDelete() {
        if (!deleteDeviceId) return;

        fetch(`<?= base_url('api/admin/devices/') ?>${deleteDeviceId}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    closeDeleteModal();
                    loadDevices();
                    showAlert('success', 'Mesin berhasil dihapus');
                } else {
                    showAlert('error', data.message || 'Gagal menghapus mesin');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Terjadi kesalahan saat menghapus mesin');
            });
    }

    // Test connection
    function testConnection(id, ip, port) {
        showAlert('info', `Menguji koneksi ke ${ip}:${port}...`);

        fetch(`<?= base_url('api/admin/devices/') ?>${id}/test`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert('success', 'Koneksi berhasil! Mesin dapat diakses.');
                } else {
                    showAlert('error', data.message || 'Koneksi gagal! Periksa IP dan port.');
                }
            })
            .catch(error => {
                showAlert('error', 'Tidak dapat menghubungi mesin');
            });
    }

    // Show alert
    function showAlert(type, message) {
        const colors = {
            success: 'bg-success-50 border-success-500 text-success-800',
            error: 'bg-danger-50 border-danger-500 text-danger-800',
            info: 'bg-primary-50 border-primary-500 text-primary-800'
        };

        const icons = {
            success: 'check_circle',
            error: 'error',
            info: 'info'
        };

        const alert = document.createElement('div');
        alert.className = `${colors[type]} border-l-4 p-4 rounded-lg flex items-center fixed top-4 right-4 z-50 shadow-lg max-w-md`;
        alert.innerHTML = `
        <span class="material-symbols mr-3">${icons[type]}</span>
        <span>${message}</span>
    `;

        document.body.appendChild(alert);

        setTimeout(() => {
            alert.remove();
        }, 5000);
    }

    // Format datetime
    function formatDateTime(datetime) {
        if (!datetime) return '-';
        const date = new Date(datetime);
        return date.toLocaleString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
</script>

<?= $this->endSection() ?>