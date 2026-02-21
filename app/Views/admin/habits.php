<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_admin') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">7 Kebiasaan Anak Indonesia Hebat</h2>
        <p class="text-gray-600 mt-1">Rekap kebiasaan harian siswa per kelas</p>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-2xl shadow p-4 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kelas</label>
            <select id="classFilter" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500">
                <option value="">-- Pilih Kelas --</option>
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

<!-- Recap Table -->
<div id="recapContainer" class="bg-white rounded-2xl shadow overflow-hidden" style="display: none;">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-bold text-gray-900" id="recapTitle">Rekap Kebiasaan</h3>
        <p class="text-sm text-gray-500" id="recapSubtitle"></p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-teal-600 text-white">
                    <th class="py-3 px-3 text-left font-semibold whitespace-nowrap" rowspan="2">Hari</th>
                    <th class="py-3 px-3 text-left font-semibold whitespace-nowrap" rowspan="2">Tanggal</th>
                    <th class="py-2 px-2 text-center font-semibold" colspan="7">7 Kebiasaan Anak Indonesia Hebat</th>
                    <th class="py-3 px-3 text-center font-semibold whitespace-nowrap" rowspan="2">%</th>
                </tr>
                <tr class="bg-teal-500 text-white text-xs">
                    <th class="py-2 px-2 text-center font-medium">Bangun Pagi</th>
                    <th class="py-2 px-2 text-center font-medium">Beribadah</th>
                    <th class="py-2 px-2 text-center font-medium">Berolahraga</th>
                    <th class="py-2 px-2 text-center font-medium">Makan Sehat</th>
                    <th class="py-2 px-2 text-center font-medium">Gemar Belajar</th>
                    <th class="py-2 px-2 text-center font-medium">Bermasyarakat</th>
                    <th class="py-2 px-2 text-center font-medium">Tidur Cepat</th>
                </tr>
            </thead>
            <tbody id="recapTableBody">
            </tbody>
        </table>
    </div>
</div>

<!-- Empty State -->
<div id="emptyState" class="bg-white rounded-2xl shadow p-12 text-center">
    <span class="material-symbols text-6xl text-teal-300 mb-4">emoji_people</span>
    <h3 class="text-lg font-semibold text-gray-700 mb-2">Pilih Kelas untuk Melihat Rekap</h3>
    <p class="text-gray-500">Pilih kelas dan periode untuk menampilkan rekap 7 kebiasaan anak Indonesia hebat</p>
</div>

<!-- Input Modal -->
<div id="habitInputModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 items-center justify-center p-4" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 bg-white z-10">
            <div>
                <h3 class="text-xl font-bold text-gray-900" id="habitModalTitle">Input Kebiasaan Harian</h3>
                <p class="text-sm text-gray-500" id="habitModalDate"></p>
            </div>
            <button onclick="closeHabitModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols">close</span>
            </button>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="py-3 px-3 text-left font-semibold">No</th>
                            <th class="py-3 px-3 text-left font-semibold">Nama Siswa</th>
                            <th class="py-2 px-2 text-center font-medium text-xs">Bangun<br>Pagi</th>
                            <th class="py-2 px-2 text-center font-medium text-xs">Beribadah</th>
                            <th class="py-2 px-2 text-center font-medium text-xs">Berolahraga</th>
                            <th class="py-2 px-2 text-center font-medium text-xs">Makan<br>Sehat</th>
                            <th class="py-2 px-2 text-center font-medium text-xs">Gemar<br>Belajar</th>
                            <th class="py-2 px-2 text-center font-medium text-xs">Bermasya-<br>rakat</th>
                            <th class="py-2 px-2 text-center font-medium text-xs">Tidur<br>Cepat</th>
                        </tr>
                    </thead>
                    <tbody id="habitInputBody">
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between items-center pt-4 mt-4 border-t border-gray-200">
                <div class="flex space-x-2">
                    <button type="button" onclick="checkAll(true)" class="text-sm px-3 py-1 bg-teal-100 text-teal-700 rounded-lg hover:bg-teal-200">
                        <span class="material-symbols text-sm mr-1">check_box</span> Centang Semua
                    </button>
                    <button type="button" onclick="checkAll(false)" class="text-sm px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        <span class="material-symbols text-sm mr-1">check_box_outline_blank</span> Hapus Semua
                    </button>
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="closeHabitModal()" class="btn-secondary">Batal</button>
                    <button type="button" onclick="saveHabits()" class="btn-primary bg-teal-600 hover:bg-teal-700">
                        <span class="material-symbols mr-2">save</span> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const habitColumns = ['bangun_pagi', 'beribadah', 'berolahraga', 'makan_sehat', 'gemar_belajar', 'bermasyarakat', 'tidur_cepat'];
    let currentStudents = [];
    let currentHabits = {};
    let selectedDate = null;

    document.addEventListener('DOMContentLoaded', function() {
        loadClasses();
        setupFilters();
    });

    function setupFilters() {
        // Set current month
        const now = new Date();
        document.getElementById('monthFilter').value = now.getMonth() + 1;

        // Populate year
        const yearSelect = document.getElementById('yearFilter');
        const currentYear = now.getFullYear();
        for (let y = currentYear - 2; y <= currentYear + 1; y++) {
            const opt = document.createElement('option');
            opt.value = y;
            opt.textContent = y;
            if (y === currentYear) opt.selected = true;
            yearSelect.appendChild(opt);
        }

        // Event listeners
        document.getElementById('classFilter').addEventListener('change', loadRecap);
        document.getElementById('monthFilter').addEventListener('change', loadRecap);
        document.getElementById('yearFilter').addEventListener('change', loadRecap);
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

    async function loadRecap() {
        const classId = document.getElementById('classFilter').value;
        const month = document.getElementById('monthFilter').value;
        const year = document.getElementById('yearFilter').value;

        if (!classId) {
            document.getElementById('recapContainer').style.display = 'none';
            document.getElementById('emptyState').style.display = 'block';
            return;
        }

        document.getElementById('emptyState').style.display = 'none';
        document.getElementById('recapContainer').style.display = 'block';

        try {
            const response = await fetch(`<?= base_url('api/admin/habits/recap') ?>?class_id=${classId}&month=${month}&year=${year}`, {
                credentials: 'same-origin'
            });
            const data = await response.json();

            if (data.status === 'success') {
                currentStudents = data.data.students;
                const className = data.data.class ? data.data.class.name : '';
                const monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

                document.getElementById('recapTitle').textContent = `Rekap 7 Kebiasaan - ${className}`;
                document.getElementById('recapSubtitle').textContent = `${monthNames[parseInt(month)]} ${year} | ${currentStudents.length} siswa`;

                renderRecapTable(data.data.dates, currentStudents.length);
            }
        } catch (error) {
            console.error('Error loading recap:', error);
        }
    }

    function renderRecapTable(dates, totalStudents) {
        const tbody = document.getElementById('recapTableBody');

        if (!dates || dates.length === 0) {
            tbody.innerHTML = '<tr><td colspan="10" class="text-center py-8 text-gray-500">Tidak ada data</td></tr>';
            return;
        }

        tbody.innerHTML = dates.map(d => {
            const isWeekend = d.is_weekend;
            const rowClass = isWeekend ? 'bg-gray-100 text-gray-400' : (d.has_data ? 'hover:bg-teal-50' : 'hover:bg-gray-50');
            const clickAttr = isWeekend ? '' : `onclick="openHabitInput('${d.date}', '${d.day_name}', ${d.day})" class="cursor-pointer"`;

            const pctColor = d.percentage >= 80 ? 'text-green-600 bg-green-100' :
                d.percentage >= 50 ? 'text-yellow-600 bg-yellow-100' :
                d.percentage > 0 ? 'text-red-600 bg-red-100' : 'text-gray-400';

            return `
                <tr class="${rowClass} border-b border-gray-100 transition-colors" ${clickAttr}>
                    <td class="py-2.5 px-3 font-medium">${d.day_name}</td>
                    <td class="py-2.5 px-3">${d.day}</td>
                    ${habitColumns.map(col => {
                        const val = d[col] || 0;
                        if (isWeekend) return '<td class="py-2.5 px-2 text-center">-</td>';
                        if (!d.has_data) return '<td class="py-2.5 px-2 text-center text-gray-300">-</td>';
                        const cellColor = val === totalStudents ? 'text-green-600 font-semibold' :
                                          val > 0 ? 'text-teal-600' : 'text-gray-300';
                        return `<td class="py-2.5 px-2 text-center ${cellColor}">${val}/${totalStudents}</td>`;
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

    async function openHabitInput(date, dayName, dayNum) {
        selectedDate = date;
        const classId = document.getElementById('classFilter').value;
        const month = document.getElementById('monthFilter').value;
        const year = document.getElementById('yearFilter').value;

        document.getElementById('habitModalTitle').textContent = `Input Kebiasaan - ${dayName}, ${dayNum}`;
        const monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        document.getElementById('habitModalDate').textContent = `${dayName}, ${dayNum} ${monthNames[parseInt(month)]} ${year}`;

        // Load existing data
        try {
            const response = await fetch(`<?= base_url('api/admin/habits') ?>?class_id=${classId}&month=${month}&year=${year}`, {
                credentials: 'same-origin'
            });
            const data = await response.json();

            if (data.status === 'success') {
                currentStudents = data.data.students;
                currentHabits = data.data.habits;
                renderHabitInput(date);
            }
        } catch (error) {
            console.error('Error:', error);
        }

        document.getElementById('habitInputModal').style.display = 'flex';
    }

    function renderHabitInput(date) {
        const tbody = document.getElementById('habitInputBody');

        tbody.innerHTML = currentStudents.map((student, idx) => {
            const habitData = (currentHabits[student.id] && currentHabits[student.id][date]) || {};

            return `
                <tr class="border-b border-gray-100 hover:bg-gray-50" data-student-id="${student.id}">
                    <td class="py-2 px-3 text-gray-500">${idx + 1}</td>
                    <td class="py-2 px-3 font-medium text-gray-900 whitespace-nowrap">${student.name}</td>
                    ${habitColumns.map(col => `
                        <td class="py-2 px-2 text-center">
                            <input type="checkbox" name="${col}" data-student="${student.id}" data-col="${col}"
                                ${habitData[col] == 1 ? 'checked' : ''}
                                class="w-5 h-5 text-teal-600 rounded focus:ring-teal-500 cursor-pointer">
                        </td>
                    `).join('')}
                </tr>
            `;
        }).join('');
    }

    function closeHabitModal() {
        document.getElementById('habitInputModal').style.display = 'none';
    }

    function checkAll(checked) {
        document.querySelectorAll('#habitInputBody input[type="checkbox"]').forEach(cb => {
            cb.checked = checked;
        });
    }

    async function saveHabits() {
        const records = [];

        currentStudents.forEach(student => {
            const record = {
                student_id: student.id
            };
            habitColumns.forEach(col => {
                const cb = document.querySelector(`input[data-student="${student.id}"][data-col="${col}"]`);
                record[col] = cb && cb.checked ? 1 : 0;
            });
            records.push(record);
        });

        try {
            const response = await fetch('<?= base_url('api/admin/habits/bulk') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    date: selectedDate,
                    records: records
                })
            });

            const result = await response.json();

            if (result.status === 'success') {
                alert(result.message);
                closeHabitModal();
                loadRecap();
            } else {
                alert(result.message || 'Gagal menyimpan data');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Gagal menyimpan data kebiasaan');
        }
    }
</script>

<?= $this->endSection() ?>