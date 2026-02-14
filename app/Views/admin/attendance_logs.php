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
<a href="<?= base_url('admin/attendance-logs') ?>" class="sidebar-item-active">
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
        <h2 class="text-2xl font-bold text-gray-900">Log Absensi Real-Time</h2>
        <p class="text-gray-600 mt-1">Monitor data absensi yang masuk dari mesin fingerprint</p>
    </div>
    <div class="flex space-x-3">
        <button onclick="loadLogs()" class="btn-secondary flex items-center space-x-2">
            <span class="material-symbols-outlined">refresh</span>
            <span>Refresh</span>
        </button>
        <button onclick="toggleAutoRefresh()" id="autoRefreshBtn" class="btn-primary flex items-center space-x-2">
            <span class="material-symbols-outlined">play_arrow</span>
            <span>Auto Refresh</span>
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="card">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Total Log Hari Ini</p>
                    <h3 class="text-2xl font-bold text-gray-900" id="totalLogs">0</h3>
                </div>
                <div class="bg-primary-100 rounded-full p-3">
                    <span class="material-symbols-outlined text-primary-600 text-3xl">description</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Check In</p>
                    <h3 class="text-2xl font-bold text-success-600" id="checkInCount">0</h3>
                </div>
                <div class="bg-success-100 rounded-full p-3">
                    <span class="material-symbols-outlined text-success-600 text-3xl">login</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Check Out</p>
                    <h3 class="text-2xl font-bold text-warning-600" id="checkOutCount">0</h3>
                </div>
                <div class="bg-warning-100 rounded-full p-3">
                    <span class="material-symbols-outlined text-warning-600 text-3xl">logout</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Log Terbaru</p>
                    <h3 class="text-sm font-bold text-gray-900" id="lastLog">-</h3>
                </div>
                <div class="bg-info-100 rounded-full p-3">
                    <span class="material-symbols-outlined text-info-600 text-3xl">schedule</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-6">
    <div class="card-body">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Tanggal</label>
                <input type="date" id="filterDate" value="" placeholder="Semua tanggal" onchange="loadLogs()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Mesin</label>
                <select id="filterDevice" onchange="loadLogs()" class="w-full px-4 py-2 border border-gray-300 rounded-xl">
                    <option value="">Semua Mesin</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Limit</label>
                <select id="filterLimit" onchange="loadLogs()" class="w-full px-4 py-2 border border-gray-300 rounded-xl">
                    <option value="50">50 Log Terbaru</option>
                    <option value="100" selected>100 Log Terbaru</option>
                    <option value="200">200 Log Terbaru</option>
                    <option value="500">500 Log Terbaru</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">&nbsp;</label>
                <button onclick="clearFilters()" class="w-full btn-secondary">
                    <span class="material-symbols-outlined text-sm mr-2">filter_alt_off</span>
                    Reset Filter
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Logs Table -->
<div class="card">
    <div class="card-body">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Waktu</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">NIS</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Nama Siswa</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Mesin</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">Tipe</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody id="logsTable">
                    <tr>
                        <td colspan="6" class="text-center py-12">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                            <p class="text-gray-500 mt-4">Memuat data log...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    let autoRefreshInterval = null;
    let isAutoRefresh = false;

    document.addEventListener('DOMContentLoaded', function() {
        loadDevices();
        loadLogs();
    });

    // Load devices for filter
    function loadDevices() {
        fetch('<?= base_url('api/admin/devices') ?>', {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const select = document.getElementById('filterDevice');
                    data.data.forEach(device => {
                        select.innerHTML += `<option value="${device.id}">${device.name} (${device.sn})</option>`;
                    });
                }
            })
            .catch(error => {
                console.error('Error loading devices:', error);
            });
    }

    // Load logs
    function loadLogs() {
        const date = document.getElementById('filterDate').value;
        const deviceId = document.getElementById('filterDevice').value;
        const limit = document.getElementById('filterLimit').value;

        let url = '<?= base_url('api/admin/attendance-logs') ?>?';
        if (date) url += `date=${date}&`;
        if (deviceId) url += `device_id=${deviceId}&`;
        if (limit) url += `limit=${limit}`;

        console.log('Fetching logs from:', url);

        fetch(url, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);
                console.log('Response headers:', Object.fromEntries(response.headers.entries()));

                if (response.status === 401) {
                    // Session expired - reload page
                    document.getElementById('logsTable').innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center py-12">
                                <div class="text-warning-600">
                                    <span class="material-symbols-outlined text-4xl">lock</span>
                                    <p class="mt-2 font-medium">Session Expired</p>
                                    <p class="text-sm mt-2">Silakan refresh halaman atau login kembali</p>
                                    <button onclick="location.reload()" class="btn-primary mt-4">Refresh Halaman</button>
                                </div>
                            </td>
                        </tr>
                    `;
                    return null;
                }
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                return response.json();
            })
            .then(data => {
                if (!data) return;

                console.log('Parsed data:', data);
                console.log('Data status:', data.status);
                console.log('Data length:', data.data ? data.data.length : 0);

                if (data.status === 'error') {
                    console.error('API Error:', data.message);
                    document.getElementById('logsTable').innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center py-12">
                                <div class="text-red-600">
                                    <span class="material-symbols-outlined text-4xl">error</span>
                                    <p class="mt-2">${data.message}</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    return;
                }

                if (data.data && data.data.length > 0) {
                    const lastLogTime = data.data[0].att_time;
                    document.getElementById('lastLog').textContent = formatDateTime(lastLogTime);

                    // Render table
                    tbody.innerHTML = data.data.map(log => {
                        const punchType = log.status === 0 ? 'Check In' :
                            log.status === 1 ? 'Check Out' : 'Unknown';
                        const punchColor = log.status === 0 ? 'success' :
                            log.status === 1 ? 'warning' : 'secondary';

                        return `
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900">${formatDateTime(log.att_time)}</div>
                                <div class="text-xs text-gray-500">${formatDate(log.att_time)}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-mono text-sm">${log.nis || log.pin || '-'}</span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900">${log.student_name || 'Tidak Dikenali'}</div>
                                <div class="text-xs text-gray-500">PIN: ${log.pin}</div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="text-sm text-gray-600">${log.device_name || '-'}</div>
                                <div class="text-xs text-gray-400">${log.device_sn || '-'}</div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="badge-${punchColor} text-xs">
                                    ${punchType}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                ${log.student_id ? 
                                    '<span class="badge-success text-xs">Teridentifikasi</span>' :
                                    '<span class="badge-danger text-xs">Tidak Dikenali</span>'
                                }
                            </td>
                        </tr>
                    `;
                    }).join('');
                } else {
                    // Reset stats
                    document.getElementById('totalLogs').textContent = '0';
                    document.getElementById('checkInCount').textContent = '0';
                    document.getElementById('checkOutCount').textContent = '0';
                    document.getElementById('lastLog').textContent = '-';

                    tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-12">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                <span class="material-symbols-outlined text-gray-400 text-4xl">description</span>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Log</h3>
                            <p class="text-gray-600">Data absensi akan muncul di sini ketika ada yang scan di mesin</p>
                        </td>
                    </tr>
                `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('logsTable').innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-12">
                            <div class="text-red-600">
                                <span class="material-symbols-outlined text-4xl">error</span>
                                <p class="mt-2 font-medium">Terjadi Kesalahan</p>
                                <p class="text-sm mt-2">${error.message || 'Tidak dapat memuat data log'}</p>
                                <button onclick="loadLogs()" class="btn-primary mt-4">Coba Lagi</button>
                            </div>
                        </td>
                    </tr>
                `;
            });
    }

    // Toggle auto refresh
    function toggleAutoRefresh() {
        const btn = document.getElementById('autoRefreshBtn');

        if (isAutoRefresh) {
            // Stop auto refresh
            clearInterval(autoRefreshInterval);
            isAutoRefresh = false;
            btn.innerHTML = '<span class="material-symbols-outlined">play_arrow</span><span>Auto Refresh</span>';
            btn.classList.remove('bg-success-600', 'hover:bg-success-700');
            btn.classList.add('btn-primary');
        } else {
            // Start auto refresh every 5 seconds
            autoRefreshInterval = setInterval(loadLogs, 5000);
            isAutoRefresh = true;
            btn.innerHTML = '<span class="material-symbols-outlined">stop</span><span>Stop Refresh</span>';
            btn.classList.remove('btn-primary');
            btn.classList.add('bg-success-600', 'hover:bg-success-700');
            loadLogs(); // Load immediately
        }
    }

    // Clear filters
    function clearFilters() {
        document.getElementById('filterDate').value = '';
        document.getElementById('filterDevice').value = '';
        document.getElementById('filterLimit').value = '100';
        loadLogs();
    }

    // Format datetime
    function formatDateTime(datetime) {
        if (!datetime) return '-';
        const date = new Date(datetime);
        return date.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }

    // Format date
    function formatDate(datetime) {
        if (!datetime) return '-';
        const date = new Date(datetime);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }
</script>

<?= $this->endSection() ?>