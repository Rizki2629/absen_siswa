<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_teacher') ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .status-btn {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 2px solid transparent;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .status-btn.btn-hadir {
        background: #ecfdf5;
        color: #059669;
        border-color: #a7f3d0;
    }

    .status-btn.btn-hadir.active,
    .status-btn.btn-hadir:hover {
        background: #059669;
        color: white;
        border-color: #059669;
    }

    .status-btn.btn-sakit {
        background: #fff7ed;
        color: #ea580c;
        border-color: #fed7aa;
    }

    .status-btn.btn-sakit.active,
    .status-btn.btn-sakit:hover {
        background: #ea580c;
        color: white;
        border-color: #ea580c;
    }

    .status-btn.btn-izin {
        background: #eff6ff;
        color: #2563eb;
        border-color: #bfdbfe;
    }

    .status-btn.btn-izin.active,
    .status-btn.btn-izin:hover {
        background: #2563eb;
        color: white;
        border-color: #2563eb;
    }

    .status-btn.btn-alpa {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
    }

    .status-btn.btn-alpa.active,
    .status-btn.btn-alpa:hover {
        background: #dc2626;
        color: white;
        border-color: #dc2626;
    }

    .student-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        border: 2px solid #e5e7eb;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .student-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: #e5e7eb;
        transition: background 0.3s ease;
    }

    .student-card.status-hadir {
        border-color: #a7f3d0;
    }

    .student-card.status-hadir::before {
        background: #059669;
    }

    .student-card.status-sakit {
        border-color: #fed7aa;
    }

    .student-card.status-sakit::before {
        background: #ea580c;
    }

    .student-card.status-izin {
        border-color: #bfdbfe;
    }

    .student-card.status-izin::before {
        background: #2563eb;
    }

    .student-card.status-alpha {
        border-color: #fecaca;
    }

    .student-card.status-alpha::before {
        background: #dc2626;
    }

    .jam-row {
        display: flex;
        gap: 8px;
        margin-top: 10px;
        margin-bottom: 6px;
    }

    .jam-item {
        flex: 1;
        background: #f9fafb;
        border-radius: 10px;
        padding: 8px 10px;
        text-align: center;
        border: 1px solid #e5e7eb;
    }

    .jam-item label {
        display: block;
        font-size: 10px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 4px;
    }

    .jam-item .jam-value {
        font-size: 14px;
        font-weight: 700;
        color: #1f2937;
    }

    .jam-item .jam-value.empty {
        color: #9ca3af;
    }

    .avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 20px;
        color: white;
        flex-shrink: 0;
    }

    .stat-card {
        border-radius: 16px;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .stat-card .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .submit-btn {
        background: linear-gradient(135deg, #059669, #047857);
        color: white;
        padding: 10px 24px;
        border-radius: 12px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(5, 150, 105, .3);
    }

    .submit-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(5, 150, 105, .4);
    }

    .submit-btn:disabled {
        opacity: .6;
        cursor: not-allowed;
        transform: none;
    }

    .mark-all-btn {
        padding: 8px 16px;
        border-radius: 10px;
        font-weight: 500;
        font-size: 13px;
        cursor: pointer;
        border: 2px solid;
        transition: all 0.2s ease;
    }

    .lock-banner {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border: 2px solid #f59e0b;
        border-radius: 16px;
        padding: 16px 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
    }

    .lock-banner .lock-icon {
        width: 48px;
        height: 48px;
        background: #f59e0b;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .lock-banner .lock-icon span {
        color: white;
        font-size: 24px;
    }

    .page-locked .student-card,
    .page-locked .mark-all-btn,
    .page-locked .submit-btn {
        opacity: .5;
        pointer-events: none;
    }

    .date-nav-btn {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        border: 2px solid #e5e7eb;
        background: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .date-nav-btn:hover {
        border-color: #6366f1;
        background: #eef2ff;
    }

    .date-display {
        min-width: 200px;
        text-align: center;
        cursor: pointer;
        padding: 8px 16px;
        border-radius: 12px;
        border: 2px solid #e5e7eb;
        background: white;
        position: relative;
    }

    .date-display:hover {
        border-color: #6366f1;
    }

    #datePickerInput {
        position: absolute;
        opacity: 0;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        cursor: pointer;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$teacherClass = $teacherClass ?? null;
?>

<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Daftar Hadir</h2>
        <p class="text-gray-600 mt-1">
            <?php if ($teacherClass): ?>
                Kelas <?= esc($teacherClass['name']) ?>
            <?php else: ?>
                Kelola kehadiran siswa
            <?php endif; ?>
        </p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="submitAttendance()" class="submit-btn" id="submitBtn" disabled>
            <span class="material-symbols text-xl">save</span>
            Simpan Kehadiran
        </button>
    </div>
</div>

<?php if (!$teacherClass): ?>
    <div class="bg-white rounded-2xl shadow p-12 text-center">
        <span class="material-symbols text-6xl text-gray-300 mb-4">school</span>
        <p class="text-gray-500 text-lg">Anda belum ditetapkan sebagai wali kelas</p>
        <p class="text-gray-400 text-sm mt-2">Hubungi administrator untuk mengatur kelas Anda</p>
    </div>
<?php else: ?>

    <input type="hidden" id="classId" value="<?= esc($teacherClass['id']) ?>">

    <!-- Toolbar -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 mb-6">
        <div class="flex flex-col md:flex-row gap-4 items-center">

            <!-- Date Navigation -->
            <div class="flex items-center gap-2">
                <button class="date-nav-btn" onclick="changeDate(-1)" title="Hari sebelumnya">
                    <span class="material-symbols text-xl">chevron_left</span>
                </button>
                <div class="date-display" title="Klik untuk pilih tanggal">
                    <input type="date" id="datePickerInput" onchange="onDatePickerChange(this.value)">
                    <div>
                        <p class="text-xs text-gray-500 font-medium" id="dateLabel">Hari ini</p>
                        <p class="text-sm font-bold text-gray-900" id="dateDisplay">-</p>
                    </div>
                </div>
                <button class="date-nav-btn" onclick="changeDate(1)" title="Hari berikutnya">
                    <span class="material-symbols text-xl">chevron_right</span>
                </button>
            </div>

            <!-- Search -->
            <div class="flex-1">
                <div class="relative">
                    <span class="material-symbols absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xl">search</span>
                    <input type="text" id="searchInput" oninput="filterStudents()" placeholder="Cari nama atau NIS..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                </div>
            </div>

            <!-- Mark All -->
            <div class="flex-shrink-0">
                <button onclick="markAll('hadir')" class="mark-all-btn border-green-300 text-green-700 bg-green-50 hover:bg-green-600 hover:text-white hover:border-green-600">
                    <span class="material-symbols text-sm align-middle mr-1">check_circle</span>Semua Hadir
                </button>
            </div>
        </div>
    </div>

    <!-- Lock Banner -->
    <div id="lockBanner" class="lock-banner hidden">
        <div class="lock-icon"><span class="material-symbols">lock</span></div>
        <div>
            <h3 class="font-bold text-amber-900 text-base">Absensi Dikunci</h3>
            <p class="text-amber-800 text-sm mt-0.5" id="lockReason"></p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="stat-card bg-white border border-gray-200 shadow-sm">
            <div class="stat-icon bg-blue-100"><span class="material-symbols text-blue-600">groups</span></div>
            <div>
                <p class="text-sm text-gray-500">Total Siswa</p>
                <p class="text-2xl font-bold text-gray-900" id="statTotal">0</p>
            </div>
        </div>
        <div class="stat-card bg-white border border-gray-200 shadow-sm">
            <div class="stat-icon bg-green-100"><span class="material-symbols text-green-600">how_to_reg</span></div>
            <div>
                <p class="text-sm text-gray-500">Hadir</p>
                <p class="text-2xl font-bold text-green-600" id="statHadir">0</p>
            </div>
        </div>
        <div class="stat-card bg-white border border-gray-200 shadow-sm">
            <div class="stat-icon bg-yellow-100"><span class="material-symbols text-yellow-600">medical_services</span></div>
            <div>
                <p class="text-sm text-gray-500">Sakit / Izin</p>
                <p class="text-2xl font-bold text-yellow-600" id="statSakitIzin">0</p>
            </div>
        </div>
        <div class="stat-card bg-white border border-gray-200 shadow-sm">
            <div class="stat-icon bg-red-100"><span class="material-symbols text-red-600">person_off</span></div>
            <div>
                <p class="text-sm text-gray-500">Alpa</p>
                <p class="text-2xl font-bold text-red-600" id="statAlpa">0</p>
            </div>
        </div>
    </div>

    <!-- Student Cards Grid -->
    <div id="studentGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4" style="min-height:200px;"></div>

    <!-- Loading State -->
    <div id="loadingState" class="hidden text-center py-16">
        <svg class="animate-spin h-10 w-10 text-primary-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        <p class="text-gray-500">Memuat data siswa...</p>
    </div>

    <!-- Toast -->
    <div id="toast" class="fixed bottom-6 right-6 z-50 hidden">
        <div class="bg-gray-900 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3">
            <span class="material-symbols text-xl" id="toastIcon">check_circle</span>
            <span id="toastMessage">Berhasil disimpan</span>
        </div>
    </div>

<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // ── State ──────────────────────────────────────────────────────────────
    let students = [];
    let attendanceData = {}; // { studentId: status }
    let existingIds = {}; // { studentId: summaryId }
    let holidayCache = {};
    let isLocked = false;
    let currentDate = '';

    const CLASS_ID = document.getElementById('classId')?.value;
    const avatarColors = ['#6366f1', '#8b5cf6', '#ec4899', '#f43f5e', '#f97316', '#eab308', '#22c55e', '#14b8a6', '#06b6d4', '#3b82f6'];

    // ── Holiday helpers ────────────────────────────────────────────────────
    const bulanNamesApi = ['januari', 'februari', 'maret', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'desember'];
    let harikerjaCache = {};

    const HOLIDAYS_FALLBACK = {
        '2025': [{
            date: '2025-01-01',
            name: 'Tahun Baru Masehi'
        }, {
            date: '2025-01-27',
            name: 'Isra Mikraj'
        }, {
            date: '2025-01-29',
            name: 'Imlek 2576'
        }, {
            date: '2025-03-14',
            name: 'Nyepi'
        }, {
            date: '2025-03-29',
            name: 'Idul Fitri'
        }, {
            date: '2025-03-30',
            name: 'Idul Fitri'
        }, {
            date: '2025-04-18',
            name: 'Wafat Isa Al Masih'
        }, {
            date: '2025-05-01',
            name: 'Hari Buruh'
        }, {
            date: '2025-05-12',
            name: 'Waisak'
        }, {
            date: '2025-05-29',
            name: 'Kenaikan Isa'
        }, {
            date: '2025-06-01',
            name: 'Pancasila'
        }, {
            date: '2025-06-06',
            name: 'Idul Adha'
        }, {
            date: '2025-06-27',
            name: 'Tahun Baru Islam'
        }, {
            date: '2025-08-17',
            name: 'Kemerdekaan RI'
        }, {
            date: '2025-09-05',
            name: 'Maulid Nabi'
        }, {
            date: '2025-12-25',
            name: 'Natal'
        }],
        '2026': [{
            date: '2026-01-01',
            name: 'Tahun Baru Masehi'
        }, {
            date: '2026-01-16',
            name: 'Isra Mikraj'
        }, {
            date: '2026-02-16',
            name: 'Imlek 2577'
        }, {
            date: '2026-02-17',
            name: 'Imlek 2577'
        }, {
            date: '2026-03-18',
            name: 'Nyepi'
        }, {
            date: '2026-03-19',
            name: 'Nyepi'
        }, {
            date: '2026-03-20',
            name: 'Idul Fitri'
        }, {
            date: '2026-03-21',
            name: 'Idul Fitri'
        }, {
            date: '2026-03-22',
            name: 'Idul Fitri'
        }, {
            date: '2026-03-23',
            name: 'Idul Fitri'
        }, {
            date: '2026-03-24',
            name: 'Idul Fitri'
        }, {
            date: '2026-04-03',
            name: 'Wafat Yesus Kristus'
        }, {
            date: '2026-04-05',
            name: 'Paskah'
        }, {
            date: '2026-05-01',
            name: 'Hari Buruh'
        }, {
            date: '2026-05-14',
            name: 'Kenaikan Yesus'
        }, {
            date: '2026-05-15',
            name: 'Kenaikan Yesus'
        }, {
            date: '2026-05-27',
            name: 'Idul Adha'
        }, {
            date: '2026-05-28',
            name: 'Idul Adha'
        }, {
            date: '2026-05-31',
            name: 'Waisak'
        }, {
            date: '2026-06-01',
            name: 'Pancasila'
        }, {
            date: '2026-06-16',
            name: 'Tahun Baru Islam'
        }, {
            date: '2026-08-17',
            name: 'Kemerdekaan RI'
        }, {
            date: '2026-08-25',
            name: 'Maulid Nabi'
        }, {
            date: '2026-12-25',
            name: 'Natal'
        }],
        '2027': [{
            date: '2027-01-01',
            name: 'Tahun Baru Masehi'
        }, {
            date: '2027-02-06',
            name: 'Imlek 2578'
        }, {
            date: '2027-03-10',
            name: 'Idul Fitri'
        }, {
            date: '2027-03-11',
            name: 'Idul Fitri'
        }, {
            date: '2027-03-22',
            name: 'Nyepi'
        }, {
            date: '2027-03-26',
            name: 'Wafat Isa Al Masih'
        }, {
            date: '2027-05-01',
            name: 'Hari Buruh'
        }, {
            date: '2027-05-06',
            name: 'Kenaikan Isa'
        }, {
            date: '2027-05-17',
            name: 'Idul Adha'
        }, {
            date: '2027-06-01',
            name: 'Pancasila'
        }, {
            date: '2027-06-07',
            name: 'Tahun Baru Islam'
        }, {
            date: '2027-08-17',
            name: 'Kemerdekaan RI'
        }, {
            date: '2027-12-25',
            name: 'Natal'
        }]
    };

    function parseTanggalRange(tanggal, year, mIdx) {
        const parts = String(tanggal).split('-');
        const days = parts.length === 2 ? Array.from({
            length: parseInt(parts[1]) - parseInt(parts[0]) + 1
        }, (_, i) => parseInt(parts[0]) + i) : [parseInt(parts[0])];
        return days.filter(d => !isNaN(d)).map(d => `${year}-${String(mIdx+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`);
    }

    function getHolidaysFallback(year, month) {
        return (HOLIDAYS_FALLBACK[String(year)] || []).filter(h => h.date.substring(5, 7) === String(month).padStart(2, '0'));
    }

    async function fetchHolidaysForDate(dateStr) {
        const [year, month] = dateStr.split('-');
        const key = `${year}-${month}`;
        if (holidayCache[key]) return holidayCache[key];
        let national = [],
            school = [];
        try {
            if (!harikerjaCache[year]) {
                try {
                    const r = await fetch('https://harikerja.vercel.app/api');
                    if (r.ok) {
                        const j = await r.json();
                        if (j.code === 200 && j.data) harikerjaCache[year] = j.data;
                    }
                } catch (e) {}
            }
            if (harikerjaCache[year]) {
                const mIdx = parseInt(month) - 1;
                const found = harikerjaCache[year].find(m => m.bulan === bulanNamesApi[mIdx]);
                if (found?.tanggal_merah?.length) {
                    national = [];
                    found.tanggal_merah.forEach(tm => parseTanggalRange(tm.tanggal, year, mIdx).forEach(d => national.push({
                        date: d,
                        name: tm.memperingati
                    })));
                }
            }
            const schResp = await fetch(`<?= base_url('api/admin/school-holidays') ?>?year=${year}&month=${parseInt(month)}`, {
                credentials: 'include',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            }).then(r => r.json()).catch(() => ({
                data: []
            }));
            if (!national.length) national = getHolidaysFallback(year, parseInt(month));
            school = schResp.data || [];
        } catch (e) {
            national = getHolidaysFallback(year, parseInt(month));
        }
        holidayCache[key] = {
            national,
            school
        };
        return holidayCache[key];
    }

    function isDateDisabled(dateStr, holidays) {
        const day = new Date(dateStr + 'T00:00:00').getDay();
        if (day === 0 || day === 6) return {
            locked: true,
            reason: 'Libur Akhir Pekan'
        };
        const n = holidays.national.find(h => h.date === dateStr);
        const s = holidays.school.find(h => h.date === dateStr);
        if (n || s) return {
            locked: true,
            reason: (n || s).name
        };
        return {
            locked: false,
            reason: null
        };
    }

    function applyLockState(lock) {
        const banner = document.getElementById('lockBanner');
        isLocked = lock.locked;
        if (lock.locked) {
            banner.classList.remove('hidden');
            const reason = lock.reason || 'Hari Libur';
            document.getElementById('lockReason').textContent = 'Absensi dikunci karena: ' + reason;
            document.getElementById('submitBtn').disabled = true;
            document.body.classList.add('page-locked');
        } else {
            banner.classList.add('hidden');
            document.body.classList.remove('page-locked');
            document.getElementById('submitBtn').disabled = (students.length === 0);
        }
    }

    // ── Date Navigation ────────────────────────────────────────────────────
    const DAYS = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const MONTHS = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    function getTodayStr() {
        const d = new Date();
        return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
    }

    function formatDate(dateStr) {
        const d = new Date(dateStr + 'T00:00:00');
        return `${DAYS[d.getDay()]}, ${d.getDate()} ${MONTHS[d.getMonth()]} ${d.getFullYear()}`;
    }

    function setDate(dateStr) {
        currentDate = dateStr;
        const today = getTodayStr();
        document.getElementById('datePickerInput').value = dateStr;
        document.getElementById('dateDisplay').textContent = formatDate(dateStr);
        document.getElementById('dateLabel').textContent = (dateStr === today) ? 'Hari ini' : (dateStr < today ? 'Sebelumnya' : 'Mendatang');
        loadStudents();
    }

    function changeDate(delta) {
        const d = new Date(currentDate + 'T00:00:00');
        d.setDate(d.getDate() + delta);
        setDate(`${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`);
    }

    function onDatePickerChange(val) {
        if (val) setDate(val);
    }

    // ── Load ───────────────────────────────────────────────────────────────
    async function loadStudents() {
        if (!CLASS_ID || !currentDate) return;

        const holidays = await fetchHolidaysForDate(currentDate);
        applyLockState(isDateDisabled(currentDate, holidays));

        document.getElementById('loadingState').classList.remove('hidden');
        document.getElementById('studentGrid').innerHTML = '';
        attendanceData = {};
        existingIds = {};

        try {
            const resp = await fetch(`<?= base_url('api/teacher/attendance') ?>?class_id=${CLASS_ID}&date=${currentDate}`, {
                credentials: 'include',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const result = await resp.json();

            if (result.status !== 'success' && !result.success) {
                showToast(result.message || 'Gagal memuat data', 'error');
                return;
            }

            const payload = result.data || {};
            students = payload.students || [];
            const attendances = payload.attendances || {};

            students.forEach(s => {
                const rec = attendances[s.id];
                if (rec) {
                    attendanceData[s.id] = rec.status;
                    existingIds[s.id] = rec.id;
                    s.check_in = rec.check_in_time || '-';
                    s.check_out = rec.check_out_time || '-';
                } else {
                    s.check_in = '-';
                    s.check_out = '-';
                }
            });

            renderStudents(students);
            if (!isLocked) document.getElementById('submitBtn').disabled = (students.length === 0);
        } catch (err) {
            console.error(err);
            showToast('Gagal memuat data siswa', 'error');
        } finally {
            document.getElementById('loadingState').classList.add('hidden');
        }
    }

    // ── Render ─────────────────────────────────────────────────────────────
    function renderStudents(list) {
        const grid = document.getElementById('studentGrid');
        grid.innerHTML = '';

        if (!list.length) {
            grid.innerHTML = `<div class="col-span-full text-center py-12">
                <span class="material-symbols text-5xl text-gray-300">person_search</span>
                <p class="text-gray-500 mt-3">Tidak ada siswa di kelas ini</p></div>`;
            updateStats();
            return;
        }

        list.forEach((student, idx) => {
            const status = attendanceData[student.id] || '';
            const isHadir = (status === 'hadir' || status === 'terlambat');
            const jd = isHadir ? (student.check_in || '-') : '-';
            const jp = isHadir ? (student.check_out || '-') : '-';
            const color = avatarColors[idx % avatarColors.length];
            const card = document.createElement('div');
            card.className = `student-card ${status ? 'status-'+status : ''}`;
            card.id = `card-${student.id}`;
            card.innerHTML = `
                <div class="flex items-center gap-3 mb-4">
                    <div class="avatar" style="background:${color}">${getInitials(student.name)}</div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-gray-900 text-sm truncate">${escHtml(student.name)}</h4>
                        <p class="text-xs text-gray-500">NIS: ${escHtml(student.nis||'-')}</p>
                    </div>
                </div>
                <div class="jam-row" id="jam-row-${student.id}">
                    <div class="jam-item"><label>Jam Datang</label><span class="jam-value ${jd==='-'?'empty':''}">${jd}</span></div>
                    <div class="jam-item"><label>Jam Pulang</label><span class="jam-value ${jp==='-'?'empty':''}">${jp}</span></div>
                </div>
                <div class="flex gap-2">
                    <button class="status-btn btn-hadir flex-1 ${status==='hadir'?'active':''}" onclick="setStatus(${student.id},'hadir')">Hadir</button>
                    <button class="status-btn btn-sakit flex-1 ${status==='sakit'?'active':''}" onclick="setStatus(${student.id},'sakit')">Sakit</button>
                    <button class="status-btn btn-izin  flex-1 ${status==='izin' ?'active':''}" onclick="setStatus(${student.id},'izin')">Izin</button>
                    <button class="status-btn btn-alpa  flex-1 ${status==='alpha'?'active':''}" onclick="setStatus(${student.id},'alpha')">Alpa</button>
                </div>`;
            grid.appendChild(card);
        });
        updateStats();
    }

    function setStatus(studentId, status) {
        if (attendanceData[studentId] === status) delete attendanceData[studentId];
        else attendanceData[studentId] = status;

        const card = document.getElementById(`card-${studentId}`);
        if (card) {
            card.className = `student-card ${attendanceData[studentId] ? 'status-'+attendanceData[studentId] : ''}`;
            card.querySelectorAll('.status-btn').forEach(b => b.classList.remove('active'));
            if (attendanceData[studentId]) {
                const a = card.querySelector(`.btn-${attendanceData[studentId]}`);
                if (a) a.classList.add('active');
            }

            const jamRow = document.getElementById(`jam-row-${studentId}`);
            if (jamRow) {
                const st = attendanceData[studentId] || '';
                const isH = (st === 'hadir' || st === 'terlambat');
                const s = students.find(x => x.id == studentId);
                const jd = isH ? (s?.check_in || '-') : '-';
                const jp = isH ? (s?.check_out || '-') : '-';
                const spans = jamRow.querySelectorAll('.jam-value');
                spans[0].textContent = jd;
                spans[0].className = `jam-value ${jd==='-'?'empty':''}`;
                spans[1].textContent = jp;
                spans[1].className = `jam-value ${jp==='-'?'empty':''}`;
            }
        }
        updateStats();
    }

    function markAll(status) {
        students.forEach(s => {
            attendanceData[s.id] = status;
        });
        renderStudents(students);
    }

    function updateStats() {
        let hadir = 0,
            sakit = 0,
            izin = 0,
            alpa = 0;
        Object.values(attendanceData).forEach(s => {
            if (s === 'hadir') hadir++;
            else if (s === 'sakit') sakit++;
            else if (s === 'izin') izin++;
            else if (s === 'alpha') alpa++;
        });
        document.getElementById('statTotal').textContent = students.length;
        document.getElementById('statHadir').textContent = hadir;
        document.getElementById('statSakitIzin').textContent = sakit + izin;
        document.getElementById('statAlpa').textContent = alpa;
    }

    function filterStudents() {
        const q = document.getElementById('searchInput').value.toLowerCase().trim();
        renderStudents(q ? students.filter(s => s.name.toLowerCase().includes(q) || (s.nis || '').toLowerCase().includes(q)) : students);
    }

    // ── Save ───────────────────────────────────────────────────────────────
    async function submitAttendance() {
        if (isLocked) {
            showToast('Absensi dikunci untuk tanggal ini', 'error');
            return;
        }
        if (!currentDate) {
            showToast('Tanggal belum dipilih', 'error');
            return;
        }
        const entries = Object.entries(attendanceData);
        if (!entries.length) {
            showToast('Belum ada data kehadiran yang diisi', 'error');
            return;
        }

        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Menyimpan...';

        try {
            const records = entries.map(([sid, status]) => ({
                student_id: parseInt(sid),
                status,
                id: existingIds[sid] || null
            }));
            const resp = await fetch('<?= base_url('api/teacher/attendance') ?>', {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    date: currentDate,
                    records
                })
            });
            const result = await resp.json();
            if (result.success || result.status === 'success') {
                showToast(`Kehadiran berhasil disimpan (${entries.length} siswa)`, 'success');
                loadStudents();
            } else {
                showToast(result.message || 'Gagal menyimpan', 'error');
            }
        } catch (err) {
            console.error(err);
            showToast('Terjadi kesalahan saat menyimpan', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<span class="material-symbols text-xl">save</span> Simpan Kehadiran';
        }
    }

    // ── Helpers ────────────────────────────────────────────────────────────
    function getInitials(name) {
        const p = (name || '').trim().split(/\s+/);
        return p.length >= 2 ? (p[0][0] + p[1][0]).toUpperCase() : (name || '??').substring(0, 2).toUpperCase();
    }

    function escHtml(t) {
        const d = document.createElement('div');
        d.textContent = t;
        return d.innerHTML;
    }

    function showToast(msg, type = 'success') {
        const toast = document.getElementById('toast');
        document.getElementById('toastIcon').textContent = type === 'success' ? 'check_circle' : 'error';
        document.getElementById('toastMessage').textContent = msg;
        toast.querySelector('div').className = `${type==='success'?'bg-green-700':'bg-red-700'} text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3`;
        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 3500);
    }

    // ── Boot ───────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        if (CLASS_ID) setDate(getTodayStr());
    });
</script>
<?= $this->endSection() ?>