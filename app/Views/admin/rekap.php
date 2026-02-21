<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_admin') ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .rekap-table {
        font-size: 11px;
        border-collapse: collapse;
        width: 100%;
        background: white;
    }

    .rekap-table th,
    .rekap-table td {
        border: 1px solid #d1d5db;
        padding: 4px 6px;
        text-align: center;
        vertical-align: middle;
    }

    .rekap-table thead th {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: white;
        font-weight: 600;
        position: sticky;
        top: 0;
        z-index: 10;
        box-shadow: 0 2px 4px rgba(79, 70, 229, 0.1);
    }

    .rekap-table .student-name {
        text-align: left;
        font-weight: 500;
        white-space: nowrap;
    }

    .rekap-table .nis-col {
        background: #eef2ff;
        font-weight: 500;
    }

    .rekap-table .date-header {
        writing-mode: horizontal-tb;
        font-size: 10px;
        min-width: 28px;
        padding: 2px;
    }

    .rekap-table .day-label {
        font-size: 9px;
        color: #fff;
        display: block;
    }

    .rekap-table .status-cell {
        cursor: pointer;
        transition: background 0.2s;
        font-weight: 600;
        font-size: 10px;
        min-width: 25px;
    }

    .rekap-table .status-cell:hover:not(.weekend):not(.holiday) {
        background: #f3f4f6;
    }

    .rekap-table .status-H {
        background: #d1fae5;
        color: #065f46;
    }

    .rekap-table .status-S {
        background: #fed7aa;
        color: #7c2d12;
    }

    .rekap-table .status-I {
        background: #bfdbfe;
        color: #1e3a8a;
    }

    .rekap-table .status-A {
        background: #fecaca;
        color: #7f1d1d;
    }

    .rekap-table .weekend-cell,
    .rekap-table .holiday-cell {
        background: #fecaca !important;
        color: #dc2626;
        font-weight: 700;
    }

    .rekap-table .weekend-header {
        background: #fecaca !important;
        color: white;
    }

    .rekap-table .total-col {
        background: #e0e7ff;
        font-weight: 600;
        color: #4338ca;
    }

    .table-wrapper {
        overflow-x: auto;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(79, 70, 229, 0.1), 0 1px 3px rgba(0, 0, 0, 0.05);
        border: 1px solid #e0e7ff;
    }

    .info-box {
        background: linear-gradient(to right, #eef2ff, #e0e7ff);
        border: 1px solid #c7d2fe;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(99, 102, 241, 0.1);
    }

    .info-row {
        display: flex;
        gap: 40px;
        flex-wrap: wrap;
    }

    .info-item {
        display: flex;
        gap: 8px;
    }

    .info-label {
        font-weight: 600;
        color: #4338ca;
    }

    .info-value {
        color: #4f46e5;
        font-weight: 500;
    }

    .legend {
        display: flex;
        gap: 24px;
        align-items: center;
        padding: 12px 16px;
        background: white;
        border-radius: 8px;
        margin-top: 16px;
        border: 1px solid #e5e7eb;
        font-size: 13px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 500;
        color: #374151;
    }

    .legend-item span:first-child {
        font-weight: 700;
        font-size: 14px;
    }

    .legend-separator {
        color: #d1d5db;
        font-weight: 300;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Rekap Daftar Hadir</h2>
        <p class="text-gray-600 mt-1">Rekap kehadiran siswa per bulan</p>
    </div>
    <div class="flex gap-2">
        <button onclick="exportToExcel()" class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors flex items-center gap-2">
            <span class="material-symbols text-lg">download</span>
            Export Excel
        </button>
        <button onclick="printTable()" class="px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-colors flex items-center gap-2">
            <span class="material-symbols text-lg">print</span>
            Cetak
        </button>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 mb-6">
    <div class="flex flex-col md:flex-row gap-4 items-end">
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
            <select id="classFilter" onchange="loadRekap()" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white text-sm">
                <option value="">-- Pilih Kelas --</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
            <select id="monthFilter" onchange="loadRekap()" class="px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                <option value="1">Januari</option>
                <option value="2">Februari</option>
                <option value="3">Maret</option>
                <option value="4">April</option>
                <option value="5">Mei</option>
                <option value="6">Juni</option>
                <option value="7">Juli</option>
                <option value="8">Agustus</option>
                <option value="9">September</option>
                <option value="10">Oktober</option>
                <option value="11">November</option>
                <option value="12">Desember</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
            <select id="yearFilter" onchange="loadRekap()" class="px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                <option value="2024">2024</option>
                <option value="2025">2025</option>
                <option value="2026">2026</option>
                <option value="2027">2027</option>
            </select>
        </div>
    </div>

    <!-- Legend -->
    <div class="legend">
        <div class="legend-item">
            <span>H</span>
            <span>Hadir</span>
        </div>
        <span class="legend-separator">|</span>
        <div class="legend-item">
            <span>S</span>
            <span>Sakit</span>
        </div>
        <span class="legend-separator">|</span>
        <div class="legend-item">
            <span>I</span>
            <span>Izin</span>
        </div>
        <span class="legend-separator">|</span>
        <div class="legend-item">
            <span>A</span>
            <span>Alpha</span>
        </div>
    </div>
</div>

<!-- Info Box -->
<div id="infoBox" class="info-box hidden">
    <div class="info-row">
        <div class="info-item">
            <span class="info-label">Kelas:</span>
            <span class="info-value" id="infoKelas">-</span>
        </div>
        <div class="info-item">
            <span class="info-label">Tahun Pelajaran:</span>
            <span class="info-value" id="infoTahun">-</span>
        </div>
        <div class="info-item">
            <span class="info-label">Bulan:</span>
            <span class="info-value" id="infoBulan">-</span>
        </div>
        <div class="info-item">
            <span class="info-label">Wali Kelas:</span>
            <span class="info-value" id="infoWali">-</span>
        </div>
    </div>
</div>

<!-- Table Container -->
<div class="table-wrapper" id="tableContainer">
    <div class="text-center py-16">
        <span class="material-symbols text-6xl text-gray-300 mb-4">table_chart</span>
        <h3 class="text-lg font-semibold text-gray-500">Pilih kelas dan periode untuk menampilkan rekap</h3>
        <p class="text-gray-400 mt-1">Pilih kelas, bulan, dan tahun di atas</p>
    </div>
</div>

<!-- Loading State -->
<div id="loadingState" class="hidden text-center py-16">
    <svg class="animate-spin h-10 w-10 text-primary-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
    </svg>
    <p class="text-gray-500">Memuat data rekap...</p>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    let nationalHolidays = [];

    // Init
    document.addEventListener('DOMContentLoaded', function() {
        const now = new Date();
        document.getElementById('monthFilter').value = now.getMonth() + 1;
        document.getElementById('yearFilter').value = now.getFullYear();
        loadClasses();
    });

    // Load Classes
    async function loadClasses() {
        try {
            const resp = await fetch('<?= base_url('api/admin/classes') ?>', {
                credentials: 'include',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await resp.json();
            const select = document.getElementById('classFilter');
            if (data.data && data.data.length > 0) {
                data.data.forEach(cls => {
                    const opt = document.createElement('option');
                    opt.value = cls.id;
                    opt.textContent = cls.name;
                    select.appendChild(opt);
                });
            }
        } catch (err) {
            console.error('Gagal memuat kelas:', err);
        }
    }

    // Load Rekap
    async function loadRekap() {
        const classId = document.getElementById('classFilter').value;
        const month = document.getElementById('monthFilter').value;
        const year = document.getElementById('yearFilter').value;

        if (!classId || !month || !year) {
            return;
        }

        const tableContainer = document.getElementById('tableContainer');
        const infoBox = document.getElementById('infoBox');
        const loadingState = document.getElementById('loadingState');

        loadingState.classList.remove('hidden');
        tableContainer.innerHTML = '';

        // Fetch holidays for this month
        await fetchHolidaysForMonth(year, month);

        try {
            const resp = await fetch(`<?= base_url('api/admin/rekap') ?>?class_id=${classId}&month=${month}&year=${year}`, {
                credentials: 'include',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await resp.json();

            if (data.success && data.data) {
                renderRekapTable(data.data);
                updateInfoBox(data.data);
                infoBox.classList.remove('hidden');
            } else {
                tableContainer.innerHTML = `<div class="text-center py-16 text-red-600">Gagal memuat data: ${data.message || 'Unknown error'}</div>`;
            }
        } catch (err) {
            console.error('Error:', err);
            tableContainer.innerHTML = `<div class="text-center py-16 text-red-600">Terjadi kesalahan saat memuat data</div>`;
        } finally {
            loadingState.classList.add('hidden');
        }
    }

    function updateInfoBox(data) {
        document.getElementById('infoKelas').textContent = data.class_name || '-';
        document.getElementById('infoTahun').textContent = data.academic_year || '-';
        document.getElementById('infoBulan').textContent = monthNames[data.month - 1] + ' ' + data.year;
        document.getElementById('infoWali').textContent = data.homeroom_teacher || '-';
    }

    // Fetch holidays for month
    async function fetchHolidaysForMonth(year, month) {
        try {
            const resp = await fetch(`<?= base_url('api/admin/school-holidays') ?>?year=${year}&month=${month}`, {
                credentials: 'include',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await resp.json();
            nationalHolidays = data.data || [];
        } catch (e) {
            nationalHolidays = [];
        }
    }

    function isHoliday(dateStr) {
        return nationalHolidays.some(h => h.date === dateStr);
    }

    function renderRekapTable(data) {
        const {
            students,
            dates,
            attendance,
            month,
            year
        } = data;

        let html = '<table class="rekap-table"><thead><tr>';
        html += '<th rowspan="2">No</th>';
        html += '<th rowspan="2">No Induk</th>';
        html += '<th rowspan="2" style="min-width: 200px;">Nama Siswa</th>';

        // Date headers
        dates.forEach(d => {
            const date = new Date(d.date);
            const dayName = dayNames[date.getDay()];
            const isWeekend = date.getDay() === 0 || date.getDay() === 6;
            const isHol = isHoliday(d.date);
            const headerClass = (isWeekend || isHol) ? 'weekend-header' : '';
            html += `<th class="date-header ${headerClass}" title="${d.date}">`;
            html += `${date.getDate()}<span class="day-label">${dayName}</span>`;
            html += '</th>';
        });

        html += '<th rowspan="2">S</th>';
        html += '<th rowspan="2">I</th>';
        html += '<th rowspan="2">A</th>';
        html += '<th rowspan="2">H</th>';
        html += '<th rowspan="2">%</th>';
        html += '</tr></thead><tbody>';

        // Student rows
        students.forEach((student, idx) => {
            const studentAttendance = attendance[student.id] || {};
            let countS = 0,
                countI = 0,
                countA = 0,
                countH = 0;

            html += '<tr>';
            html += `<td>${idx + 1}</td>`;
            html += `<td class="nis-col">${escHtml(student.nis)}</td>`;
            html += `<td class="student-name">${escHtml(student.name)}</td>`;

            dates.forEach(d => {
                const date = new Date(d.date);
                const isWeekend = date.getDay() === 0 || date.getDay() === 6;
                const isHol = isHoliday(d.date);

                // If weekend or holiday, show "O" in red
                if (isWeekend || isHol) {
                    html += `<td class="status-cell ${isWeekend ? 'weekend-cell' : 'holiday-cell'}">O</td>`;
                } else {
                    const status = studentAttendance[d.date] || '';
                    let statusLabel = '';
                    let statusClass = '';

                    if (status === 'hadir') {
                        statusLabel = 'H';
                        statusClass = 'status-H';
                        countH++;
                    } else if (status === 'sakit') {
                        statusLabel = 'S';
                        statusClass = 'status-S';
                        countS++;
                    } else if (status === 'izin') {
                        statusLabel = 'I';
                        statusClass = 'status-I';
                        countI++;
                    } else if (status === 'alpha') {
                        statusLabel = 'A';
                        statusClass = 'status-A';
                        countA++;
                    }

                    html += `<td class="status-cell ${statusClass}">${statusLabel}</td>`;
                }
            });

            const total = countS + countI + countA + countH;
            const percentage = total > 0 ? ((countH / total) * 100).toFixed(1) : '0.0';

            html += `<td class="total-col">${countS}</td>`;
            html += `<td class="total-col">${countI}</td>`;
            html += `<td class="total-col">${countA}</td>`;
            html += `<td class="total-col">${countH}</td>`;
            html += `<td class="total-col">${percentage}%</td>`;
            html += '</tr>';
        });

        html += '</tbody></table>';
        document.getElementById('tableContainer').innerHTML = html;
    }

    function escHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function exportToExcel() {
        alert('Fitur export Excel akan segera tersedia');
    }

    function printTable() {
        window.print();
    }
</script>
<?= $this->endSection() ?>