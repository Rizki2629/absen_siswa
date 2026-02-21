<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_guru_piket') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Rekap Harian</h2>
        <p class="text-gray-500 mt-1">Laporan kehadiran siswa per hari</p>
    </div>
    <div class="flex items-center border border-gray-300 rounded-xl overflow-hidden divide-x divide-gray-300">
        <button onclick="changeDate(-1)" class="px-4 py-2.5 hover:bg-gray-100 transition-colors bg-white">
            <span class="material-symbols text-gray-600">chevron_left</span>
        </button>
        <div class="px-4 py-2 bg-white">
            <input type="date" id="recapDate" value="<?= date('Y-m-d') ?>"
                onchange="loadRecap(this.value)"
                class="text-sm font-medium text-gray-700 border-0 outline-none cursor-pointer bg-transparent">
        </div>
        <button onclick="changeDate(1)" class="px-4 py-2.5 hover:bg-gray-100 transition-colors bg-white">
            <span class="material-symbols text-gray-600">chevron_right</span>
        </button>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6" id="summaryCards">
    <div class="card border-l-4 border-success-500">
        <div class="card-body py-3">
            <p class="text-xs text-gray-500">Hadir</p>
            <h3 class="text-2xl font-bold text-success-600" id="cntHadir">-</h3>
        </div>
    </div>
    <div class="card border-l-4 border-warning-500">
        <div class="card-body py-3">
            <p class="text-xs text-gray-500">Sakit</p>
            <h3 class="text-2xl font-bold text-warning-600" id="cntSakit">-</h3>
        </div>
    </div>
    <div class="card border-l-4 border-primary-500">
        <div class="card-body py-3">
            <p class="text-xs text-gray-500">Izin</p>
            <h3 class="text-2xl font-bold text-primary-600" id="cntIzin">-</h3>
        </div>
    </div>
    <div class="card border-l-4 border-danger-500">
        <div class="card-body py-3">
            <p class="text-xs text-gray-500">Alpha</p>
            <h3 class="text-2xl font-bold text-danger-600" id="cntAlpha">-</h3>
        </div>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-body p-0">
        <div id="tableContainer" class="overflow-x-auto">
            <div class="p-8 text-center text-gray-400">
                <span class="material-symbols text-4xl mb-2 block">hourglass_top</span>
                <p>Memuat data...</p>
            </div>
        </div>
    </div>
</div>

<script>
    function changeDate(delta) {
        const input = document.getElementById('recapDate');
        const d = new Date(input.value);
        d.setDate(d.getDate() + delta);
        input.value = d.toISOString().split('T')[0];
        loadRecap(input.value);
    }

    function loadRecap(date) {
        const container = document.getElementById('tableContainer');
        container.innerHTML = '<div class="p-8 text-center text-gray-400"><span class="material-symbols text-4xl mb-2 block">hourglass_top</span><p>Memuat data...</p></div>';

        fetch(`/api/guru-piket/daily-summary?date=${date}`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                const rows = data.data ?? [];
                document.getElementById('cntHadir').textContent = rows.filter(r => r.status === 'hadir' || r.status === 'terlambat').length;
                document.getElementById('cntSakit').textContent = rows.filter(r => r.status === 'sakit').length;
                document.getElementById('cntIzin').textContent = rows.filter(r => r.status === 'izin').length;
                document.getElementById('cntAlpha').textContent = rows.filter(r => r.status === 'alpha').length;

                if (rows.length === 0) {
                    container.innerHTML = '<div class="p-8 text-center text-gray-400"><span class="material-symbols text-4xl mb-2 block">inbox</span><p>Tidak ada data untuk tanggal ini.</p></div>';
                    return;
                }

                const statusBadge = s => {
                    const map = {
                        hadir: 'success',
                        terlambat: 'warning',
                        sakit: 'warning',
                        izin: 'primary',
                        alpha: 'danger'
                    };
                    const c = map[s] ?? 'gray';
                    return `<span class="px-2 py-0.5 rounded-full text-xs bg-${c}-100 text-${c}-700">${s}</span>`;
                };

                container.innerHTML = `
            <table class="table">
                <thead class="table-header">
                    <tr>
                        <th class="table-header-cell">No</th>
                        <th class="table-header-cell">NIS</th>
                        <th class="table-header-cell">Nama</th>
                        <th class="table-header-cell">Kelas</th>
                        <th class="table-header-cell">Masuk</th>
                        <th class="table-header-cell">Status</th>
                    </tr>
                </thead>
                <tbody class="table-body">
                    ${rows.map((r, i) => `
                        <tr class="hover:bg-gray-50">
                            <td class="table-cell text-gray-500">${i + 1}</td>
                            <td class="table-cell font-mono text-sm">${r.nis ?? '-'}</td>
                            <td class="table-cell font-medium">${r.student_name ?? '-'}</td>
                            <td class="table-cell text-gray-600">${r.class_name ?? '-'}</td>
                            <td class="table-cell font-mono text-sm">${r.check_in_time ?? '-'}</td>
                            <td class="table-cell">${statusBadge(r.status)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>`;
            })
            .catch(() => {
                container.innerHTML = '<div class="p-8 text-center text-danger-400"><span class="material-symbols text-4xl mb-2 block">error</span><p>Gagal memuat data.</p></div>';
            });
    }

    loadRecap(document.getElementById('recapDate').value);
</script>

<?= $this->endSection() ?>