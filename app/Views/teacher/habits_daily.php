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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Kelas -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <span class="material-symbols-outlined text-sm align-middle">class</span>
                Kelas
            </label>
            <select id="classId" onchange="loadDaily()" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="">Pilih Kelas</option>
            </select>
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
    <p class="text-gray-500">Pilih kelas untuk menampilkan data</p>
</div>

<script>
    const daysIndonesia = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const monthsIndonesia = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    document.addEventListener('DOMContentLoaded', function() {
        loadClasses();
        updateDateLabel();
    });

    async function loadClasses() {
        try {
            const response = await fetch('<?= base_url('api/teacher/classes') ?>');
            const result = await response.json();

            const select = document.getElementById('classId');
            select.innerHTML = '<option value="">Pilih Kelas</option>';

            result.data.forEach(cls => {
                const option = document.createElement('option');
                option.value = cls.id;
                option.textContent = cls.name;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading classes:', error);
        }
    }

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
            document.getElementById('emptyState').classList.remove('hidden');
            document.getElementById('dataContainer').classList.add('hidden');
            return;
        }

        try {
            const response = await fetch(`<?= base_url('api/teacher/habits') ?>?class_id=${classId}&date=${date}`);
            const result = await response.json();

            if (result.status !== 'success') {
                alert(result.message);
                return;
            }

            renderTable(result.data);
            await loadClassRecap(classId, date);

            document.getElementById('emptyState').classList.add('hidden');
            document.getElementById('dataContainer').classList.remove('hidden');
            document.getElementById('recapContainer').classList.remove('hidden');
        } catch (error) {
            console.error('Error loading habits:', error);
            alert('Gagal memuat data kebiasaan');
        }
    }

    function renderTable(payload) {
        const tbody = document.getElementById('dataTableBody');
        const students = payload.students || [];

        if (students.length === 0) {
            tbody.innerHTML = '<tr><td colspan="11" class="px-4 py-8 text-center text-gray-500">Tidak ada data siswa</td></tr>';
            return;
        }

        let html = '';

        students.forEach((student, index) => {
            html += `
            <tr class="hover:bg-primary-50 transition-colors">
                <td class="px-4 py-3 border-b border-gray-200 text-sm sticky left-0 bg-white">${index + 1}</td>
                <td class="px-4 py-3 border-b border-gray-200 text-sm font-medium sticky left-12 bg-white">${student.student_name}</td>
                <td class="px-4 py-3 border-b border-gray-200 text-center">
                    ${getIcon(student.bangun_pagi)}
                </td>
                <td class="px-4 py-3 border-b border-gray-200 text-center">
                    ${getIcon(student.beribadah)}
                </td>
                <td class="px-4 py-3 border-b border-gray-200 text-center">
                    ${getIcon(student.berolahraga)}
                </td>
                <td class="px-4 py-3 border-b border-gray-200 text-center">
                    ${getIcon(student.makan_sehat)}
                </td>
                <td class="px-4 py-3 border-b border-gray-200 text-center">
                    ${getIcon(student.gemar_belajar)}
                </td>
                <td class="px-4 py-3 border-b border-gray-200 text-center">
                    ${getIcon(student.bermasyarakat)}
                </td>
                <td class="px-4 py-3 border-b border-gray-200 text-center">
                    ${getIcon(student.tidur_cepat)}
                </td>
                <td class="px-4 py-3 border-b border-gray-200 text-center text-sm font-bold text-primary-700">
                    ${student.completed}/7
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

    function getIcon(checked) {
        if (checked === true || checked === 1 || checked === '1') {
            return '<span class="material-symbols-outlined text-2xl text-green-500">check_circle</span>';
        } else {
            return '<span class="material-symbols-outlined text-2xl text-red-300">cancel</span>';
        }
    }
</script>

<?= $this->endSection() ?>