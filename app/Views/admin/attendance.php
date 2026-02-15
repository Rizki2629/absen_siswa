<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_admin') ?>
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
        letter-spacing: 0.5px;
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

    .student-card.status-alpa {
        border-color: #fecaca;
    }

    .student-card.status-alpa::before {
        background: #dc2626;
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
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
    }

    .submit-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(5, 150, 105, 0.4);
    }

    .submit-btn:disabled {
        opacity: 0.6;
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

    .page-locked .student-card {
        opacity: 0.5;
        pointer-events: none;
    }

    .page-locked .mark-all-btn,
    .page-locked .submit-btn {
        opacity: 0.5;
        pointer-events: none;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Daftar Hadir Siswa</h2>
        <p class="text-gray-600 mt-1">Kelola kehadiran siswa per kelas dan tanggal</p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="submitAttendance()" class="submit-btn" id="submitBtn" disabled>
            <span class="material-symbols-outlined text-xl">save</span>
            Simpan Kehadiran
        </button>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 mb-6">
    <div class="flex flex-col md:flex-row gap-4 items-end">
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
            <select id="classFilter" onchange="loadStudents()" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white text-sm">
                <option value="">-- Pilih Kelas --</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
            <input type="date" id="dateFilter" onchange="loadStudents()" class="px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
        </div>
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-1">Cari Siswa</label>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xl">search</span>
                <input type="text" id="searchInput" oninput="filterStudents()" placeholder="Cari nama atau NIS..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
            </div>
        </div>
        <div class="flex gap-2">
            <button onclick="markAll('hadir')" class="mark-all-btn border-green-300 text-green-700 bg-green-50 hover:bg-green-600 hover:text-white hover:border-green-600" title="Tandai Semua Hadir">
                <span class="material-symbols-outlined text-sm align-middle mr-1">check_circle</span>Semua Hadir
            </button>
        </div>
    </div>
</div>

<!-- Lock Banner -->
<div id="lockBanner" class="lock-banner hidden">
    <div class="lock-icon">
        <span class="material-symbols-outlined">lock</span>
    </div>
    <div>
        <h3 class="font-bold text-amber-900 text-base">Absensi Dikunci</h3>
        <p class="text-amber-800 text-sm mt-0.5" id="lockReason">Hari ini adalah hari libur</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="stat-card bg-white border border-gray-200 shadow-sm">
        <div class="stat-icon bg-blue-100">
            <span class="material-symbols-outlined text-blue-600">groups</span>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Siswa</p>
            <p class="text-2xl font-bold text-gray-900" id="statTotal">0</p>
        </div>
    </div>
    <div class="stat-card bg-white border border-gray-200 shadow-sm">
        <div class="stat-icon bg-green-100">
            <span class="material-symbols-outlined text-green-600">how_to_reg</span>
        </div>
        <div>
            <p class="text-sm text-gray-500">Hadir</p>
            <p class="text-2xl font-bold text-green-600" id="statHadir">0</p>
        </div>
    </div>
    <div class="stat-card bg-white border border-gray-200 shadow-sm">
        <div class="stat-icon bg-yellow-100">
            <span class="material-symbols-outlined text-yellow-600">medical_services</span>
        </div>
        <div>
            <p class="text-sm text-gray-500">Sakit / Izin</p>
            <p class="text-2xl font-bold text-yellow-600" id="statSakitIzin">0</p>
        </div>
    </div>
    <div class="stat-card bg-white border border-gray-200 shadow-sm">
        <div class="stat-icon bg-red-100">
            <span class="material-symbols-outlined text-red-600">person_off</span>
        </div>
        <div>
            <p class="text-sm text-gray-500">Alpa</p>
            <p class="text-2xl font-bold text-red-600" id="statAlpa">0</p>
        </div>
    </div>
</div>

<!-- Student Cards Grid -->
<div id="studentGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    <!-- Cards will be loaded here -->
</div>

<!-- Empty State -->
<div id="emptyState" class="hidden text-center py-16">
    <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">how_to_reg</span>
    <h3 class="text-lg font-semibold text-gray-500">Pilih kelas untuk menampilkan daftar siswa</h3>
    <p class="text-gray-400 mt-1">Pilih kelas dan tanggal di atas untuk mulai mengisi daftar hadir</p>
</div>

<!-- Loading State -->
<div id="loadingState" class="hidden text-center py-16">
    <svg class="animate-spin h-10 w-10 text-primary-600 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
    </svg>
    <p class="text-gray-500">Memuat data siswa...</p>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed bottom-6 right-6 z-50 hidden">
    <div class="bg-gray-900 text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3">
        <span class="material-symbols-outlined text-xl" id="toastIcon">check_circle</span>
        <span id="toastMessage">Berhasil disimpan</span>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // State
    let students = [];
    let attendanceData = {}; // { studentId: status }
    let existingSummaries = {}; // loaded from server
    let holidayCache = {}; // { 'YYYY-MM': { national: [], school: [] } }
    let isLocked = false;
    const avatarColors = ['#6366f1', '#8b5cf6', '#ec4899', '#f43f5e', '#f97316', '#eab308', '#22c55e', '#14b8a6', '#06b6d4', '#3b82f6'];

    // Init
    document.addEventListener('DOMContentLoaded', function() {
        // Set today's date
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        document.getElementById('dateFilter').value = `${yyyy}-${mm}-${dd}`;

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

    /**
     * Fetch holidays for a given month (cached)
     */
    async function fetchHolidaysForDate(dateStr) {
        const [year, month] = dateStr.split('-');
        const key = `${year}-${month}`;
        if (holidayCache[key]) return holidayCache[key];

        let national = [],
            school = [];
        try {
            const [natResp, schResp] = await Promise.all([
                fetch(`https://api-harilibur.vercel.app/api?year=${year}&month=${parseInt(month)}`).then(r => r.json()).catch(() => []),
                fetch(`<?= base_url('api/admin/school-holidays') ?>?year=${year}&month=${parseInt(month)}`, {
                    credentials: 'include',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                }).then(r => r.json()).catch(() => ({
                    data: []
                }))
            ]);
            national = (natResp || []).filter(h => h.is_national_holiday).map(h => ({
                date: h.holiday_date,
                name: h.holiday_name
            }));
            school = schResp.data || [];
        } catch (e) {}

        holidayCache[key] = {
            national,
            school
        };
        return holidayCache[key];
    }

    /**
     * Check if a date is disabled/locked for attendance
     */
    function isDateDisabled(dateStr, holidays) {
        const d = new Date(dateStr);
        const day = d.getDay();
        const isWeekend = (day === 0 || day === 6);
        const natHoliday = holidays.national.find(h => h.date === dateStr);
        const schHoliday = holidays.school.find(h => h.date === dateStr);

        if (isWeekend || natHoliday || schHoliday) {
            let reason = 'Libur Akhir Pekan';
            if (natHoliday) reason = natHoliday.name;
            else if (schHoliday) reason = schHoliday.name;
            return {
                locked: true,
                reason
            };
        }
        return {
            locked: false,
            reason: null
        };
    }

    /**
     * Apply or remove lock state on the page
     */
    function applyLockState(lockStatus) {
        const banner = document.getElementById('lockBanner');
        const content = document.querySelector('#studentGrid').parentElement;
        isLocked = lockStatus.locked;

        if (lockStatus.locked) {
            banner.classList.remove('hidden');
            document.getElementById('lockReason').textContent = `Absensi dikunci karena: ${lockStatus.reason}`;
            document.getElementById('submitBtn').disabled = true;
            document.body.classList.add('page-locked');
        } else {
            banner.classList.add('hidden');
            document.body.classList.remove('page-locked');
        }
    }

    // Load Students & existing attendance
    async function loadStudents() {
        const classId = document.getElementById('classFilter').value;
        const date = document.getElementById('dateFilter').value;

        // Check lock status for the selected date
        if (date) {
            const holidays = await fetchHolidaysForDate(date);
            const lockStatus = isDateDisabled(date, holidays);
            applyLockState(lockStatus);
        }

        if (!classId) {
            document.getElementById('emptyState').classList.remove('hidden');
            document.getElementById('studentGrid').innerHTML = '';
            document.getElementById('submitBtn').disabled = true;
            updateStats();
            return;
        }

        document.getElementById('emptyState').classList.add('hidden');
        document.getElementById('loadingState').classList.remove('hidden');
        document.getElementById('studentGrid').innerHTML = '';

        try {
            // Fetch students for this class
            const resp = await fetch(`<?= base_url('api/admin/students') ?>?class_id=${classId}`, {
                credentials: 'include',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await resp.json();
            students = data.data || [];

            // Fetch existing attendance for this date & class
            attendanceData = {};
            existingSummaries = {};
            if (date) {
                try {
                    const attResp = await fetch(`<?= base_url('api/admin/attendance') ?>?class_id=${classId}&date=${date}`, {
                        credentials: 'include',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    const attData = await attResp.json();
                    if (attData.data) {
                        attData.data.forEach(rec => {
                            attendanceData[rec.student_id] = rec.status;
                            existingSummaries[rec.student_id] = rec.id;
                        });
                    }
                } catch (e) {
                    console.log('No existing attendance data');
                }
            }

            renderStudents(students);
            document.getElementById('submitBtn').disabled = false;
        } catch (err) {
            console.error('Gagal memuat siswa:', err);
            showToast('Gagal memuat data siswa', 'error');
        } finally {
            document.getElementById('loadingState').classList.add('hidden');
        }
    }

    // Render student cards
    function renderStudents(list) {
        const grid = document.getElementById('studentGrid');
        grid.innerHTML = '';

        if (list.length === 0) {
            grid.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <span class="material-symbols-outlined text-5xl text-gray-300">person_search</span>
                    <p class="text-gray-500 mt-3">Tidak ada siswa ditemukan</p>
                </div>
            `;
            updateStats();
            return;
        }

        list.forEach((student, idx) => {
            const status = attendanceData[student.id] || '';
            const initials = getInitials(student.name);
            const color = avatarColors[idx % avatarColors.length];
            const statusClass = status ? `status-${status}` : '';

            const card = document.createElement('div');
            card.className = `student-card ${statusClass}`;
            card.id = `card-${student.id}`;
            const isHadir = (status === 'hadir' || status === 'terlambat');
            const jamDatang = isHadir ? (student.check_in || '-') : '-';
            const jamPulang = isHadir ? (student.check_out || '-') : '-';

            card.innerHTML = `
                <div class="flex items-center gap-3 mb-4">
                    <div class="avatar" style="background:${color}">
                        ${initials}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-gray-900 text-sm truncate">${escHtml(student.name)}</h4>
                        <p class="text-xs text-gray-500">NIS: ${escHtml(student.nis)}</p>
                    </div>
                </div>
                <div class="jam-row" id="jam-row-${student.id}">
                    <div class="jam-item">
                        <label>Jam Datang</label>
                        <span class="jam-value ${jamDatang === '-' ? 'empty' : ''}">${jamDatang}</span>
                    </div>
                    <div class="jam-item">
                        <label>Jam Pulang</label>
                        <span class="jam-value ${jamPulang === '-' ? 'empty' : ''}">${jamPulang}</span>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="status-btn btn-hadir flex-1 ${status==='hadir'?'active':''}" onclick="setStatus(${student.id},'hadir')">Hadir</button>
                    <button class="status-btn btn-sakit flex-1 ${status==='sakit'?'active':''}" onclick="setStatus(${student.id},'sakit')">Sakit</button>
                    <button class="status-btn btn-izin flex-1 ${status==='izin'?'active':''}" onclick="setStatus(${student.id},'izin')">Izin</button>
                    <button class="status-btn btn-alpa flex-1 ${status==='alpha'?'active':''}" onclick="setStatus(${student.id},'alpha')">Alpa</button>
                </div>
            `;
            grid.appendChild(card);
        });

        updateStats();
    }

    // Set attendance status
    function setStatus(studentId, status) {
        // Toggle: if already selected, deselect
        if (attendanceData[studentId] === status) {
            delete attendanceData[studentId];
        } else {
            attendanceData[studentId] = status;
        }

        // Update card
        const card = document.getElementById(`card-${studentId}`);
        if (card) {
            card.className = `student-card ${attendanceData[studentId] ? 'status-' + attendanceData[studentId] : ''}`;
            card.querySelectorAll('.status-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            if (attendanceData[studentId]) {
                const activeBtn = card.querySelector(`.btn-${attendanceData[studentId]}`);
                if (activeBtn) activeBtn.classList.add('active');
            }

            // Update jam datang/pulang display
            const jamRow = document.getElementById(`jam-row-${studentId}`);
            if (jamRow) {
                const currentStatus = attendanceData[studentId] || '';
                const isHadir = (currentStatus === 'hadir' || currentStatus === 'terlambat');
                const student = students.find(s => s.id == studentId);
                const jamDatang = isHadir ? (student && student.check_in ? student.check_in : '-') : '-';
                const jamPulang = isHadir ? (student && student.check_out ? student.check_out : '-') : '-';

                const spans = jamRow.querySelectorAll('.jam-value');
                spans[0].textContent = jamDatang;
                spans[0].className = `jam-value ${jamDatang === '-' ? 'empty' : ''}`;
                spans[1].textContent = jamPulang;
                spans[1].className = `jam-value ${jamPulang === '-' ? 'empty' : ''}`;
            }
        }

        updateStats();
    }

    // Mark all students with a status
    function markAll(status) {
        students.forEach(s => {
            attendanceData[s.id] = status;
        });
        renderStudents(students);
    }

    // Update stats counters
    function updateStats() {
        const total = students.length;
        let hadir = 0,
            sakit = 0,
            izin = 0,
            alpa = 0;

        Object.values(attendanceData).forEach(status => {
            if (status === 'hadir') hadir++;
            else if (status === 'sakit') sakit++;
            else if (status === 'izin') izin++;
            else if (status === 'alpha') alpa++;
        });

        document.getElementById('statTotal').textContent = total;
        document.getElementById('statHadir').textContent = hadir;
        document.getElementById('statSakitIzin').textContent = sakit + izin;
        document.getElementById('statAlpa').textContent = alpa;
    }

    // Search / Filter students
    function filterStudents() {
        const query = document.getElementById('searchInput').value.toLowerCase().trim();
        if (!query) {
            renderStudents(students);
            return;
        }
        const filtered = students.filter(s =>
            s.name.toLowerCase().includes(query) ||
            s.nis.toLowerCase().includes(query)
        );
        renderStudents(filtered);
    }

    // Submit attendance
    async function submitAttendance() {
        if (isLocked) {
            showToast('Absensi dikunci untuk tanggal ini', 'error');
            return;
        }

        const date = document.getElementById('dateFilter').value;
        const classId = document.getElementById('classFilter').value;

        if (!classId || !date) {
            showToast('Pilih kelas dan tanggal terlebih dahulu', 'error');
            return;
        }

        // Check if any attendance is set
        const entries = Object.entries(attendanceData);
        if (entries.length === 0) {
            showToast('Belum ada data kehadiran yang diisi', 'error');
            return;
        }

        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Menyimpan...';

        try {
            const records = entries.map(([studentId, status]) => ({
                student_id: parseInt(studentId),
                status: status,
                id: existingSummaries[studentId] || null
            }));

            const resp = await fetch('<?= base_url('api/admin/attendance') ?>', {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    date: date,
                    class_id: classId,
                    records: records
                })
            });

            const result = await resp.json();

            if (resp.ok && result.success) {
                showToast(`Kehadiran berhasil disimpan (${entries.length} siswa)`, 'success');
                // Reload to get updated IDs
                loadStudents();
            } else {
                showToast(result.message || 'Gagal menyimpan kehadiran', 'error');
            }
        } catch (err) {
            console.error('Submit error:', err);
            showToast('Terjadi kesalahan saat menyimpan', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<span class="material-symbols-outlined text-xl">save</span> Simpan Kehadiran';
        }
    }

    // Helpers
    function getInitials(name) {
        const parts = name.trim().split(/\s+/);
        if (parts.length >= 2) return (parts[0][0] + parts[1][0]).toUpperCase();
        return name.substring(0, 2).toUpperCase();
    }

    function escHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const icon = document.getElementById('toastIcon');
        const msg = document.getElementById('toastMessage');

        msg.textContent = message;
        icon.textContent = type === 'success' ? 'check_circle' : 'error';
        toast.querySelector('div').className = `${type === 'success' ? 'bg-green-700' : 'bg-red-700'} text-white px-6 py-3 rounded-xl shadow-lg flex items-center gap-3`;

        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 3500);
    }
</script>
<?= $this->endSection() ?>