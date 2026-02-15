<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_admin') ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .cal-cell {
        position: relative;
        min-height: 52px;
        border-right: 1px solid #e5e7eb;
        border-bottom: 1px solid #e5e7eb;
        padding: 3px 2px;
        transition: all 0.15s ease;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .cal-cell:hover:not(.outside) {
        background: #f3f4f6;
        box-shadow: inset 0 0 0 1px #d1d5db;
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
        background: #eff6ff !important;
        box-shadow: inset 0 0 0 2px #3b82f6;
    }

    .cal-cell.holiday-school {
        background: #fefce8;
    }

    .cal-cell.holiday-national {
        background: #fee2e2;
    }

    .cal-date {
        font-size: 11px;
        font-weight: 600;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin-bottom: 1px;
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
        font-size: 7px;
        font-weight: 700;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
        text-align: center;
    }

    .cal-badge.national {
        background: #dc2626;
        color: white;
    }

    .cal-badge.school {
        background: #f59e0b;
        color: white;
    }

    .cal-badge.off-label {
        background: #9ca3af;
        color: white;
        font-size: 7px;
    }

    /* Tooltip */
    .cal-tooltip {
        position: absolute;
        bottom: calc(100% + 8px);
        left: 50%;
        transform: translateX(-50%);
        background: #1f2937;
        color: white;
        padding: 8px 12px;
        border-radius: 10px;
        font-size: 11px;
        white-space: nowrap;
        z-index: 40;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.2s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
    }

    .cal-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 5px solid transparent;
        border-top-color: #1f2937;
    }

    .cal-cell:hover .cal-tooltip {
        opacity: 1;
    }

    /* Holiday list sidebar */
    .holiday-list-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 10px 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .holiday-list-item:last-child {
        border-bottom: none;
    }

    .holiday-date-badge {
        min-width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        flex-shrink: 0;
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

    @media (max-width: 1024px) {
        .cal-layout {
            flex-direction: column;
        }

        .cal-sidebar {
            max-height: 300px;
        }
    }

    @media (max-width: 640px) {
        .cal-cell {
            min-height: 42px;
        }

        .cal-date {
            font-size: 9px;
            width: 18px;
            height: 18px;
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

<!-- Layout: Calendar Left + Holiday List Right -->
<div class="cal-layout flex gap-6">
    <!-- Calendar Card (Left) -->
    <div class="flex-1 min-w-0">
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

            <!-- Day Headers (ISO-8601: Mon-Sun) -->
            <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-200">
                <div class="py-2 text-center text-[10px] font-bold uppercase tracking-wider text-gray-500">Sen</div>
                <div class="py-2 text-center text-[10px] font-bold uppercase tracking-wider text-gray-500">Sel</div>
                <div class="py-2 text-center text-[10px] font-bold uppercase tracking-wider text-gray-500">Rab</div>
                <div class="py-2 text-center text-[10px] font-bold uppercase tracking-wider text-gray-500">Kam</div>
                <div class="py-2 text-center text-[10px] font-bold uppercase tracking-wider text-gray-500">Jum</div>
                <div class="py-2 text-center text-[10px] font-bold uppercase tracking-wider text-red-500">Sab</div>
                <div class="py-2 text-center text-[10px] font-bold uppercase tracking-wider text-red-500">Min</div>
            </div>

            <!-- Calendar Grid -->
            <div class="grid grid-cols-7" id="calendarGrid">
                <!-- Cells rendered by JS -->
            </div>

            <!-- Legend -->
            <div class="flex flex-wrap gap-3 px-4 py-2.5 bg-gray-50 border-t border-gray-200 text-[11px] text-gray-600">
                <span class="flex items-center gap-1.5">
                    <span class="inline-block w-3 h-3 rounded border-2 border-blue-500 bg-blue-50"></span> Hari Ini
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="inline-block w-3 h-3 rounded bg-red-100 border border-red-200"></span> Sabtu/Minggu
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="inline-block px-1.5 py-0.5 text-[8px] font-bold bg-red-600 text-white rounded">LIBUR</span> Nasional
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="inline-block px-1.5 py-0.5 text-[8px] font-bold bg-amber-500 text-white rounded">LIBUR</span> Sekolah
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="inline-block px-1.5 py-0.5 text-[8px] font-bold bg-gray-400 text-white rounded">OFF</span> Akhir Pekan
                </span>
            </div>
        </div>
    </div>

    <!-- Holiday List (Right Sidebar) -->
    <div class="cal-sidebar w-80 flex-shrink-0">
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 bg-gradient-to-r from-red-600 to-red-700">
                <h4 class="font-bold text-white text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">event_busy</span>
                    Hari Libur <span id="holidayListTitle" class="font-normal text-red-200 text-xs"></span>
                </h4>
            </div>
            <div class="px-4 py-3 max-h-[480px] overflow-y-auto" id="holidayListContainer">
                <div class="text-center text-gray-400 py-6 text-sm">
                    <span class="material-symbols-outlined text-3xl mb-2 block">calendar_month</span>
                    Memuat data libur...
                </div>
            </div>
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
    let allNationalHolidays = [];

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
        renderHolidayList();
    }

    async function fetchSchoolHolidays() {
        try {
            const resp = await fetch(`<?= base_url('api/admin/school-holidays') ?>?year=${currentYear}&month=${currentMonth + 1}`, {
                credentials: 'include',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            const data = await resp.json();
            holidays = data.data || [];
        } catch (e) {
            holidays = [];
        }
    }

    // Cache API response per year
    let apiCache = {};
    const bulanNames = ['januari','februari','maret','april','mei','juni','juli','agustus','september','oktober','november','desember'];

    /**
     * Parse tanggal field from harikerja API (handles "1", "21-22", etc.)
     */
    function parseTanggalRange(tanggal, year, monthIdx) {
        const results = [];
        const parts = String(tanggal).split('-');
        if (parts.length === 2) {
            const start = parseInt(parts[0]);
            const end = parseInt(parts[1]);
            for (let d = start; d <= end; d++) results.push(d);
        } else {
            results.push(parseInt(parts[0]));
        }
        return results.filter(d => !isNaN(d)).map(d => {
            const mm = String(monthIdx + 1).padStart(2, '0');
            const dd = String(d).padStart(2, '0');
            return `${year}-${mm}-${dd}`;
        });
    }

    async function fetchNationalHolidays() {
        try {
            // Use cache if available for this year
            if (!apiCache[currentYear]) {
                const resp = await fetch('https://harikerja.vercel.app/api');
                if (!resp.ok) throw new Error('API error');
                const json = await resp.json();
                if (json.code !== 200 || !json.data) throw new Error('Invalid response');
                apiCache[currentYear] = json.data;
            }

            const monthData = apiCache[currentYear];
            const bulanKey = bulanNames[currentMonth];
            const found = monthData.find(m => m.bulan === bulanKey);

            if (found && found.tanggal_merah && found.tanggal_merah.length > 0) {
                const holidays = [];
                found.tanggal_merah.forEach(tm => {
                    const dates = parseTanggalRange(tm.tanggal, currentYear, currentMonth);
                    dates.forEach(dateStr => {
                        holidays.push({
                            date: dateStr,
                            name: tm.memperingati,
                            isNational: true
                        });
                    });
                });
                allNationalHolidays = holidays;
                nationalHolidays = holidays;
            } else {
                allNationalHolidays = [];
                nationalHolidays = [];
            }
        } catch (e) {
            console.warn('API harikerja gagal, pakai data fallback:', e.message);
            const fallback = getHolidaysFallback(currentYear, currentMonth + 1);
            allNationalHolidays = fallback;
            nationalHolidays = fallback;
        }
    }

    // Fallback hardcoded holidays (jika API down)
    const HOLIDAYS_FALLBACK = {
        '2025': [
            {date:'2025-01-01',name:'Tahun Baru Masehi',isNational:true},
            {date:'2025-01-27',name:'Isra Mikraj Nabi Muhammad SAW',isNational:true},
            {date:'2025-01-29',name:'Tahun Baru Imlek 2576',isNational:true},
            {date:'2025-03-14',name:'Hari Suci Nyepi',isNational:true},
            {date:'2025-03-29',name:'Idul Fitri 1446 H',isNational:true},
            {date:'2025-03-30',name:'Idul Fitri 1446 H',isNational:true},
            {date:'2025-04-18',name:'Wafat Isa Al Masih',isNational:true},
            {date:'2025-05-01',name:'Hari Buruh',isNational:true},
            {date:'2025-05-12',name:'Waisak 2569 BE',isNational:true},
            {date:'2025-05-29',name:'Kenaikan Isa Al Masih',isNational:true},
            {date:'2025-06-01',name:'Hari Lahir Pancasila',isNational:true},
            {date:'2025-06-06',name:'Idul Adha 1446 H',isNational:true},
            {date:'2025-06-27',name:'Tahun Baru Islam 1447 H',isNational:true},
            {date:'2025-08-17',name:'Hari Kemerdekaan RI',isNational:true},
            {date:'2025-09-05',name:'Maulid Nabi Muhammad SAW',isNational:true},
            {date:'2025-12-25',name:'Hari Raya Natal',isNational:true}
        ],
        '2026': [
            {date:'2026-01-01',name:'Tahun Baru Masehi',isNational:true},
            {date:'2026-01-16',name:'Isra Mikraj Nabi Muhammad SAW',isNational:true},
            {date:'2026-02-16',name:'Tahun Baru Imlek 2577',isNational:true},
            {date:'2026-02-17',name:'Tahun Baru Imlek 2577',isNational:true},
            {date:'2026-03-18',name:'Hari Suci Nyepi',isNational:true},
            {date:'2026-03-19',name:'Hari Suci Nyepi',isNational:true},
            {date:'2026-03-20',name:'Idul Fitri 1447 H',isNational:true},
            {date:'2026-03-21',name:'Idul Fitri 1447 H',isNational:true},
            {date:'2026-03-22',name:'Idul Fitri 1447 H',isNational:true},
            {date:'2026-03-23',name:'Idul Fitri 1447 H',isNational:true},
            {date:'2026-03-24',name:'Idul Fitri 1447 H',isNational:true},
            {date:'2026-04-03',name:'Wafat Yesus Kristus',isNational:true},
            {date:'2026-04-05',name:'Kebangkitan Yesus Kristus',isNational:true},
            {date:'2026-05-01',name:'Hari Buruh',isNational:true},
            {date:'2026-05-14',name:'Kenaikan Yesus Kristus',isNational:true},
            {date:'2026-05-15',name:'Kenaikan Yesus Kristus',isNational:true},
            {date:'2026-05-27',name:'Idul Adha 1447 H',isNational:true},
            {date:'2026-05-28',name:'Idul Adha 1447 H',isNational:true},
            {date:'2026-05-31',name:'Waisak 2570 BE',isNational:true},
            {date:'2026-06-01',name:'Hari Lahir Pancasila',isNational:true},
            {date:'2026-06-16',name:'Tahun Baru Islam 1448 H',isNational:true},
            {date:'2026-08-17',name:'Hari Kemerdekaan RI',isNational:true},
            {date:'2026-08-25',name:'Maulid Nabi Muhammad SAW',isNational:true},
            {date:'2026-12-25',name:'Hari Raya Natal',isNational:true}
        ],
        '2027': [
            {date:'2027-01-01',name:'Tahun Baru Masehi',isNational:true},
            {date:'2027-02-06',name:'Tahun Baru Imlek 2578',isNational:true},
            {date:'2027-03-10',name:'Idul Fitri 1448 H',isNational:true},
            {date:'2027-03-11',name:'Idul Fitri 1448 H',isNational:true},
            {date:'2027-03-22',name:'Hari Suci Nyepi',isNational:true},
            {date:'2027-03-26',name:'Wafat Isa Al Masih',isNational:true},
            {date:'2027-05-01',name:'Hari Buruh',isNational:true},
            {date:'2027-05-06',name:'Kenaikan Isa Al Masih',isNational:true},
            {date:'2027-05-17',name:'Idul Adha 1448 H',isNational:true},
            {date:'2027-06-01',name:'Hari Lahir Pancasila',isNational:true},
            {date:'2027-06-07',name:'Tahun Baru Islam 1449 H',isNational:true},
            {date:'2027-08-17',name:'Hari Kemerdekaan RI',isNational:true},
            {date:'2027-12-25',name:'Hari Raya Natal',isNational:true}
        ]
    };

    function getHolidaysFallback(year, month) {
        const yearData = HOLIDAYS_FALLBACK[String(year)] || [];
        const mm = String(month).padStart(2, '0');
        return yearData.filter(h => h.date.substring(5, 7) === mm);
    }

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

    // ISO-8601: Monday=0 ... Sunday=6
    function isoDay(jsDay) {
        return (jsDay + 6) % 7;
    }

    function renderCalendar() {
        document.getElementById('calendarTitle').textContent = `${monthNames[currentMonth]} ${currentYear}`;
        const grid = document.getElementById('calendarGrid');
        grid.innerHTML = '';

        const firstDay = new Date(currentYear, currentMonth, 1);
        const lastDay = new Date(currentYear, currentMonth + 1, 0);
        const startDay = isoDay(firstDay.getDay());
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
                    badge.textContent = 'LIBUR';
                    cell.appendChild(badge);

                    const tooltip = document.createElement('div');
                    tooltip.className = 'cal-tooltip';
                    tooltip.innerHTML = `<span style="color:#fca5a5;">&#9679;</span> ${escHtml(natHoliday.name)}`;
                    cell.appendChild(tooltip);
                } else if (schHoliday) {
                    cell.classList.add('holiday-school');
                    const badge = document.createElement('span');
                    badge.className = 'cal-badge school';
                    badge.textContent = 'LIBUR';
                    cell.appendChild(badge);

                    const tooltip = document.createElement('div');
                    tooltip.className = 'cal-tooltip';
                    tooltip.innerHTML = `<span style="color:#fcd34d;">&#9679;</span> ${escHtml(schHoliday.name)}`;
                    cell.appendChild(tooltip);
                } else if (isWeekend) {
                    const badge = document.createElement('span');
                    badge.className = 'cal-badge off-label';
                    badge.textContent = 'OFF';
                    cell.appendChild(badge);
                }

                cell.onclick = () => openModal(dateStr, date);
            }

            grid.appendChild(cell);
        }
    }

    function renderHolidayList() {
        const container = document.getElementById('holidayListContainer');
        document.getElementById('holidayListTitle').textContent = `- ${monthNames[currentMonth]} ${currentYear}`;

        const allHolidays = [];

        nationalHolidays.forEach(h => {
            allHolidays.push({
                date: h.date,
                name: h.name,
                type: h.isNational ? 'national' : 'cuti',
                label: h.isNational ? 'Libur Nasional' : 'Cuti Bersama'
            });
        });

        holidays.forEach(h => {
            if (!allHolidays.find(x => x.date === h.date)) {
                allHolidays.push({
                    date: h.date,
                    name: h.name,
                    type: 'school',
                    label: 'Libur Sekolah'
                });
            }
        });

        allHolidays.sort((a, b) => a.date.localeCompare(b.date));

        if (allHolidays.length === 0) {
            container.innerHTML = `
                <div class="text-center text-gray-400 py-8 text-sm">
                    <span class="material-symbols-outlined text-3xl mb-2 block text-green-300">check_circle</span>
                    <p class="text-gray-500">Tidak ada hari libur</p>
                    <p class="text-gray-400 text-xs mt-1">Bulan ini tidak ada libur nasional</p>
                </div>`;
            return;
        }

        let html = '';
        allHolidays.forEach(h => {
            const d = new Date(h.date);
            const day = d.getDate();
            const dayName = dayNames[d.getDay()];
            const isNat = h.type === 'national';
            const isCuti = h.type === 'cuti';

            const bgColor = isNat ? 'bg-red-100 text-red-700' : (isCuti ? 'bg-orange-100 text-orange-700' : 'bg-amber-100 text-amber-700');
            const tagBg = isNat ? 'bg-red-100 text-red-600' : (isCuti ? 'bg-orange-100 text-orange-600' : 'bg-amber-100 text-amber-600');

            html += `
                <div class="holiday-list-item">
                    <div class="holiday-date-badge ${bgColor}">
                        ${day}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 text-sm leading-tight">${escHtml(h.name)}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs ${tagBg} px-2 py-0.5 rounded-full font-medium">${h.label}</span>
                            <span class="text-xs text-gray-400">${dayName}</span>
                        </div>
                    </div>
                </div>`;
        });

        container.innerHTML = html;
    }

    function prevMonth() {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        loadCalendar();
    }

    function nextMonth() {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        loadCalendar();
    }

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

        document.getElementById('weekendInfo').classList.toggle('hidden', !isWeekend);

        if (natHoliday) {
            document.getElementById('nationalHolidayInfo').classList.remove('hidden');
            document.getElementById('nationalHolidayName').textContent = natHoliday.name;
        } else {
            document.getElementById('nationalHolidayInfo').classList.add('hidden');
        }

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
                renderHolidayList();
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

    function escHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
<?= $this->endSection() ?>