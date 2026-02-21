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
            <div class="flex items-center border border-gray-300 rounded-xl overflow-hidden divide-x divide-gray-300">
                <button type="button" onclick="changeDate(-1)" class="px-3 py-2 bg-gray-50 text-gray-700 hover:bg-gray-100 transition-colors" title="Hari sebelumnya">
                    <span class="material-symbols">chevron_left</span>
                </button>
                <div class="flex-1 relative">
                    <input type="date" id="dateFilter" class="w-full px-4 py-2 opacity-0 absolute inset-0 cursor-pointer">
                    <div id="dateLabel" class="px-4 py-2 bg-white flex items-center justify-between">
                        <span id="dateLabelText" class="text-gray-900 font-medium"></span>
                        <span class="material-symbols text-gray-400 ml-2">calendar_today</span>
                    </div>
                </div>
                <button type="button" onclick="changeDate(1)" class="px-3 py-2 bg-gray-50 text-gray-700 hover:bg-gray-100 transition-colors" title="Hari berikutnya">
                    <span class="material-symbols">chevron_right</span>
                </button>
                <button type="button" onclick="setToday()" class="px-4 py-2 bg-primary-50 text-primary-700 hover:bg-primary-100 transition-colors font-medium" title="Kembali ke hari ini">
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
                    <th class="py-3 px-3 text-center font-semibold w-16">Total</th>
                    <th class="py-3 px-3 text-center font-semibold w-20">Status</th>
                </tr>
            </thead>
            <tbody id="dailyTableBody">
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Detail Kebiasaan -->
<div id="habitDetailModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 bg-primary-600 text-white rounded-t-2xl flex items-center justify-between flex-shrink-0">
            <div>
                <h3 id="modalStudentName" class="text-lg font-bold"></h3>
                <p id="modalDate" class="text-sm text-primary-100 mt-0.5"></p>
            </div>
            <button onclick="closeHabitModal()" class="text-white hover:text-primary-200 transition-colors">
                <span class="material-symbols text-2xl">close</span>
            </button>
        </div>
        <div id="modalBody" class="p-6 overflow-y-auto flex-1 space-y-4"></div>
    </div>
</div>

<!-- Empty State -->
<div id="emptyState" class="bg-white rounded-2xl shadow p-12 text-center">
    <span class="material-symbols text-6xl text-primary-300 mb-4">today</span>
    <h3 class="text-lg font-semibold text-gray-700 mb-2">Pilih Kelas dan Tanggal</h3>
    <p class="text-gray-500">Pilih kelas dan tanggal untuk menampilkan rekap harian 7 kebiasaan</p>
</div>

<script>
    const habitColumns = ['bangun_pagi', 'beribadah', 'berolahraga', 'makan_sehat', 'gemar_belajar', 'bermasyarakat', 'tidur_cepat'];
    let currentStudents = [];
    let currentHabits = {};
    let currentLoadedDate = '';

    const habitQuestionLabels = {
        bangun_pagi: {
            title: 'Bangun Pagi',
            icon: 'wb_sunny',
            color: 'text-yellow-500',
            bg: 'bg-yellow-50',
            border: 'border-yellow-200',
            fields: {
                jam_bangun: 'Jam Bangun'
            }
        },
        beribadah: {
            title: 'Beribadah',
            icon: 'mosque',
            color: 'text-green-600',
            bg: 'bg-green-50',
            border: 'border-green-200',
            fields: {
                ibadah_wajib: 'Ibadah yang Dilakukan',
                ibadah_lainnya: 'Ibadah Lainnya'
            }
        },
        berolahraga: {
            title: 'Berolahraga',
            icon: 'fitness_center',
            color: 'text-blue-500',
            bg: 'bg-blue-50',
            border: 'border-blue-200',
            fields: {
                kegiatan_olahraga: 'Kegiatan Olahraga',
                durasi_olahraga: 'Durasi'
            }
        },
        makan_sehat: {
            title: 'Makan Bergizi',
            icon: 'restaurant',
            color: 'text-orange-500',
            bg: 'bg-orange-50',
            border: 'border-orange-200',
            fields: {
                menu_makanan: 'Menu Makanan'
            }
        },
        gemar_belajar: {
            title: 'Gemar Belajar',
            icon: 'menu_book',
            color: 'text-purple-500',
            bg: 'bg-purple-50',
            border: 'border-purple-200',
            fields: {
                materi_belajar: 'Yang Dipelajari'
            }
        },
        bermasyarakat: {
            title: 'Bermasyarakat',
            icon: 'groups',
            color: 'text-teal-500',
            bg: 'bg-teal-50',
            border: 'border-teal-200',
            fields: {
                kegiatan_masyarakat: 'Kegiatan Sosial'
            }
        },
        tidur_cepat: {
            title: 'Tidur Cepat',
            icon: 'bedtime',
            color: 'text-indigo-500',
            bg: 'bg-indigo-50',
            border: 'border-indigo-200',
            fields: {
                jam_tidur: 'Jam Tidur'
            }
        },
    };

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
                currentLoadedDate = date;

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
            tbody.innerHTML = '<tr><td colspan="11" class="text-center py-8 text-gray-500">Tidak ada siswa di kelas ini</td></tr>';
            return;
        }

        tbody.innerHTML = currentStudents.map((student, idx) => {
            const habitData = (currentHabits[student.id] && currentHabits[student.id][date]) || {};
            let checkedCount = 0;
            habitColumns.forEach(col => {
                if (habitData[col] == 1) checkedCount++;
            });

            const statusLabel = checkedCount === 7 ? 'Konsisten' : checkedCount >= 4 ? 'Perlu Bimbingan' : 'Sering Bolong';
            const statusClass = checkedCount === 7 ? 'bg-green-100 text-green-700' : checkedCount >= 4 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700';

            return `
                <tr class="border-b border-gray-100 hover:bg-primary-50 transition-colors">
                    <td class="py-2.5 px-3 text-center text-gray-500">${idx + 1}</td>
                    <td class="py-2.5 px-3 font-medium text-gray-900 whitespace-nowrap">${student.name}</td>
                    ${habitColumns.map(col => {
                        const isDone = habitData[col] == 1;
                        if (isDone) {
                            return `<td class="py-2 px-2 text-center"><button onclick="showHabitDetail(${idx}, '${col}', '${date}')" class="focus:outline-none hover:scale-110 transition-transform"><span class="material-symbols text-xl text-green-500">check_circle</span></button></td>`;
                        } else {
                            return '<td class="py-2 px-2 text-center"><span class="material-symbols text-xl text-red-300">cancel</span></td>';
                        }
                    }).join('')}
                    <td class="py-2.5 px-3 text-center">
                        <button onclick="showAllHabits(${idx}, '${date}')" class="font-bold text-primary-700 hover:text-primary-900 hover:underline">${checkedCount}/7</button>
                    </td>
                    <td class="py-2.5 px-3 text-center">
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold ${statusClass}">${statusLabel}</span>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function showHabitDetail(studentIdx, habitKey, date) {
        const student = currentStudents[studentIdx];
        if (!student) return;
        const habitData = (currentHabits[student.id] && currentHabits[student.id][date]) || {};
        const config = habitQuestionLabels[habitKey];

        document.getElementById('modalStudentName').textContent = student.name;
        document.getElementById('modalDate').textContent = formatDateLabel(date);

        const body = document.getElementById('modalBody');
        let html = `<div class="rounded-xl border p-4 ${config.bg} ${config.border}">`;
        html += `<div class="flex items-center gap-2 mb-3">`;
        html += `<span class="material-symbols text-2xl ${config.color}">${config.icon}</span>`;
        html += `<span class="font-bold text-gray-800 text-base">${config.title}</span>`;
        html += `<span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-bold"><span class="material-symbols text-xs mr-0.5">check_circle</span> Dilakukan</span>`;
        html += `</div>`;
        const fieldEntries = Object.entries(config.fields);
        const hasData = fieldEntries.some(([k]) => habitData[k] !== undefined && habitData[k] !== null && habitData[k] !== '');
        if (!hasData) {
            html += `<p class="text-sm text-gray-500 italic">Tidak ada catatan detail dari siswa.</p>`;
        } else {
            fieldEntries.forEach(([fieldKey, fieldLabel]) => {
                const val = habitData[fieldKey];
                if (val === undefined || val === null || val === '') return;
                html += `<div class="mb-2"><p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">${fieldLabel}</p>`;
                if (Array.isArray(val)) {
                    html += `<div class="flex flex-wrap gap-1">${val.map(v => `<span class="inline-block px-2 py-0.5 bg-white border border-gray-200 rounded-full text-xs text-gray-700">${v}</span>`).join('')}</div>`;
                } else {
                    html += `<p class="text-sm text-gray-800 bg-white rounded-lg px-3 py-1.5 border border-gray-200">${val}</p>`;
                }
                html += `</div>`;
            });
        }
        html += `</div>`;
        body.innerHTML = html;
        document.getElementById('habitDetailModal').classList.remove('hidden');
        document.getElementById('habitDetailModal').classList.add('flex');
    }

    function showAllHabits(studentIdx, date) {
        const student = currentStudents[studentIdx];
        if (!student) return;
        const habitData = (currentHabits[student.id] && currentHabits[student.id][date]) || {};

        document.getElementById('modalStudentName').textContent = student.name;
        document.getElementById('modalDate').textContent = formatDateLabel(date);

        const body = document.getElementById('modalBody');
        let html = '';
        habitColumns.forEach(key => {
            const config = habitQuestionLabels[key];
            if (!config) return;
            const isDone = habitData[key] == 1;
            html += `<div class="rounded-xl border p-4 ${isDone ? config.bg + ' ' + config.border : 'bg-gray-50 border-gray-200'}">`;
            html += `<div class="flex items-center gap-2 mb-2">`;
            html += `<span class="material-symbols text-xl ${isDone ? config.color : 'text-gray-300'}">${config.icon}</span>`;
            html += `<span class="font-semibold text-gray-800">${config.title}</span>`;
            if (isDone) {
                html += `<span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-bold"><span class="material-symbols text-xs mr-0.5">check_circle</span> Dilakukan</span>`;
            } else {
                html += `<span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full bg-red-100 text-red-600 text-xs font-bold"><span class="material-symbols text-xs mr-0.5">cancel</span> Belum</span>`;
            }
            html += `</div>`;
            if (isDone) {
                const fieldEntries = Object.entries(config.fields);
                const hasData = fieldEntries.some(([k]) => habitData[k] !== undefined && habitData[k] !== null && habitData[k] !== '');
                if (!hasData) {
                    html += `<p class="text-xs text-gray-400 italic">Tidak ada catatan detail.</p>`;
                } else {
                    fieldEntries.forEach(([fieldKey, fieldLabel]) => {
                        const val = habitData[fieldKey];
                        if (val === undefined || val === null || val === '') return;
                        html += `<div class="mb-1"><p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">${fieldLabel}</p>`;
                        if (Array.isArray(val)) {
                            html += `<div class="flex flex-wrap gap-1">${val.map(v => `<span class="inline-block px-2 py-0.5 bg-white border border-gray-200 rounded-full text-xs text-gray-700">${v}</span>`).join('')}</div>`;
                        } else {
                            html += `<p class="text-sm text-gray-800 bg-white rounded-lg px-3 py-1.5 border border-gray-200">${val}</p>`;
                        }
                        html += `</div>`;
                    });
                }
            }
            html += `</div>`;
        });
        body.innerHTML = html;
        document.getElementById('habitDetailModal').classList.remove('hidden');
        document.getElementById('habitDetailModal').classList.add('flex');
    }

    function closeHabitModal() {
        document.getElementById('habitDetailModal').classList.add('hidden');
        document.getElementById('habitDetailModal').classList.remove('flex');
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('habitDetailModal').addEventListener('click', function(e) {
            if (e.target === this) closeHabitModal();
        });
    });

    function formatDateLabel(dateStr) {
        if (!dateStr) return '';
        const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const d = new Date(dateStr + 'T00:00:00');
        return `${dayNames[d.getDay()]}, ${d.getDate()} ${monthNames[d.getMonth()]} ${d.getFullYear()}`;
    }
</script>

<?= $this->endSection() ?>