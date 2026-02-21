<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_teacher') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Rekap Daftar Hadir</h2>
    <p class="text-gray-600 mt-1">Lihat rekap kehadiran siswa bulanan</p>
</div>

<!-- Filter Form -->
<div class="bg-white rounded-2xl shadow p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Kelas -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <span class="material-symbols text-sm align-middle">class</span>
                Kelas
            </label>
            <select id="classId" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <option value="">Pilih Kelas</option>
            </select>
        </div>

        <!-- Bulan -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <span class="material-symbols text-sm align-middle">calendar_month</span>
                Bulan
            </label>
            <select id="month" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
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
                <span class="material-symbols text-sm align-middle">event</span>
                Tahun
            </label>
            <select id="year" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <!-- Will be populated by JavaScript -->
            </select>
        </div>

        <!-- Button -->
        <div class="flex items-end">
            <button onclick="loadRekap()"
                class="w-full px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition-colors font-medium">
                <span class="material-symbols text-sm align-middle mr-1">search</span>
                Tampilkan
            </button>
        </div>
    </div>
</div>

<!-- Rekap Table -->
<div id="rekapContainer" class="hidden">
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="px-6 py-4 bg-primary-600 text-white">
            <h3 class="text-lg font-bold flex items-center">
                <span class="material-symbols mr-2">table_chart</span>
                Rekap Kehadiran
            </h3>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full" id="rekapTable">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b border-gray-200">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b border-gray-200">NIS</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider border-b border-gray-200">Nama</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider border-b border-gray-200">Hadir</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider border-b border-gray-200">Izin</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider border-b border-gray-200">Sakit</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider border-b border-gray-200">Alpha</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider border-b border-gray-200">Persentase</th>
                        </tr>
                    </thead>
                    <tbody id="rekapTableBody">
                        <!-- Will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Empty State -->
<div id="emptyState" class="bg-white rounded-2xl shadow p-12 text-center">
    <span class="material-symbols text-6xl text-gray-300 mb-4">analytics</span>
    <p class="text-gray-500">Pilih kelas, bulan, dan tahun untuk menampilkan rekap kehadiran</p>
</div>

<script>
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

    async function loadRekap() {
        const classId = document.getElementById('classId').value;
        const month = document.getElementById('month').value;
        const year = document.getElementById('year').value;

        if (!classId || !month || !year) {
            alert('Mohon lengkapi semua field');
            return;
        }

        try {
            const response = await fetch(`<?= base_url('api/teacher/rekap') ?>?class_id=${classId}&month=${month}&year=${year}`);
            const result = await response.json();

            if (!result.success) {
                alert(result.message);
                return;
            }

            renderRekapTable(result.data);

            document.getElementById('emptyState').classList.add('hidden');
            document.getElementById('rekapContainer').classList.remove('hidden');
        } catch (error) {
            console.error('Error loading rekap:', error);
            alert('Gagal memuat rekap kehadiran');
        }
    }

    function renderRekapTable(students) {
        const tbody = document.getElementById('rekapTableBody');

        if (students.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">Tidak ada data kehadiran</td></tr>';
            return;
        }

        let html = '';

        students.forEach((student, index) => {
            const total = student.total_hadir + student.total_izin + student.total_sakit + student.total_alpha;
            const percentage = total > 0 ? Math.round((student.total_hadir / total) * 100) : 0;

            let badgeClass = 'bg-green-100 text-green-700';
            if (percentage < 50) {
                badgeClass = 'bg-red-100 text-red-700';
            } else if (percentage < 75) {
                badgeClass = 'bg-yellow-100 text-yellow-700';
            }

            html += `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3 border-b border-gray-200 text-sm">${index + 1}</td>
                <td class="px-4 py-3 border-b border-gray-200 text-sm">${student.nis}</td>
                <td class="px-4 py-3 border-b border-gray-200 text-sm font-medium">${student.name}</td>
                <td class="px-4 py-3 border-b border-gray-200 text-sm text-center">
                    <span class="inline-flex items-center px-2 py-1 rounded-lg bg-green-100 text-green-700 font-medium">
                        ${student.total_hadir}
                    </span>
                </td>
                <td class="px-4 py-3 border-b border-gray-200 text-sm text-center">
                    <span class="inline-flex items-center px-2 py-1 rounded-lg bg-blue-100 text-blue-700 font-medium">
                        ${student.total_izin}
                    </span>
                </td>
                <td class="px-4 py-3 border-b border-gray-200 text-sm text-center">
                    <span class="inline-flex items-center px-2 py-1 rounded-lg bg-yellow-100 text-yellow-700 font-medium">
                        ${student.total_sakit}
                    </span>
                </td>
                <td class="px-4 py-3 border-b border-gray-200 text-sm text-center">
                    <span class="inline-flex items-center px-2 py-1 rounded-lg bg-red-100 text-red-700 font-medium">
                        ${student.total_alpha}
                    </span>
                </td>
                <td class="px-4 py-3 border-b border-gray-200 text-sm text-center">
                    <span class="inline-flex items-center px-2 py-1 rounded-lg ${badgeClass} font-bold">
                        ${percentage}%
                    </span>
                </td>
            </tr>
        `;
        });

        tbody.innerHTML = html;
    }
</script>

<?= $this->endSection() ?>