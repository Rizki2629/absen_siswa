<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_teacher') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Rekap Harian 7 Kebiasaan</h2>
    <p class="text-gray-600 mt-1">Lihat rekap harian kebiasaan siswa</p>
</div>

<!-- Filter Form -->
<div class="bg-white rounded-2xl shadow p-6 mb-6">
    <!-- Hidden class id -->
    <input type="hidden" id="classId" value="<?= isset($teacherClass) ? esc($teacherClass['id']) : '' ?>">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Info Kelas -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <span class="material-symbols-outlined text-sm align-middle">class</span>
                Kelas
            </label>
            <?php if (isset($teacherClass)): ?>
                <div class="w-full px-4 py-2 border border-primary-300 bg-primary-50 rounded-xl text-primary-700 font-semibold">
                    <span class="material-symbols-outlined text-sm align-middle mr-1">school</span>
                    <?= esc($teacherClass['name']) ?>
                </div>
            <?php else: ?>
                <div class="w-full px-4 py-2 border border-red-300 bg-red-50 rounded-xl text-red-600">
                    Kelas belum diassign
                </div>
            <?php endif; ?>
        </div>

        <!-- Tanggal dengan navigasi -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <span class="material-symbols-outlined text-sm align-middle">calendar_today</span>
                Tanggal
            </label>
            <div class="flex gap-2">
                <button onclick="changeDate(-1)" class="px-3 py-2 border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                    <span class="material-symbols-outlined text-sm">chevron_left</span>
                </button>
                <input type="date" id="date" onchange="loadDaily()"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    value="<?= date('Y-m-d') ?>">
                <button onclick="changeDate(1)" class="px-3 py-2 border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                    <span class="material-symbols-outlined text-sm">chevron_right</span>
                </button>
            </div>
        </div>

        <!-- Hari Ini Button -->
        <div class="flex items-end">
            <button onclick="setToday()"
                class="w-full px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition-colors font-medium">
                <span class="material-symbols-outlined text-sm align-middle mr-1">today</span>
                Hari Ini
            </button>
        </div>
    </div>

    <!-- Date Label -->
    <div class="mt-4 text-center">
        <p id="dateLabel" class="text-xl font-bold text-primary-600"></p>
    </div>
</div>

<!-- Table -->
<div id="dataContainer" class="hidden">
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="px-6 py-4 bg-primary-600 text-white">
            <h3 class="text-lg font-bold flex items-center">
                <span class="material-symbols-outlined mr-2">emoji_people</span>
                Daftar Siswa
            </h3>
        </div>

        <div class="p-6 overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-primary-50">
                        <th class="px-4 py-3 text-left text-xs font-medium text-primary-700 uppercase tracking-wider border-b-2 border-primary-200 sticky left-0 bg-primary-50">No</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-primary-700 uppercase tracking-wider border-b-2 border-primary-200 sticky left-12 bg-primary-50">Nama</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-primary-700 uppercase tracking-wider border-b-2 border-primary-200">Bangun Pagi</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-primary-700 uppercase tracking-wider border-b-2 border-primary-200">Beribadah</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-primary-700 uppercase tracking-wider border-b-2 border-primary-200">Olahraga</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-primary-700 uppercase tracking-wider border-b-2 border-primary-200">Makan Sehat</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-primary-700 uppercase tracking-wider border-b-2 border-primary-200">Gemar Belajar</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-primary-700 uppercase tracking-wider border-b-2 border-primary-200">Bermasyarakat</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-primary-700 uppercase tracking-wider border-b-2 border-primary-200">Tidur Cepat</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-primary-700 uppercase tracking-wider border-b-2 border-primary-200">Total</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-primary-700 uppercase tracking-wider border-b-2 border-primary-200">Status</th>
                    </tr>
                </thead>
                <tbody id="dataTableBody">
                    <!-- Will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="recapContainer" class="hidden mt-6 bg-white rounded-2xl shadow overflow-hidden">
    <div class="px-6 py-4 bg-indigo-600 text-white">
        <h3 class="text-lg font-bold flex items-center">
            <span class="material-symbols-outlined mr-2">insights</span>
            Rekap Kelas & Intervensi Dini
        </h3>
    </div>
    <div class="p-6 overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-indigo-50">
                    <th class="px-4 py-3 text-left text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Nama</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Rata-rata 7 Hari</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Hari Sempurna</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Kategori</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-indigo-700 uppercase tracking-wider border-b border-indigo-200">Intervensi Dini</th>
                </tr>
            </thead>
            <tbody id="recapTableBody"></tbody>
        </table>
    </div>
</div>

<!-- Empty State -->
<div id="emptyState" class="bg-white rounded-2xl shadow p-12 text-center">
    <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">emoji_people</span>
    <p class="text-gray-500">Memuat data siswa...</p>
</div>

<!-- Modal Detail Kebiasaan -->
<div id="habitDetailModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black bg-opacity-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="px-6 py-4 bg-primary-600 text-white rounded-t-2xl flex items-center justify-between flex-shrink-0">
            <div>
                <h3 id="modalStudentName" class="text-lg font-bold"></h3>
                <p id="modalDate" class="text-sm text-primary-100 mt-0.5"></p>
            </div>
            <button onclick="closeHabitModal()" class="text-white hover:text-primary-200 transition-colors">
                <span class="material-symbols-outlined text-2xl">close</span>
            </button>
        </div>
        <!-- Modal Body -->
        <div id="modalBody" class="p-6 overflow-y-auto flex-1 space-y-4">
        </div>
    </div>
</div>

<script>
    const daysIndonesia = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const monthsIndonesia = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

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

    let studentHabitData = [];
    let currentLoadedDate = '';

    document.addEventListener('DOMContentLoaded', function() {
        updateDateLabel();
        // Auto-load jika kelas sudah tersedia dari PHP
        const classId = document.getElementById('classId').value;
        if (classId) {
            loadDaily();
        } else {
            document.getElementById('emptyState').querySelector('p').textContent = 'Kelas belum diassign untuk akun ini.';
        }
    });

    function updateDateLabel() {
        const dateInput = document.getElementById('date');
        const date = new Date(dateInput.value + 'T00:00:00');

        const dayName = daysIndonesia[date.getDay()];
        const day = date.getDate();
        const monthName = monthsIndonesia[date.getMonth()];
        const year = date.getFullYear();

        document.getElementById('dateLabel').textContent = `${dayName}, ${day} ${monthName} ${year}`;
    }

    function changeDate(days) {
        const dateInput = document.getElementById('date');
        const currentDate = new Date(dateInput.value + 'T00:00:00');
        currentDate.setDate(currentDate.getDate() + days);

        const year = currentDate.getFullYear();
        const month = String(currentDate.getMonth() + 1).padStart(2, '0');
        const day = String(currentDate.getDate()).padStart(2, '0');

        dateInput.value = `${year}-${month}-${day}`;
        updateDateLabel();
        loadDaily();
    }

    function setToday() {
        document.getElementById('date').value = '<?= date('Y-m-d') ?>';
        updateDateLabel();
        loadDaily();
    }

    async function loadDaily() {
        const classId = document.getElementById('classId').value;
        const date = document.getElementById('date').value;

        if (!classId || !date) {
            return;
        }

        try {
            const response = await fetch(`<?= base_url('api/teacher/habits') ?>?class_id=${classId}&date=${date}`);
            const result = await response.json();

            if (result.status !== 'success') {
                alert(result.message);
                return;
            }

            renderTable(result.data, date);
            await loadClassRecap(classId, date);

            document.getElementById('emptyState').classList.add('hidden');
            document.getElementById('dataContainer').classList.remove('hidden');
            document.getElementById('recapContainer').classList.remove('hidden');
        } catch (error) {
            console.error('Error loading habits:', error);
            alert('Gagal memuat data kebiasaan');
        }
    }

    function renderTable(payload, date) {
        const tbody = document.getElementById('dataTableBody');
        const students = payload.students || [];
        studentHabitData = students;
        currentLoadedDate = date;

        if (students.length === 0) {
            tbody.innerHTML = '<tr><td colspan="11" class="px-4 py-8 text-center text-gray-500">Tidak ada data siswa</td></tr>';
            return;
        }

        const habitKeys = ['bangun_pagi', 'beribadah', 'berolahraga', 'makan_sehat', 'gemar_belajar', 'bermasyarakat', 'tidur_cepat'];

        let html = '';

        students.forEach((student, index) => {
            html += `
            <tr class="hover:bg-primary-50 transition-colors">
                <td class="px-4 py-3 border-b border-gray-200 text-sm sticky left-0 bg-white">${index + 1}</td>
                <td class="px-4 py-3 border-b border-gray-200 text-sm font-medium sticky left-12 bg-white">${student.student_name}</td>
                ${habitKeys.map(key => `
                <td class="px-4 py-3 border-b border-gray-200 text-center">
                    ${getIcon(student[key], key, index)}
                </td>`).join('')}
                <td class="px-4 py-3 border-b border-gray-200 text-center text-sm font-bold text-primary-700">
                    <button onclick="showHabitModalByIndex(${index})"
                        class="font-bold text-primary-700 hover:text-primary-900 hover:underline cursor-pointer">
                        ${student.completed}/7
                    </button>
                </td>
                <td class="px-4 py-3 border-b border-gray-200 text-center">
                    ${getStatusBadge(student.status)}
                </td>
            </tr>
        `;
        });

        tbody.innerHTML = html;
    }

    async function loadClassRecap(classId, date) {
        try {
            const response = await fetch(`<?= base_url('api/teacher/habits/class-recap') ?>?class_id=${classId}&date=${date}`);
            const result = await response.json();

            if (result.status !== 'success') {
                return;
            }

            renderClassRecap(result.data.recap || []);
        } catch (error) {
            console.error('Error loading recap:', error);
        }
    }

    function renderClassRecap(rows) {
        const tbody = document.getElementById('recapTableBody');
        if (!rows.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada data rekap</td></tr>';
            return;
        }

        tbody.innerHTML = rows.map((row) => `
            <tr class="hover:bg-indigo-50 transition-colors">
                <td class="px-4 py-3 border-b border-gray-200 text-sm font-semibold">${row.student_name}</td>
                <td class="px-4 py-3 border-b border-gray-200 text-center text-sm">${row.avg_completed_7_days}/7</td>
                <td class="px-4 py-3 border-b border-gray-200 text-center text-sm">${row.perfect_days_7_days}</td>
                <td class="px-4 py-3 border-b border-gray-200 text-center">${getStatusBadge(row.status)}</td>
                <td class="px-4 py-3 border-b border-gray-200 text-center">
                    ${row.need_intervention
                        ? '<span class="inline-flex items-center px-2 py-1 rounded-lg bg-red-100 text-red-700 text-xs font-bold">Perlu Intervensi</span>'
                        : '<span class="inline-flex items-center px-2 py-1 rounded-lg bg-green-100 text-green-700 text-xs font-bold">Aman</span>'}
                </td>
            </tr>
        `).join('');
    }

    function getStatusBadge(status) {
        if (status === 'konsisten') {
            return '<span class="inline-flex items-center px-2 py-1 rounded-lg bg-green-100 text-green-700 text-xs font-bold">Konsisten</span>';
        }
        if (status === 'sering bolong') {
            return '<span class="inline-flex items-center px-2 py-1 rounded-lg bg-red-100 text-red-700 text-xs font-bold">Sering Bolong</span>';
        }
        return '<span class="inline-flex items-center px-2 py-1 rounded-lg bg-yellow-100 text-yellow-700 text-xs font-bold">Perlu Bimbingan</span>';
    }

    function getIcon(value, habitKey, studentIndex) {
        const isDone = (value === true || value === 1 || value === '1');
        if (isDone) {
            return `<button onclick="showHabitDetailByIndex(event, ${studentIndex}, '${habitKey}')"
                title="Lihat detail ${habitQuestionLabels[habitKey]?.title || habitKey}"
                class="focus:outline-none hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-2xl text-green-500">check_circle</span>
            </button>`;
        } else {
            return '<span class="material-symbols-outlined text-2xl text-red-300">cancel</span>';
        }
    }

    // Buka modal untuk satu habit spesifik saat ikon centang diklik
    function showHabitDetailByIndex(event, studentIndex, habitKey) {
        event.stopPropagation();
        const student = studentHabitData[studentIndex];
        if (!student) return;
        let answers = {};
        try { answers = JSON.parse(student.habit_answers || '{}'); } catch(e) {}
        const habitAnswers = answers[habitKey] || {};
        const config = habitQuestionLabels[habitKey];

        document.getElementById('modalStudentName').textContent = student.student_name;
        document.getElementById('modalDate').textContent = formatDateLabel(currentLoadedDate);

        const body = document.getElementById('modalBody');
        if (!config) {
            body.innerHTML = '<p class="text-gray-500">Tidak ada detail tersedia.</p>';
        } else {
            let html = `<div class="rounded-xl border p-4 ${config.bg} ${config.border}">`;
            html += `<div class="flex items-center gap-2 mb-3">`;
            html += `<span class="material-symbols-outlined text-2xl ${config.color}">${config.icon}</span>`;
            html += `<span class="font-bold text-gray-800 text-base">${config.title}</span>`;
            html += `<span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-bold">`;
            html += `<span class="material-symbols-outlined text-xs mr-0.5">check_circle</span> Dilakukan</span>`;
            html += `</div>`;

            const fieldEntries = Object.entries(config.fields);
            if (fieldEntries.length === 0 || Object.keys(habitAnswers).length === 0) {
                html += `<p class="text-sm text-gray-500 italic">Tidak ada catatan detail dari siswa.</p>`;
            } else {
                fieldEntries.forEach(([fieldKey, fieldLabel]) => {
                    const val = habitAnswers[fieldKey];
                    if (val === undefined || val === null || val === '') return;
                    html += `<div class="mb-2">`;
                    html += `<p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">${fieldLabel}</p>`;
                    if (Array.isArray(val)) {
                        html += `<div class="flex flex-wrap gap-1">`;
                        val.forEach(v => {
                            html += `<span class="inline-block px-2 py-0.5 bg-white border border-gray-200 rounded-full text-xs text-gray-700">${v}</span>`;
                        });
                        html += `</div>`;
                    } else {
                        html += `<p class="text-sm text-gray-800 bg-white rounded-lg px-3 py-1.5 border border-gray-200">${val}</p>`;
                    }
                    html += `</div>`;
                });
            }
            html += `</div>`;
            body.innerHTML = html;
        }

        document.getElementById('habitDetailModal').classList.remove('hidden');
        document.getElementById('habitDetailModal').classList.add('flex');
    }

    // Buka modal semua kebiasaan siswa saat klik total (angka x/7)
    function showHabitModalByIndex(studentIndex) {
        const student = studentHabitData[studentIndex];
        if (!student) return;
        let answers = {};
        try { answers = JSON.parse(student.habit_answers || '{}'); } catch(e) {}

        document.getElementById('modalStudentName').textContent = student.student_name;
        document.getElementById('modalDate').textContent = formatDateLabel(currentLoadedDate);

        const habitKeys = ['bangun_pagi', 'beribadah', 'berolahraga', 'makan_sehat', 'gemar_belajar', 'bermasyarakat', 'tidur_cepat'];
        const body = document.getElementById('modalBody');
        let html = '';

        habitKeys.forEach(key => {
            const config = habitQuestionLabels[key];
            if (!config) return;
            const isDone = (student[key] === 1 || student[key] === '1' || student[key] === true);
            const habitAnswers = answers[key] || {};

            html += `<div class="rounded-xl border p-4 ${isDone ? config.bg + ' ' + config.border : 'bg-gray-50 border-gray-200'}">`;
            html += `<div class="flex items-center gap-2 mb-2">`;
            html += `<span class="material-symbols-outlined text-xl ${isDone ? config.color : 'text-gray-300'}">${config.icon}</span>`;
            html += `<span class="font-semibold text-gray-800">${config.title}</span>`;
            if (isDone) {
                html += `<span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-bold">`;
                html += `<span class="material-symbols-outlined text-xs mr-0.5">check_circle</span> Dilakukan</span>`;
            } else {
                html += `<span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full bg-red-100 text-red-600 text-xs font-bold">`;
                html += `<span class="material-symbols-outlined text-xs mr-0.5">cancel</span> Belum</span>`;
            }
            html += `</div>`;

            if (isDone) {
                const fieldEntries = Object.entries(config.fields);
                const hasData = fieldEntries.some(([k]) => habitAnswers[k] !== undefined && habitAnswers[k] !== null && habitAnswers[k] !== '');
                if (!hasData) {
                    html += `<p class="text-xs text-gray-400 italic">Tidak ada catatan detail.</p>`;
                } else {
                    fieldEntries.forEach(([fieldKey, fieldLabel]) => {
                        const val = habitAnswers[fieldKey];
                        if (val === undefined || val === null || val === '') return;
                        html += `<div class="mb-1">`;
                        html += `<p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-0.5">${fieldLabel}</p>`;
                        if (Array.isArray(val)) {
                            html += `<div class="flex flex-wrap gap-1">`;
                            val.forEach(v => {
                                html += `<span class="inline-block px-2 py-0.5 bg-white border border-gray-200 rounded-full text-xs text-gray-700">${v}</span>`;
                            });
                            html += `</div>`;
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

    // Tutup modal saat klik area luar
    document.getElementById('habitDetailModal').addEventListener('click', function(e) {
        if (e.target === this) closeHabitModal();
    });

    function formatDateLabel(dateStr) {
        if (!dateStr) return '';
        const d = new Date(dateStr + 'T00:00:00');
        return `${daysIndonesia[d.getDay()]}, ${d.getDate()} ${monthsIndonesia[d.getMonth()]} ${d.getFullYear()}`;
    }
</script>

<?= $this->endSection() ?>