<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_guru_piket') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Hadir -->
    <div class="card group hover:scale-105 transition-transform duration-300 border-l-4 border-success-500">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Hadir</p>
                    <h3 class="text-3xl font-bold text-success-600"><?= $stats['present'] ?? 0 ?></h3>
                </div>
                <div class="bg-success-100 rounded-xl p-3 group-hover:scale-110 transition-transform">
                    <span class="material-symbols text-success-600 text-3xl">check_circle</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sakit -->
    <div class="card group hover:scale-105 transition-transform duration-300 border-l-4 border-warning-500">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Sakit</p>
                    <h3 class="text-3xl font-bold text-warning-600"><?= $stats['sick'] ?? 0 ?></h3>
                </div>
                <div class="bg-warning-100 rounded-xl p-3 group-hover:scale-110 transition-transform">
                    <span class="material-symbols text-warning-600 text-3xl">medication</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Izin -->
    <div class="card group hover:scale-105 transition-transform duration-300 border-l-4 border-primary-500">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Izin</p>
                    <h3 class="text-3xl font-bold text-primary-600"><?= $stats['permission'] ?? 0 ?></h3>
                </div>
                <div class="bg-primary-100 rounded-xl p-3 group-hover:scale-110 transition-transform">
                    <span class="material-symbols text-primary-600 text-3xl">mail</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpha -->
    <div class="card group hover:scale-105 transition-transform duration-300 border-l-4 border-danger-500">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Alpha</p>
                    <h3 class="text-3xl font-bold text-danger-600"><?= $stats['absent'] ?? 0 ?></h3>
                </div>
                <div class="bg-danger-100 rounded-xl p-3 group-hover:scale-110 transition-transform">
                    <span class="material-symbols text-danger-600 text-3xl">cancel</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Live Monitoring -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Scan Terbaru -->
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h3 class="font-bold text-gray-900 flex items-center">
                    <span class="material-symbols mr-2 text-success-600 animate-pulse">sensors</span>
                    Scan Terbaru (Real-time)
                </h3>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-success-100 text-success-800 text-xs font-medium">
                        <span class="inline-block w-2 h-2 bg-success-500 rounded-full mr-2 animate-pulse"></span>
                        Live
                    </span>
                    <button onclick="toggleSound()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors" title="Toggle Sound">
                        <span class="material-symbols text-gray-600" id="soundIcon">volume_up</span>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="recentScans" class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                    <?php
                    $scans = [
                        ['time' => '07:25:15', 'name' => 'Andi Wirawan', 'nis' => '12345', 'class' => 'XII RPL 1', 'status' => 'Tepat Waktu', 'photo' => 'success'],
                        ['time' => '07:26:30', 'name' => 'Sarah Putri', 'nis' => '12346', 'class' => 'XI TKJ 2', 'status' => 'Tepat Waktu', 'photo' => 'success'],
                        ['time' => '07:31:45', 'name' => 'Rizky Ramadhan', 'nis' => '12347', 'class' => 'X MM 1', 'status' => 'Terlambat', 'photo' => 'warning'],
                    ];
                    foreach ($scans as $scan):
                    ?>
                        <div class="p-4 hover:bg-gray-50 transition-colors animate-slide-in">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="bg-<?= $scan['photo'] ?>-100 rounded-full p-3">
                                        <span class="material-symbols text-<?= $scan['photo'] ?>-600 text-2xl">person</span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <p class="text-sm font-bold text-gray-900"><?= $scan['name'] ?></p>
                                        <span class="text-xs text-gray-500 flex items-center">
                                            <span class="material-symbols text-xs mr-1">schedule</span>
                                            <?= $scan['time'] ?>
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-3 text-xs text-gray-500">
                                        <span class="flex items-center">
                                            <span class="material-symbols text-xs mr-1">badge</span>
                                            <?= $scan['nis'] ?>
                                        </span>
                                        <span class="flex items-center">
                                            <span class="material-symbols text-xs mr-1">class</span>
                                            <?= $scan['class'] ?>
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <?php if ($scan['status'] === 'Tepat Waktu'): ?>
                                        <span class="badge-success">
                                            <span class="material-symbols text-xs mr-1">check</span>
                                            <?= $scan['status'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-warning">
                                            <span class="material-symbols text-xs mr-1">warning</span>
                                            <?= $scan['status'] ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Siswa Belum Scan -->
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h3 class="font-bold text-gray-900 flex items-center">
                    <span class="material-symbols mr-2 text-danger-600">pending</span>
                    Siswa Belum Scan (<?= $stats['not_scanned'] ?? 0 ?>)
                </h3>
                <div class="flex space-x-2">
                    <button class="btn-secondary text-xs py-1.5 px-3">
                        <span class="material-symbols text-xs mr-1">filter_alt</span>
                        Filter Kelas
                    </button>
                    <button onclick="showAddExceptionModal()" class="btn-primary text-xs py-1.5 px-3">
                        <span class="material-symbols text-xs mr-1">add</span>
                        Input Manual
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead class="table-header">
                            <tr>
                                <th class="table-header-cell">NIS</th>
                                <th class="table-header-cell">Nama</th>
                                <th class="table-header-cell">Kelas</th>
                                <th class="table-header-cell">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-body">
                            <?php
                            $notScanned = [
                                ['nis' => '12350', 'name' => 'Dedi Kurniawan', 'class' => 'XII RPL 2'],
                                ['nis' => '12351', 'name' => 'Fitri Handayani', 'class' => 'XI TKJ 1'],
                                ['nis' => '12352', 'name' => 'Galih Pratama', 'class' => 'X MM 2'],
                            ];
                            foreach ($notScanned as $student):
                            ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="table-cell font-mono text-sm"><?= $student['nis'] ?></td>
                                    <td class="table-cell font-medium"><?= $student['name'] ?></td>
                                    <td class="table-cell text-gray-600"><?= $student['class'] ?></td>
                                    <td class="table-cell">
                                        <div class="flex space-x-1">
                                            <button onclick="markAs('sick', '<?= $student['nis'] ?>')"
                                                class="p-1.5 text-warning-600 hover:bg-warning-50 rounded transition-colors"
                                                title="Tandai Sakit">
                                                <span class="material-symbols text-sm">medication</span>
                                            </button>
                                            <button onclick="markAs('permission', '<?= $student['nis'] ?>')"
                                                class="p-1.5 text-primary-600 hover:bg-primary-50 rounded transition-colors"
                                                title="Tandai Izin">
                                                <span class="material-symbols text-sm">mail</span>
                                            </button>
                                            <button onclick="markAs('forgot', '<?= $student['nis'] ?>')"
                                                class="p-1.5 text-success-600 hover:bg-success-50 rounded transition-colors"
                                                title="Lupa Scan">
                                                <span class="material-symbols text-sm">edit</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Sidebar Info -->
    <div class="space-y-6">

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h3 class="font-bold text-gray-900 flex items-center">
                    <span class="material-symbols mr-2">bolt</span>
                    Aksi Cepat
                </h3>
            </div>
            <div class="card-body space-y-2">
                <button class="btn-primary w-full justify-center">
                    <span class="material-symbols mr-2">download</span>
                    Export Rekap Hari Ini
                </button>
                <button class="btn-secondary w-full justify-center">
                    <span class="material-symbols mr-2">print</span>
                    Cetak Laporan
                </button>
                <button onclick="showAddExceptionModal()" class="btn-outline w-full justify-center">
                    <span class="material-symbols mr-2">add_circle</span>
                    Input Ketidakhadiran
                </button>
            </div>
        </div>

        <!-- Statistics Pie -->
        <div class="card">
            <div class="card-header">
                <h3 class="font-bold text-gray-900 flex items-center">
                    <span class="material-symbols mr-2">pie_chart</span>
                    Distribusi Kehadiran
                </h3>
            </div>
            <div class="card-body">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-success-500 rounded"></div>
                            <span class="text-sm text-gray-700">Hadir</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900">85%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-warning-500 rounded"></div>
                            <span class="text-sm text-gray-700">Sakit</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900">8%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-primary-500 rounded"></div>
                            <span class="text-sm text-gray-700">Izin</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900">5%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-danger-500 rounded"></div>
                            <span class="text-sm text-gray-700">Alpha</span>
                        </div>
                        <span class="text-sm font-bold text-gray-900">2%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info -->
        <div class="alert-info">
            <span class="material-symbols text-xl">info</span>
            <div>
                <p class="font-medium mb-1">Tips Guru Piket</p>
                <p class="text-sm">Segera input ketidakhadiran sebelum jam 08:00 agar data lebih akurat.</p>
            </div>
        </div>

    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let soundEnabled = true;

    function toggleSound() {
        soundEnabled = !soundEnabled;
        const icon = document.getElementById('soundIcon');
        icon.textContent = soundEnabled ? 'volume_up' : 'volume_off';
    }

    function markAs(type, nis) {
        const types = {
            'sick': 'Sakit',
            'permission': 'Izin',
            'forgot': 'Lupa Scan'
        };

        if (confirm(`Tandai siswa ${nis} sebagai ${types[type]}?`)) {
            // Ajax call here
            console.log(`Mark ${nis} as ${type}`);
        }
    }

    function showAddExceptionModal() {
        alert('Modal input ketidakhadiran akan muncul di sini');
    }

    // Auto refresh every 5 seconds
    // setInterval(() => {
    //     // Fetch new scans via AJAX
    //     console.log('Refreshing...');
    // }, 5000);
</script>
<?= $this->endSection() ?>