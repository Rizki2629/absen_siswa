<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_admin') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    #dateFilter {
        opacity: 0;
        position: absolute;
        z-index: 10;
    }

    #dateLabel {
        cursor: pointer;
        z-index: 5;
    }
</style>

<!-- Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Rekap Harian - 7 Kebiasaan</h2>
        <p class="text-gray-600 mt-1">Lihat kebiasaan harian seluruh siswa per kelas</p>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-2xl shadow p-4 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kelas</label>
            <select id="classFilter" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
                <option value="">-- Pilih Kelas --</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
            <div class="flex items-center space-x-2">
                <button type="button" onclick="changeDate(-1)" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors" title="Hari sebelumnya">
                    <span class="material-symbols-outlined">chevron_left</span>
                </button>
                <div class="flex-1 relative">
                    <input type="date" id="dateFilter" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
                    <div id="dateLabel" class="absolute inset-0 px-4 py-2 bg-white rounded-xl border border-gray-300 flex items-center justify-between">
                        <span id="dateLabelText" class="text-gray-900 font-medium"></span>
                        <span class="material-symbols-outlined text-gray-400">calendar_today</span>
                    </div>
                </div>
                <button type="button" onclick="changeDate(1)" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors" title="Hari berikutnya">
                    <span class="material-symbols-outlined">chevron_right</span>
                </button>
                <button type="button" onclick="setToday()" class="px-3 py-2 bg-primary-100 text-primary-700 rounded-lg hover:bg-primary-200 transition-colors font-medium" title="Kembali ke hari ini">
                    Hari Ini
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Daily Table -->
<div id="dailyContainer" class="bg-white rounded-2xl shadow overflow-hidden" style="display: none;">
    <div class="px-6 py-4 border-b border-gray-200">
        <div>
            <h3 class="text-lg font-bold text-gray-900" id="dailyTitle">Rekap Harian</h3>
            <p class="text-sm text-gray-500" id="dailySubtitle"></p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-primary-600 text-white">
                    <th class="py-3 px-3 text-center font-semibold w-12">No</th>
                    <th class="py-3 px-3 text-left font-semibold whitespace-nowrap">Nama Siswa</th>
                    <th class="py-2 px-2 text-center font-medium text-xs">Bangun<br>Pagi</th>
                    <th class="py-2 px-2 text-center font-medium text-xs">Beribadah</th>
                    <th class="py-2 px-2 text-center font-medium text-xs">Berolahraga</th>
                    <th class="py-2 px-2 text-center font-medium text-xs">Makan<br>Sehat</th>
                    <th class="py-2 px-2 text-center font-medium text-xs">Gemar<br>Belajar</th>
                    <th class="py-2 px-2 text-center font-medium text-xs">Bermasya-<br>rakat</th>
                    <th class="py-2 px-2 text-center font-medium text-xs">Tidur<br>Cepat</th>
                    <th class="py-3 px-3 text-center font-semibold w-16">%</th>
                </tr>
            </thead>
            <tbody id="dailyTableBody">
            </tbody>
        </table>
    </div>
</div>

<!-- Empty State -->
<div id="emptyState" class="bg-white rounded-2xl shadow p-12 text-center">
    <span class="material-symbols-outlined text-6xl text-primary-300 mb-4">today</span>
    <h3 class="text-lg font-semibold text-gray-700 mb-2">Pilih Kelas dan Tanggal</h3>
    <p class="text-gray-500">Pilih kelas dan tanggal untuk menampilkan rekap harian 7 kebiasaan</p>
</div>

<script>
    const habitColumns = ['bangun_pagi', 'beribadah', 'berolahraga', 'makan_sehat', 'gemar_belajar', 'bermasyarakat', 'tidur_cepat'];
    let currentStudents = [];
    let currentHabits = {};

    document.addEventListener('DOMContentLoaded', function() {
        loadClasses();

        // Set today as default date
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        document.getElementById('dateFilter').value = `${yyyy}-${mm}-${dd}`;
        updateDateLabel();

        document.getElementById('classFilter').addEventListener('change', loadDaily);
        document.getElementById('dateFilter').addEventListener('change', function() {
            updateDateLabel();
            loadDaily();
        });

        // Click on date label to open date picker
        document.getElementById('dateLabel').addEventListener('click', function() {
            document.getElementById('dateFilter').focus();
            try {
                document.getElementById('dateFilter').showPicker();
            } catch (e) {
                // showPicker not supported, fallback to focus
            }
        });
    });

    function updateDateLabel() {
        const dateInput = document.getElementById('dateFilter');
        const dateValue = dateInput.value;
        if (!dateValue) return;

        const dateObj = new Date(dateValue + 'T00:00:00');
        const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        const dayName = dayNames[dateObj.getDay()];
        const day = dateObj.getDate();
        const monthName = monthNames[dateObj.getMonth()];
        const year = dateObj.getFullYear();

        document.getElementById('dateLabelText').textContent = `${dayName}, ${day} ${monthName} ${year}`;
    }

    function changeDate(days) {
        const dateInput = document.getElementById('dateFilter');
        const currentDate = new Date(dateInput.value + 'T00:00:00');
        currentDate.setDate(currentDate.getDate() + days);

        const yyyy = currentDate.getFullYear();
        const mm = String(currentDate.getMonth() + 1).padStart(2, '0');
        const dd = String(currentDate.getDate()).padStart(2, '0');

        dateInput.value = `${yyyy}-${mm}-${dd}`;
        updateDateLabel();
        loadDaily();
    }

    function setToday() {
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');

        document.getElementById('dateFilter').value = `${yyyy}-${mm}-${dd}`;
        updateDateLabel();
        loadDaily();
    }

    async function loadClasses() {
        try {
            const response = await fetch('<?= base_url('api/admin/classes') ?>', {
                credentials: 'same-origin'
            });
            const data = await response.json();
            if (data.status === 'success') {
                const select = document.getElementById('classFilter');
                data.data.forEach(cls => {
                    const opt = document.createElement('option');
                    opt.value = cls.id;
                    opt.textContent = cls.name;
                    select.appendChild(opt);
                });
            }
        } catch (error) {
            console.error('Error loading classes:', error);
        }
    }

    async function loadDaily() {
        const classId = document.getElementById('classFilter').value;
        const date = document.getElementById('dateFilter').value;

        if (!classId || !date) {
            document.getElementById('dailyContainer').style.display = 'none';
            document.getElementById('emptyState').style.display = 'block';
            return;
        }

        document.getElementById('emptyState').style.display = 'none';
        document.getElementById('dailyContainer').style.display = 'block';

        // Extract month/year from date for API
        const [year, month] = date.split('-');

        try {
            const response = await fetch(`<?= base_url('api/admin/habits') ?>?class_id=${classId}&month=${parseInt(month)}&year=${year}`, {
                credentials: 'same-origin'
            });
            const data = await response.json();

            if (data.status === 'success') {
                currentStudents = data.data.students;
                currentHabits = data.data.habits;

                const className = data.data.class ? data.data.class.name : '';
                const dateObj = new Date(date + 'T00:00:00');
                const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                const dayName = dayNames[dateObj.getDay()];
                const dayNum = dateObj.getDate();
                const monthName = monthNames[dateObj.getMonth()];

                document.getElementById('dailyTitle').textContent = `Rekap Harian - ${className}`;
                document.getElementById('dailySubtitle').textContent = `${dayName}, ${dayNum} ${monthName} ${year} | ${currentStudents.length} siswa`;

                renderDailyTable(date);
            }
        } catch (error) {
            console.error('Error loading daily data:', error);
        }
    }

    function renderDailyTable(date) {
        const tbody = document.getElementById('dailyTableBody');

        if (!currentStudents || currentStudents.length === 0) {
            tbody.innerHTML = '<tr><td colspan="10" class="text-center py-8 text-gray-500">Tidak ada siswa di kelas ini</td></tr>';
            return;
        }

        tbody.innerHTML = currentStudents.map((student, idx) => {
            const habitData = (currentHabits[student.id] && currentHabits[student.id][date]) || {};
            let checkedCount = 0;
            habitColumns.forEach(col => {
                if (habitData[col] == 1) checkedCount++;
            });
            const pct = Math.round((checkedCount / 7) * 100);

            const pctColor = pct >= 80 ? 'text-green-600 bg-green-100' :
                pct >= 50 ? 'text-yellow-600 bg-yellow-100' :
                pct > 0 ? 'text-red-600 bg-red-100' : 'text-gray-400 bg-gray-100';

            return `
                <tr class="border-b border-gray-100 hover:bg-primary-50 transition-colors">
                    <td class="py-2.5 px-3 text-center text-gray-500">${idx + 1}</td>
                    <td class="py-2.5 px-3 font-medium text-gray-900 whitespace-nowrap">${student.name}</td>
                    ${habitColumns.map(col => {
                        const hasData = habitData[col] == 1;
                        if (hasData) {
                            return '<td class="py-2 px-2 text-center text-green-600"><span class="material-symbols-outlined text-lg">check_circle</span></td>';
                        } else {
                            return '<td class="py-2 px-2 text-center text-gray-300"><span class="material-symbols-outlined text-lg">cancel</span></td>';
                        }
                    }).join('')}
                    <td class="py-2.5 px-3 text-center">
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold ${pctColor}">${pct}%</span>
                    </td>
                </tr>
            `;
        }).join('');
    }
</script>

<?= $this->endSection() ?>