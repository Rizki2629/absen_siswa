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
<a href="<?= base_url('admin/attendance') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">how_to_reg</span>
    <span>Daftar Hadir</span>
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
<a href="<?= base_url('admin/reports') ?>" class="sidebar-item-active">
    <span class="material-symbols-outlined mr-3">assessment</span>
    <span>Laporan</span>
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Laporan Absensi</h2>
        <p class="text-gray-600 mt-1">Generate dan export laporan absensi siswa</p>
    </div>
</div>

<!-- Report Type Selection -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="card cursor-pointer hover:shadow-lg transition-shadow" onclick="selectReportType('daily')">
        <div class="card-body text-center">
            <div class="bg-primary-100 rounded-full p-4 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                <span class="material-symbols-outlined text-primary-600 text-3xl">today</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Laporan Harian</h3>
            <p class="text-sm text-gray-500 mt-2">Rekap absensi per hari</p>
        </div>
    </div>

    <div class="card cursor-pointer hover:shadow-lg transition-shadow" onclick="selectReportType('weekly')">
        <div class="card-body text-center">
            <div class="bg-success-100 rounded-full p-4 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                <span class="material-symbols-outlined text-success-600 text-3xl">date_range</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Laporan Mingguan</h3>
            <p class="text-sm text-gray-500 mt-2">Rekap absensi per minggu</p>
        </div>
    </div>

    <div class="card cursor-pointer hover:shadow-lg transition-shadow" onclick="selectReportType('monthly')">
        <div class="card-body text-center">
            <div class="bg-warning-100 rounded-full p-4 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                <span class="material-symbols-outlined text-warning-600 text-3xl">calendar_month</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Laporan Bulanan</h3>
            <p class="text-sm text-gray-500 mt-2">Rekap absensi per bulan</p>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-6">
    <div class="card-body">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Filter Laporan</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" id="startDate" value="<?= date('Y-m-01') ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" id="endDate" value="<?= date('Y-m-d') ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Kelas</label>
                <select id="filterClass" class="w-full px-4 py-2 border border-gray-300 rounded-xl">
                    <option value="">Semua Kelas</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">&nbsp;</label>
                <button onclick="generateReport()" class="w-full btn-primary">
                    <span class="material-symbols-outlined mr-2">assessment</span>
                    Generate Laporan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Report Preview -->
<div class="card" id="reportPreview" style="display: none;">
    <div class="card-body">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Preview Laporan</h3>
            <div class="flex space-x-2">
                <button onclick="exportPDF()" class="btn-secondary flex items-center">
                    <span class="material-symbols-outlined mr-2">picture_as_pdf</span>
                    Export PDF
                </button>
                <button onclick="exportExcel()" class="btn-primary flex items-center">
                    <span class="material-symbols-outlined mr-2">table_chart</span>
                    Export Excel
                </button>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-sm text-gray-500">Total Siswa</p>
                <p class="text-2xl font-bold text-gray-900" id="totalStudents">0</p>
            </div>
            <div class="bg-success-50 rounded-xl p-4">
                <p class="text-sm text-success-600">Hadir</p>
                <p class="text-2xl font-bold text-success-700" id="totalPresent">0</p>
            </div>
            <div class="bg-warning-50 rounded-xl p-4">
                <p class="text-sm text-warning-600">Terlambat</p>
                <p class="text-2xl font-bold text-warning-700" id="totalLate">0</p>
            </div>
            <div class="bg-danger-50 rounded-xl p-4">
                <p class="text-sm text-danger-600">Tidak Hadir</p>
                <p class="text-2xl font-bold text-danger-700" id="totalAbsent">0</p>
            </div>
        </div>

        <!-- Report Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">No</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">NIS</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Nama Siswa</th>
                        <th class="text-left py-3 px-4 font-semibold text-gray-700">Kelas</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">Hadir</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">Terlambat</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">Ijin</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">Sakit</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">Alpha</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">%</th>
                    </tr>
                </thead>
                <tbody id="reportTable">
                    <tr>
                        <td colspan="10" class="text-center py-12 text-gray-500">
                            Klik "Generate Laporan" untuk menampilkan data
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    let selectedReportType = 'daily';

    document.addEventListener('DOMContentLoaded', function() {
        loadClasses();
    });

    function loadClasses() {
        fetch('<?= base_url('api/admin/classes') ?>', {
                credentials: 'same-origin'
            })
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

    function selectReportType(type) {
        selectedReportType = type;

        // Update date range based on type
        const today = new Date();
        const startDate = document.getElementById('startDate');
        const endDate = document.getElementById('endDate');

        if (type === 'daily') {
            startDate.value = today.toISOString().split('T')[0];
            endDate.value = today.toISOString().split('T')[0];
        } else if (type === 'weekly') {
            const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
            startDate.value = weekAgo.toISOString().split('T')[0];
            endDate.value = today.toISOString().split('T')[0];
        } else if (type === 'monthly') {
            startDate.value = today.toISOString().slice(0, 7) + '-01';
            endDate.value = today.toISOString().split('T')[0];
        }
    }

    function generateReport() {
        document.getElementById('reportPreview').style.display = 'block';

        // TODO: Fetch actual report data
        document.getElementById('totalStudents').textContent = '150';
        document.getElementById('totalPresent').textContent = '145';
        document.getElementById('totalLate').textContent = '3';
        document.getElementById('totalAbsent').textContent = '2';

        document.getElementById('reportTable').innerHTML = `
        <tr class="border-b border-gray-100">
            <td class="py-3 px-4">1</td>
            <td class="py-3 px-4">12345</td>
            <td class="py-3 px-4">Ahmad Fauzan</td>
            <td class="py-3 px-4">X IPA 1</td>
            <td class="py-3 px-4 text-center text-success-600 font-bold">20</td>
            <td class="py-3 px-4 text-center text-warning-600">2</td>
            <td class="py-3 px-4 text-center">1</td>
            <td class="py-3 px-4 text-center">0</td>
            <td class="py-3 px-4 text-center text-danger-600">0</td>
            <td class="py-3 px-4 text-center font-bold">95%</td>
        </tr>
        <tr class="border-b border-gray-100">
            <td class="py-3 px-4">2</td>
            <td class="py-3 px-4">12346</td>
            <td class="py-3 px-4">Budi Santoso</td>
            <td class="py-3 px-4">X IPA 1</td>
            <td class="py-3 px-4 text-center text-success-600 font-bold">22</td>
            <td class="py-3 px-4 text-center text-warning-600">0</td>
            <td class="py-3 px-4 text-center">0</td>
            <td class="py-3 px-4 text-center">1</td>
            <td class="py-3 px-4 text-center text-danger-600">0</td>
            <td class="py-3 px-4 text-center font-bold">100%</td>
        </tr>
    `;

        // Scroll to preview
        document.getElementById('reportPreview').scrollIntoView({
            behavior: 'smooth'
        });
    }

    function exportPDF() {
        alert('Export PDF - Coming soon!');
    }

    function exportExcel() {
        alert('Export Excel - Coming soon!');
    }
</script>

<?= $this->endSection() ?>