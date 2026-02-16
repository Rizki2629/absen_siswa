<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_teacher') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Rekap Bulanan 7 Kebiasaan</h2>
    <p class="text-gray-600 mt-1">Lihat rekap bulanan kebiasaan per siswa</p>
</div>

<!-- Filter Form -->
<div class="bg-white rounded-2xl shadow p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <!-- Kelas -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <span class="material-symbols-outlined text-sm align-middle">class</span>
                Kelas
            </label>
            <select id="classId" onchange="onClassChange()" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="">Pilih Kelas</option>
            </select>
        </div>

        <!-- Siswa -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <span class="material-symbols-outlined text-sm align-middle">person</span>
                Siswa
            </label>
            <select id="studentId" onchange="loadMonthly()" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500" disabled>
                <option value="">Pilih Siswa</option>
            </select>
        </div>

        <!-- Bulan -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <span class="material-symbols-outlined text-sm align-middle">calendar_month</span>
                Bulan
            </label>
            <select id="month" onchange="loadMonthly()" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
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

        <!-- Tahun -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <span class="material-symbols-outlined text-sm align-middle">event</span>
                Tahun
            </label>
            <select id="year" onchange="loadMonthly()" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <!-- Will be populated by JavaScript -->
            </select>
        </div>

        <!-- Button -->
        <div class="flex items-end">
            <button onclick="loadMonthly()"
                class="w-full px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition-colors font-medium">
                <span class="material-symbols-outlined text-sm align-middle mr-1">search</span>
                Tampilkan
            </button>
        </div>
    </div>
</div>

<!-- Table -->
<div id="dataContainer" class="hidden">
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="px-6 py-4 bg-primary-600 text-white flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold flex items-center">
                    <span class="material-symbols-outlined mr-2">emoji_people</span>
                    Rekap Bulanan
                </h3>
                <p class="text-sm text-primary-100 mt-1" id="studentInfo"></p>
            </div>
            <div id="overallPercentage" class="bg-white text-primary-600 px-4 py-2 rounded-lg font-bold text-lg">
                0%
            </div>
        </div>

        <div class="p-6 overflow-x-auto">
            <table class="w-full" id="monthlyTable">
                <thead>
                    <tr class="bg-primary-50">
                        <th class="px-4 py-3 text-left text-xs font-medium text-primary-700 uppercase tracking-wider border-b-2 border-primary-200">Tanggal</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-primary-700 uppercase tracking-wider border-b-2 border-primary-200">Proaktif</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-primary-700 uppercase tracking-wider border-b-2 border-primary-200">Merujuk Tujuan</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-primary-700 uppercase tracking-wider border-b-2 border-primary-200">Dahulukan Yang Utama</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-primary-700 uppercase tracking-wider border-b-2 border-primary-200">Berpikir Menang-Menang</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-primary-700 uppercase tracking-wider border-b-2 border-primary-200">Mengerti Lalu Dimengerti</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-primary-700 uppercase tracking-wider border-b-2 border-primary-200">Wujudkan Sinergi</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-primary-700 uppercase tracking-wider border-b-2 border-primary-200">Mengasah Gergaji</th>
                    </tr>
                </thead>
                <tbody id="monthlyTableBody">
                    <!-- Will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Empty State -->
<div id="emptyState" class="bg-white rounded-2xl shadow p-12 text-center">
    <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">person_search</span>
    <p class="text-gray-500">Pilih kelas dan siswa untuk menampilkan rekap bulanan</p>
</div>

<script>
    const daysIndonesia = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    document.addEventListener('DOMContentLoaded', function() {
        loadClasses();
        initializeYearSelect();
        setCurrentMonth();
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

    function initializeYearSelect() {
        const yearSelect = document.getElementById('year');
        const currentYear = new Date().getFullYear();

        for (let year = currentYear - 2; year <= currentYear + 1; year++) {
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;
            if (year === currentYear) {
                option.selected = true;
            }
            yearSelect.appendChild(option);
        }
    }

    function setCurrentMonth() {
        const currentMonth = new Date().getMonth() + 1;
        document.getElementById('month').value = currentMonth;
    }

    async function onClassChange() {
        const classId = document.getElementById('classId').value;
        const studentSelect = document.getElementById('studentId');

        if (!classId) {
            studentSelect.innerHTML = '<option value="">Pilih Siswa</option>';
            studentSelect.disabled = true;
            document.getElementById('emptyState').classList.remove('hidden');
            document.getElementById('dataContainer').classList.add('hidden');
            return;
        }

        try {
            const response = await fetch(`<?= base_url('api/teacher/students') ?>?class_id=${classId}`);
            const result = await response.json();

            studentSelect.innerHTML = '<option value="">Pilih Siswa</option>';

            result.data.forEach(student => {
                const option = document.createElement('option');
                option.value = student.id;
                option.textContent = `${student.name} (${student.nis})`;
                studentSelect.appendChild(option);
            });

            studentSelect.disabled = false;
        } catch (error) {
            console.error('Error loading students:', error);
            alert('Gagal memuat daftar siswa');
        }
    }

    async function loadMonthly() {
        const studentId = document.getElementById('studentId').value;
        const month = document.getElementById('month').value;
        const year = document.getElementById('year').value;

        if (!studentId || !month || !year) {
            document.getElementById('emptyState').classList.remove('hidden');
            document.getElementById('dataContainer').classList.add('hidden');
            return;
        }

        try {
            const response = await fetch(`<?= base_url('api/teacher/habits/student') ?>?student_id=${studentId}&month=${month}&year=${year}`);
            const result = await response.json();

            if (!result.success) {
                alert(result.message);
                return;
            }

            renderMonthlyTable(result.data);

            document.getElementById('emptyState').classList.add('hidden');
            document.getElementById('dataContainer').classList.remove('hidden');
        } catch (error) {
            console.error('Error loading monthly habits:', error);
            alert('Gagal memuat data kebiasaan bulanan');
        }
    }

    function renderMonthlyTable(data) {
        const tbody = document.getElementById('monthlyTableBody');
        const studentSelect = document.getElementById('studentId');
        const selectedOption = studentSelect.options[studentSelect.selectedIndex];

        document.getElementById('studentInfo').textContent = selectedOption.text;

        if (data.habits.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">Tidak ada data untuk bulan ini</td></tr>';
            document.getElementById('overallPercentage').textContent = '0%';
            return;
        }

        let html = '';

        data.habits.forEach(habit => {
            const date = new Date(habit.date);
            const dayName = daysIndonesia[date.getDay()];
            const day = date.getDate();

            html += `
            <tr class="hover:bg-primary-50 transition-colors">
                <td class="px-4 py-3 border-b border-gray-200 text-sm font-medium">
                    <div>${dayName}</div>
                    <div class="text-xs text-gray-500">${day} ${getMonthName(date.getMonth())}</div>
                </td>
                <td class="px-4 py-3 border-b border-gray-200 text-center">
                    ${getIcon(habit.proaktif)}
                </td>
                <td class="px-4 py-3 border-b border-gray-200 text-center">
                    ${getIcon(habit.merujuk_tujuan)}
                </td>
                <td class="px-4 py-3 border-b border-gray-200 text-center">
                    ${getIcon(habit.dahulukan_yang_utama)}
                </td>
                <td class="px-4 py-3 border-b border-gray-200 text-center">
                    ${getIcon(habit.berpikir_menang_menang)}
                </td>
                <td class="px-4 py-3 border-b border-gray-200 text-center">
                    ${getIcon(habit.mengerti_lalu_dimengerti)}
                </td>
                <td class="px-4 py-3 border-b border-gray-200 text-center">
                    ${getIcon(habit.wujudkan_sinergi)}
                </td>
                <td class="px-4 py-3 border-b border-gray-200 text-center">
                    ${getIcon(habit.mengasah_gergaji)}
                </td>
            </tr>
        `;
        });

        tbody.innerHTML = html;
        document.getElementById('overallPercentage').textContent = `${data.percentage}%`;
    }

    function getIcon(checked) {
        if (checked === true || checked === 1 || checked === '1') {
            return '<span class="material-symbols-outlined text-2xl text-green-500">check_circle</span>';
        } else {
            return '<span class="material-symbols-outlined text-2xl text-red-300">cancel</span>';
        }
    }

    function getMonthName(monthIndex) {
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        return months[monthIndex];
    }
</script>

<?= $this->endSection() ?>