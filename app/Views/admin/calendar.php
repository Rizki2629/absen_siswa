<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_admin') ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .cal-wrapper {
        max-width: 56rem;
    }

    .cal-cell {
        position: relative;
        aspect-ratio: 1 / 1;
        border-right: 1px solid #e5e7eb;
        border-bottom: 1px solid #e5e7eb;
        padding: 4px;
        transition: all 0.15s ease;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .cal-cell:hover {
        background: #f3f4f6;
    }

    .cal-cell:nth-child(7n) {
        border-right: none;
    }

    .cal-cell.outside {
        background: #fafafa;
        cursor: default;
    }

    .cal-cell.outside .cal-date {
        color: #d1d5db;
    }

    .cal-cell.weekend {
        background: #fef2f2;
    }

    .cal-cell.weekend.outside {
        background: #fdf5f5;
    }

    .cal-cell.today {
        background: #eff6ff;
        box-shadow: inset 0 0 0 2px #3b82f6;
    }

    .cal-cell.holiday-school {
        background: #fefce8;
    }

    .cal-cell.holiday-national {
        background: #fef2f2;
    }

    .cal-date {
        font-size: 12px;
        font-weight: 600;
        width: 26px;
        height: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin-bottom: 2px;
    }

    .cal-cell.today .cal-date {
        background: #3b82f6;
        color: white !important;
    }

    .cal-date.weekend-text {
        color: #ef4444;
    }

    .cal-badge {
        padding: 1px 4px;
        border-radius: 3px;
        font-size: 8px;
        font-weight: 600;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
        text-align: center;
    }

    .cal-badge.national {
        background: #fecaca;
        color: #991b1b;
    }

    .cal-badge.school {
        background: #fde68a;
        color: #92400e;
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 50;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .modal-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .modal-content {
        background: white;
        border-radius: 16px;
        width: 100%;
        max-width: 440px;
        padding: 0;
        transform: scale(0.9);
        transition: transform 0.3s ease;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }

    .modal-overlay.active .modal-content {
        transform: scale(1);
    }

    .toggle-switch {
        position: relative;
        width: 48px;
        height: 26px;
        background: #d1d5db;
        border-radius: 13px;
        cursor: pointer;
        transition: background 0.3s ease;
        flex-shrink: 0;
        border: none;
    }

    .toggle-switch.active {
        background: #f59e0b;
    }

    .toggle-switch::after {
        content: '';
        position: absolute;
        top: 3px;
        left: 3px;
        width: 20px;
        height: 20px;
        background: white;
        border-radius: 50%;
        transition: transform 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    .toggle-switch.active::after {
        transform: translateX(22px);
    }

    @media (max-width: 640px) {
        .cal-date {
            font-size: 10px;
            width: 22px;
            height: 22px;
        }

        .cal-badge {
            font-size: 6px;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Kalender Akademik</h2>
        <p class="text-gray-600 mt-1">Lihat dan atur hari libur sekolah</p>
    </div>
</div>

<!-- Calendar Card -->
<div class="cal-wrapper mx-auto">
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <!-- Month/Year Navigation -->
        <div class="flex items-center justify-between px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-700">
            <button onclick="prevMonth()" class="p-1.5 rounded-lg hover:bg-white/20 transition-colors text-white">
                <span class="material-symbols-outlined text-xl">chevron_left</span>
            </button>
            <h3 class="text-lg font-bold text-white" id="calendarTitle">Februari 2026</h3>
            <button onclick="nextMonth()" class="p-1.5 rounded-lg hover:bg-white/20 transition-colors text-white">
                <span class="material-symbols-outlined text-xl">chevron_right</span>
            </button>
        </div>

        <!-- Legend -->
        <div class="flex flex-wrap gap-3 px-4 py-2 bg-gray-50 border-b border-gray-200 text-xs text-gray-600">
            <span class="flex items-center gap-1">
                <span class="inline-block w-3 h-3 rounded border-2 border-blue-500 bg-blue-50"></span> Hari Ini
            </span>
            <span class="flex items-center gap-1">
                <span class="inline-block w-3 h-3 rounded bg-red-100"></span> Sabtu/Minggu
            </span>
            <span class="flex items-center gap-1">
                <span class="cal-badge national" style="font-size:9px;">Libur</span> Nasional
            </span>
            <span class="flex items-center gap-1">
                <span class="cal-badge school" style="font-size:9px;">Libur</span> Sekolah
            </span>
        </div>

        <!-- Day Headers -->
        <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-200">
            <div class="py-2 text-center text-[10px] font-bold uppercase tracking-wider text-red-500">Min</div>
            <div class="py-2 text-center text-[10px] font-bold uppercase tracking-wider text-gray-500">Sen</div>
            <div class="py-2 text-center text-[10px] font-bold uppercase tracking-wider text-gray-500">Sel</div>
            <div class="py-2 text-center text-[10px] font-bold uppercase tracking-wider text-gray-500">Rab</div>
            <div class="py-2 text-center text-[10px] font-bold uppercase tracking-wider text-gray-500">Kam</div>
            <div class="py-2 text-center text-[10px] font-bold uppercase tracking-wider text-gray-500">Jum</div>
            <div class="py-2 text-center text-[10px] font-bold uppercase tracking-wider text-red-500">Sab</div>
        </div>

        <!-- Calendar Grid -->
        <div class="grid grid-cols-7" id="calendarGrid">
            <!-- Cells rendered by JS -->
        </div>
    </div>
</div>

<!-- Modal: Holiday Setting -->
<div class="modal-overlay" id="holidayModal">
    <div class="modal-content">
        <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-4">
            <h3 class="text-lg font-bold text-white" id="modalDate">Minggu, 15 Februari 2026</h3>
            <p class="text-primary-100 text-sm mt-0.5" id="modalSubtitle">Pengaturan hari libur</p>
        </div>

        <div class="px-6 py-5">
            <!-- Weekend info -->
            <div id="weekendInfo" class="hidden mb-3 bg-red-50 border border-red-200 rounded-xl p-3">
                <div class="flex items-center gap-2 text-red-700">
                    <span class="material-symbols-outlined text-lg">weekend</span>
                    <div>
                        <p class="font-semibold text-sm">Libur Akhir Pekan</p>
                        <p class="text-xs mt-0.5">Absensi otomatis dikunci</p>
                    </div>
                </div>
            </div>

            <!-- National holiday info -->
            <div id="nationalHolidayInfo" class="hidden mb-3 bg-red-50 border border-red-200 rounded-xl p-3">
                <div class="flex items-center gap-2 text-red-700">
                    <span class="material-symbols-outlined text-lg">event_busy</span>
                    <div>
                        <p class="font-semibold text-sm">Libur Nasional</p>
                        <p class="text-xs mt-0.5" id="nationalHolidayName"></p>
                    </div>
                </div>
            </div>

            <!-- Toggle school holiday -->
            <div class="flex items-center justify-between mb-4 p-3 bg-gray-50 rounded-xl">
                <div>
                    <p class="font-semibold text-gray-900 text-sm" id="toggleLabel">Sekolah Aktif</p>
                    <p class="text-xs text-gray-500 mt-0.5">Klik toggle untuk mengubah status</p>
                </div>
                <div class="toggle-switch" id="holidayToggle" onclick="toggleHoliday()"></div>
            </div>

            <!-- Reason textarea -->
            <div id="reasonSection" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan Libur</label>
                <textarea id="holidayReason" rows="2" placeholder="Contoh: Pembagian Rapor, Libur Semester..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm resize-none"></textarea>
            </div>

            <!-- Lock alert -->
            <div id="lockAlert" class="hidden mt-4 bg-amber-50 border border-amber-200 rounded-xl p-3">
                <div class="flex items-center gap-2 text-amber-800">
                    <span class="material-symbols-outlined text-lg">lock</span>
                    <p class="text-xs font-medium" id="lockAlertText">Absensi dikunci karena hari libur</p>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
            <button onclick="closeModal()" class="px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 rounded-xl transition-colors">
                Batal
            </button>
            <button onclick="saveHoliday()" id="modalSaveBtn" class="px-5 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-xl transition-colors shadow-sm">
                <span class="material-symbols-outlined text-sm align-middle mr-1">save</span>
                Simpan
            </button>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // State
    let currentYear = new Date().getFullYear();
    let currentMonth = new Date().getMonth();
    let selectedDate = null;
    let holidays = [];
    let nationalHolidays = [];

    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    document.addEventListener('DOMContentLoaded', () => {
        loadCalendar();
    });

    async function loadCalendar() {
        await Promise.all([
            fetchSchoolHolidays(),
            fetchNationalHolidays()
        ]);
        renderCalendar();
    }

    async function fetchSchoolHolidays() {
        try {
            const resp = await fetch(`<?= base_url('api/admin/school-holidays') ?>?year=${currentYear}&month=${currentMonth + 1}`, {
                credentials: 'include',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await resp.json();
            holidays = data.data || [];
        } catch (e) {
            holidays = [];
        }
    }

    async function fetchNationalHolidays() {
        try {
            const resp = await fetch(`https://api-harilibur.vercel.app/api?year=${currentYear}&month=${currentMonth + 1}`);
            const data = await resp.json();
            nationalHolidays = (data || [])
                .filter(h => h.is_national_holiday)
                .map(h => ({ date: h.holiday_date, name: h.holiday_name }));
        } catch (e) {
            nationalHolidays = [];
        }
    }

    /**
     * Check if a date is disabled/locked for attendance
     */
    function isDateDisabled(dateStr) {
        const d = new Date(dateStr);
        const day = d.getDay();
        const isWeekend = (day === 0 || day === 6);
        const natHoliday = nationalHolidays.find(h => h.date === dateStr);
        const schHoliday = holidays.find(h => h.date === dateStr);

        if (isWeekend || natHoliday || schHoliday) {
            let reason = 'Libur Akhir Pekan';
            if (natHoliday) reason = natHoliday.name;
            else if (schHoliday) reason = schHoliday.name;
            return { locked: true, reason: reason };
        }
        return { locked: false, reason: null };
    }

    function renderCalendar() {
        document.getElementById('calendarTitle').textContent = `${monthNames[currentMonth]} ${currentYear}`;
        const grid = document.getElementById('calendarGrid');
        grid.innerHTML = '';

        const firstDay = new Date(currentYear, currentMonth, 1);
        const lastDay = new Date(currentYear, currentMonth + 1, 0);
        const startDay = firstDay.getDay();
        const daysInMonth = lastDay.getDate();
        const prevLastDay = new Date(currentYear, currentMonth, 0);
        const prevDays = prevLastDay.getDate();

        const today = new Date();
        const todayStr = fmtDate(today);

        const totalCells = Math.ceil((startDay + daysInMonth) / 7) * 7;

        for (let i = 0; i < totalCells; i++) {
            const cell = document.createElement('div');
            cell.className = 'cal-cell';

            let date, dateStr, isOutside = false;

            if (i < startDay) {
                const d = prevDays - startDay + i + 1;
                const m = currentMonth === 0 ? 11 : currentMonth - 1;
                const y = currentMonth === 0 ? currentYear - 1 : currentYear;
                date = new Date(y, m, d);
                dateStr = fmtDate(date);
                isOutside = true;
                cell.classList.add('outside');
            } else if (i >= startDay + daysInMonth) {
                const d = i - startDay - daysInMonth + 1;
                const m = currentMonth === 11 ? 0 : currentMonth + 1;
                const y = currentMonth === 11 ? currentYear + 1 : currentYear;
                date = new Date(y, m, d);
                dateStr = fmtDate(date);
                isOutside = true;
                cell.classList.add('outside');
            } else {
                const d = i - startDay + 1;
                date = new Date(currentYear, currentMonth, d);
                dateStr = fmtDate(date);
            }

            const dayOfWeek = date.getDay();
            const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);

            if (isWeekend) cell.classList.add('weekend');
            if (dateStr === todayStr) cell.classList.add('today');

            // Date number
            const dateEl = document.createElement('div');
            dateEl.className = 'cal-date' + (isWeekend && !isOutside ? ' weekend-text' : '');
            dateEl.textContent = date.getDate();
            cell.appendChild(dateEl);

            if (!isOutside) {
                const natHoliday = nationalHolidays.find(h => h.date === dateStr);
                const schHoliday = holidays.find(h => h.date === dateStr);

                if (natHoliday) {
                    cell.classList.add('holiday-national');
                    const badge = document.createElement('span');
                    badge.className = 'cal-badge national';
                    badge.textContent = natHoliday.name;
                    badge.title = natHoliday.name;
                    cell.appendChild(badge);
                }

                if (schHoliday) {
                    cell.classList.add('holiday-school');
                    const badge = document.createElement('span');
                    badge.className = 'cal-badge school';
                    badge.textContent = schHoliday.name;
                    badge.title = schHoliday.name;
                    cell.appendChild(badge);
                }

                cell.onclick = () => openModal(dateStr, date);
            }

            grid.appendChild(cell);
        }
    }

    function prevMonth() {
        currentMonth--;
        if (currentMonth < 0) { currentMonth = 11; currentYear--; }
        loadCalendar();
    }

    function nextMonth() {
        currentMonth++;
        if (currentMonth > 11) { currentMonth = 0; currentYear++; }
        loadCalendar();
    }

    // Modal
    function openModal(dateStr, dateObj) {
        selectedDate = dateStr;

        const dayName = dayNames[dateObj.getDay()];
        const day = dateObj.getDate();
        const month = monthNames[dateObj.getMonth()];
        const year = dateObj.getFullYear();
        document.getElementById('modalDate').textContent = `${dayName}, ${String(day).padStart(2, '0')} ${month} ${year}`;

        const dayOfWeek = dateObj.getDay();
        const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);
        const natHoliday = nationalHolidays.find(h => h.date === dateStr);
        const schHoliday = holidays.find(h => h.date === dateStr);

        // Weekend info
        document.getElementById('weekendInfo').classList.toggle('hidden', !isWeekend);

        // National holiday info
        if (natHoliday) {
            document.getElementById('nationalHolidayInfo').classList.remove('hidden');
            document.getElementById('nationalHolidayName').textContent = natHoliday.name;
        } else {
            document.getElementById('nationalHolidayInfo').classList.add('hidden');
        }

        // Toggle state
        const toggle = document.getElementById('holidayToggle');
        const reasonSection = document.getElementById('reasonSection');
        const reasonInput = document.getElementById('holidayReason');
        const toggleLabel = document.getElementById('toggleLabel');

        if (schHoliday) {
            toggle.classList.add('active');
            toggleLabel.textContent = 'Libur Sekolah';
            reasonSection.classList.remove('hidden');
            reasonInput.value = schHoliday.name || '';
        } else {
            toggle.classList.remove('active');
            toggleLabel.textContent = 'Sekolah Aktif';
            reasonSection.classList.add('hidden');
            reasonInput.value = '';
        }

        // Lock alert
        const lockStatus = isDateDisabled(dateStr);
        const lockAlert = document.getElementById('lockAlert');
        if (lockStatus.locked) {
            lockAlert.classList.remove('hidden');
            document.getElementById('lockAlertText').textContent = `Absensi dikunci karena: ${lockStatus.reason}`;
        } else {
            lockAlert.classList.add('hidden');
        }

        document.getElementById('holidayModal').classList.add('active');
    }

    function closeModal() {
        document.getElementById('holidayModal').classList.remove('active');
        selectedDate = null;
    }

    function toggleHoliday() {
        const toggle = document.getElementById('holidayToggle');
        const reasonSection = document.getElementById('reasonSection');
        const toggleLabel = document.getElementById('toggleLabel');
        toggle.classList.toggle('active');

        if (toggle.classList.contains('active')) {
            toggleLabel.textContent = 'Libur Sekolah';
            reasonSection.classList.remove('hidden');
        } else {
            toggleLabel.textContent = 'Sekolah Aktif';
            reasonSection.classList.add('hidden');
        }
    }

    async function saveHoliday() {
        if (!selectedDate) return;
        const toggle = document.getElementById('holidayToggle');
        const isHoliday = toggle.classList.contains('active');
        const reason = document.getElementById('holidayReason').value.trim();

        try {
            const resp = await fetch('<?= base_url('api/admin/school-holidays') ?>', {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    date: selectedDate,
                    name: reason || 'Libur Sekolah',
                    is_holiday: isHoliday
                })
            });
            const data = await resp.json();
            if (data.success) {
                closeModal();
                await fetchSchoolHolidays();
                renderCalendar();
            } else {
                alert(data.message || 'Gagal menyimpan');
            }
        } catch (e) {
            alert('Terjadi kesalahan');
        }
    }

    document.getElementById('holidayModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });

    function fmtDate(d) {
        return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
    }
</script>
<?= $this->endSection() ?>
