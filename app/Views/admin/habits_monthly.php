<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_admin') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Rekap Bulanan - 7 Kebiasaan</h2>
        <p class="text-gray-600 mt-1">Lihat rekap kebiasaan siswa dalam satu bulan</p>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-2xl shadow p-4 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kelas</label>
            <select id="classFilter" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
                <option value="">-- Pilih Kelas --</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Siswa</label>
            <select id="studentFilter" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500" disabled>
                <option value="">-- Pilih Kelas Dulu --</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
            <select id="monthFilter" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
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
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
            <select id="yearFilter" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
            </select>
        </div>
    </div>
</div>

<!-- Monthly Recap Table -->
<div id="monthlyContainer" class="bg-white rounded-2xl shadow overflow-hidden" style="display: none;">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-bold text-gray-900" id="monthlyTitle">Rekap Bulanan</h3>
        <p class="text-sm text-gray-500" id="monthlySubtitle"></p>
        <div class="mt-2 flex items-center space-x-4">
            <span class="text-sm text-gray-600">Persentase Keseluruhan:</span>
            <span class="inline-block px-3 py-1 rounded-full text-sm font-bold" id="overallPct">-</span>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-primary-600 text-white">
                    <th class="py-3 px-3 text-left font-semibold whitespace-nowrap" rowspan="2">Hari</th>
                    <th class="py-3 px-3 text-center font-semibold whitespace-nowrap" rowspan="2">Tgl</th>
                    <th class="py-2 px-2 text-center font-semibold" colspan="7">7 Kebiasaan Anak Indonesia Hebat</th>
                    <th class="py-3 px-3 text-center font-semibold whitespace-nowrap" rowspan="2">%</th>
                </tr>
                <tr class="bg-primary-500 text-white text-xs">
                    <th class="py-2 px-2 text-center font-medium">Bangun<br>Pagi</th>
                    <th class="py-2 px-2 text-center font-medium">Beribadah</th>
                    <th class="py-2 px-2 text-center font-medium">Berolahraga</th>
                    <th class="py-2 px-2 text-center font-medium">Makan<br>Sehat</th>
                    <th class="py-2 px-2 text-center font-medium">Gemar<br>Belajar</th>
                    <th class="py-2 px-2 text-center font-medium">Bermasya-<br>rakat</th>
                    <th class="py-2 px-2 text-center font-medium">Tidur<br>Cepat</th>
                </tr>
            </thead>
            <tbody id="monthlyTableBody">
            </tbody>
        </table>
    </div>
</div>

<!-- Empty State -->
<div id="emptyState" class="bg-white rounded-2xl shadow p-12 text-center">
    <span class="material-symbols-outlined text-6xl text-primary-300 mb-4">date_range</span>
    <h3 class="text-lg font-semibold text-gray-700 mb-2">Pilih Kelas dan Siswa</h3>
    <p class="text-gray-500">Pilih kelas, siswa, bulan, dan tahun untuk menampilkan rekap bulanan 7 kebiasaan</p>
</div>

<script>
    const habitColumns = ['bangun_pagi', 'beribadah', 'berolahraga', 'makan_sehat', 'gemar_belajar', 'bermasyarakat', 'tidur_cepat'];

    document.addEventListener('DOMContentLoaded', function() {
        loadClasses();
        setupFilters();
    });

    function setupFilters() {
        const now = new Date();
        document.getElementById('monthFilter').value = now.getMonth() + 1;

        const yearSelect = document.getElementById('yearFilter');
        const currentYear = now.getFullYear();
        for (let y = currentYear - 2; y <= currentYear + 1; y++) {
            const opt = document.createElement('option');
            opt.value = y;
            opt.textContent = y;
            if (y === currentYear) opt.selected = true;
            yearSelect.appendChild(opt);
        }

        document.getElementById('classFilter').addEventListener('change', onClassChange);
        document.getElementById('studentFilter').addEventListener('change', loadMonthly);
        document.getElementById('monthFilter').addEventListener('change', loadMonthly);
        document.getElementById('yearFilter').addEventListener('change', loadMonthly);
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

    async function onClassChange() {
        const classId = document.getElementById('classFilter').value;
        const studentSelect = document.getElementById('studentFilter');

        // Reset student dropdown
        studentSelect.innerHTML = '<option value="">-- Pilih Siswa --</option>';
        studentSelect.disabled = true;

        // Hide table
        document.getElementById('monthlyContainer').style.display = 'none';
        document.getElementById('emptyState').style.display = 'block';

        if (!classId) {
            studentSelect.innerHTML = '<option value="">-- Pilih Kelas Dulu --</option>';
            return;
        }

        try {
            const response = await fetch(`<?= base_url('api/admin/students') ?>?class_id=${classId}`, {
                credentials: 'same-origin'
            });
            const data = await response.json();
            if (data.status === 'success') {
                const students = data.data;
                if (students.length === 0) {
                    studentSelect.innerHTML = '<option value="">Tidak ada siswa</option>';
                    return;
                }
                students.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.id;
                    opt.textContent = s.name;
                    studentSelect.appendChild(opt);
                });
                studentSelect.disabled = false;
            }
        } catch (error) {
            console.error('Error loading students:', error);
        }
    }

    async function loadMonthly() {
        const studentId = document.getElementById('studentFilter').value;
        const month = document.getElementById('monthFilter').value;
        const year = document.getElementById('yearFilter').value;

        if (!studentId) {
            document.getElementById('monthlyContainer').style.display = 'none';
            document.getElementById('emptyState').style.display = 'block';
            return;
        }

        document.getElementById('emptyState').style.display = 'none';
        document.getElementById('monthlyContainer').style.display = 'block';

        try {
            const response = await fetch(`<?= base_url('api/admin/habits/student') ?>?student_id=${studentId}&month=${month}&year=${year}`, {
                credentials: 'same-origin'
            });
            const data = await response.json();

            if (data.status === 'success') {
                const monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

                const studentName = data.data.student ? data.data.student.name : '';
                const className = data.data.class ? data.data.class.name : '';

                document.getElementById('monthlyTitle').textContent = `Rekap Bulanan - ${studentName}`;
                document.getElementById('monthlySubtitle').textContent = `${className} | ${monthNames[parseInt(month)]} ${year}`;

                const overallPct = data.data.overall_percentage;
                const pctSpan = document.getElementById('overallPct');
                pctSpan.textContent = `${overallPct}%`;
                pctSpan.className = 'inline-block px-3 py-1 rounded-full text-sm font-bold ' +
                    (overallPct >= 80 ? 'text-green-600 bg-green-100' :
                        overallPct >= 50 ? 'text-yellow-600 bg-yellow-100' :
                        overallPct > 0 ? 'text-red-600 bg-red-100' : 'text-gray-400 bg-gray-100');

                renderMonthlyTable(data.data.dates);
            }
        } catch (error) {
            console.error('Error loading monthly data:', error);
        }
    }

    function renderMonthlyTable(dates) {
        const tbody = document.getElementById('monthlyTableBody');

        if (!dates || dates.length === 0) {
            tbody.innerHTML = '<tr><td colspan="10" class="text-center py-8 text-gray-500">Tidak ada data</td></tr>';
            return;
        }

        tbody.innerHTML = dates.map(d => {
            const isWeekend = d.is_weekend;
            const rowClass = isWeekend ? 'bg-gray-100 text-gray-400' :
                (d.has_data ? 'hover:bg-primary-50' : 'hover:bg-gray-50');

            const pctColor = d.percentage >= 80 ? 'text-green-600 bg-green-100' :
                d.percentage >= 50 ? 'text-yellow-600 bg-yellow-100' :
                d.percentage > 0 ? 'text-red-600 bg-red-100' : 'text-gray-400';

            return `
                <tr class="${rowClass} border-b border-gray-100 transition-colors">
                    <td class="py-2.5 px-3 font-medium">${d.day_name}</td>
                    <td class="py-2.5 px-3 text-center">${d.day}</td>
                    ${habitColumns.map(col => {
                        if (isWeekend) return '<td class="py-2.5 px-2 text-center">-</td>';
                        if (!d.has_data) return '<td class="py-2.5 px-2 text-center text-gray-300">-</td>';
                        const val = d[col];
                        if (val == 1) {
                            return '<td class="py-2.5 px-2 text-center text-green-600"><span class="material-symbols-outlined text-lg">check_circle</span></td>';
                        } else {
                            return '<td class="py-2.5 px-2 text-center text-red-400"><span class="material-symbols-outlined text-lg">cancel</span></td>';
                        }
                    }).join('')}
                    <td class="py-2.5 px-3 text-center">
                        ${isWeekend ? '<span class="text-gray-400">-</span>' :
                          d.has_data ? `<span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold ${pctColor}">${d.percentage}%</span>` :
                          '<span class="text-gray-300">-</span>'}
                    </td>
                </tr>
            `;
        }).join('');
    }
</script>

<?= $this->endSection() ?>