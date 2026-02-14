<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<a href="<?= base_url('admin/dashboard') ?>" class="sidebar-item-active">
    <span class="material-symbols-outlined mr-3">dashboard</span>
    <span>Dashboard</span>
</a>
<a href="<?= base_url('admin/devices') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">devices</span>
    <span>Mesin Fingerprint</span>
</a>
<a href="<?= base_url('admin/device-mapping') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">link</span>
    <span>Mapping ID Mesin</span>
</a>
<a href="<?= base_url('admin/attendance-logs') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">description</span>
    <span>Log Absensi</span>
</a>
<a href="<?= base_url('admin/attendance') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">how_to_reg</span>
    <span>Daftar Hadir</span>
</a>
<a href="<?= base_url('admin/shifts') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">schedule</span>
    <span>Pengaturan Shift</span>
</a>
<a href="<?= base_url('admin/students') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">groups</span>
    <span>Data Siswa</span>
</a>
<a href="<?= base_url('admin/classes') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">class</span>
    <span>Data Kelas</span>
</a>
<a href="<?= base_url('admin/users') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">manage_accounts</span>
    <span>Manajemen User</span>
</a>
<a href="<?= base_url('admin/reports') ?>" class="sidebar-item">
    <span class="material-symbols-outlined mr-3">assessment</span>
    <span>Laporan</span>
</a>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Siswa -->
    <div class="card group hover:scale-105 transition-transform duration-300 border-l-4 border-primary-500">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Total Siswa</p>
                    <h3 class="text-3xl font-bold text-gray-900"><?= $stats['total_students'] ?? 0 ?></h3>
                </div>
                <div class="bg-primary-100 rounded-xl p-3 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-primary-600 text-3xl">groups</span>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="material-symbols-outlined text-success-600 text-sm mr-1">trending_up</span>
                <span class="text-success-600 font-medium mr-1">12%</span>
                <span class="text-gray-500">dari bulan lalu</span>
            </div>
        </div>
    </div>

    <!-- Hadir Hari Ini -->
    <div class="card group hover:scale-105 transition-transform duration-300 border-l-4 border-success-500">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Hadir Hari Ini</p>
                    <h3 class="text-3xl font-bold text-gray-900"><?= $stats['present_today'] ?? 0 ?></h3>
                </div>
                <div class="bg-success-100 rounded-xl p-3 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-success-600 text-3xl">check_circle</span>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-gray-500">
                    <?= round(($stats['present_today'] ?? 0) / max(1, $stats['total_students'] ?? 1) * 100, 1) ?>% dari total
                </span>
            </div>
        </div>
    </div>

    <!-- Mesin Aktif -->
    <div class="card group hover:scale-105 transition-transform duration-300 border-l-4 border-warning-500">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Mesin Aktif</p>
                    <h3 class="text-3xl font-bold text-gray-900"><?= $stats['active_devices'] ?? 0 ?>/<?= $stats['total_devices'] ?? 0 ?></h3>
                </div>
                <div class="bg-warning-100 rounded-xl p-3 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-warning-600 text-3xl">devices</span>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <div class="flex-1 bg-gray-200 rounded-full h-2">
                    <div class="bg-warning-500 h-2 rounded-full" style="width: <?= round(($stats['active_devices'] ?? 0) / max(1, $stats['total_devices'] ?? 1) * 100) ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpha Hari Ini -->
    <div class="card group hover:scale-105 transition-transform duration-300 border-l-4 border-danger-500">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium mb-1">Alpha Hari Ini</p>
                    <h3 class="text-3xl font-bold text-gray-900"><?= $stats['absent_today'] ?? 0 ?></h3>
                </div>
                <div class="bg-danger-100 rounded-xl p-3 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-danger-600 text-3xl">cancel</span>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-gray-500">
                    <?= round(($stats['absent_today'] ?? 0) / max(1, $stats['total_students'] ?? 1) * 100, 1) ?>% dari total
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Attendance Chart -->
    <div class="lg:col-span-2 card">
        <div class="card-header flex items-center justify-between">
            <h3 class="font-bold text-gray-900 flex items-center">
                <span class="material-symbols-outlined mr-2 text-primary-600">bar_chart</span>
                Statistik Kehadiran 7 Hari Terakhir
            </h3>
            <select class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-primary-500">
                <option>7 Hari Terakhir</option>
                <option>30 Hari Terakhir</option>
                <option>90 Hari Terakhir</option>
            </select>
        </div>
        <div class="card-body">
            <div class="space-y-4">
                <?php
                $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                $values = [85, 92, 88, 90, 87, 0, 0];
                foreach ($days as $index => $day):
                ?>
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700"><?= $day ?></span>
                            <span class="text-sm font-bold text-primary-600"><?= $values[$index] ?>%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-primary-500 to-primary-600 h-3 rounded-full transition-all duration-500 animate-slide-in"
                                style="width: <?= $values[$index] ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Device Status -->
    <div class="card">
        <div class="card-header">
            <h3 class="font-bold text-gray-900 flex items-center">
                <span class="material-symbols-outlined mr-2 text-warning-600">devices</span>
                Status Mesin
            </h3>
        </div>
        <div class="card-body">
            <div class="space-y-3">
                <?php
                $devices = [
                    ['name' => 'Mesin Gerbang Utama', 'status' => 'online'],
                    ['name' => 'Mesin Gedung A', 'status' => 'online'],
                    ['name' => 'Mesin Gedung B', 'status' => 'offline'],
                    ['name' => 'Mesin Kantin', 'status' => 'online'],
                ];
                foreach ($devices as $device):
                ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex items-center space-x-3">
                            <span class="material-symbols-outlined text-gray-600">router</span>
                            <span class="text-sm font-medium text-gray-900"><?= $device['name'] ?></span>
                        </div>
                        <?php if ($device['status'] === 'online'): ?>
                            <span class="badge-success">
                                <span class="inline-block w-2 h-2 bg-success-500 rounded-full mr-1 animate-pulse"></span>
                                Online
                            </span>
                        <?php else: ?>
                            <span class="badge-danger">
                                <span class="inline-block w-2 h-2 bg-danger-500 rounded-full mr-1"></span>
                                Offline
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="<?= base_url('admin/devices') ?>" class="btn-primary w-full mt-4">
                <span class="material-symbols-outlined mr-2 text-sm">settings</span>
                Kelola Mesin
            </a>
        </div>
    </div>
</div>

<!-- Recent Logs -->
<div class="card">
    <div class="card-header flex items-center justify-between">
        <h3 class="font-bold text-gray-900 flex items-center">
            <span class="material-symbols-outlined mr-2 text-success-600">history</span>
            Log Absensi Terbaru
        </h3>
        <a href="<?= base_url('admin/logs') ?>" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
            Lihat Semua
            <span class="material-symbols-outlined text-sm align-middle">arrow_forward</span>
        </a>
    </div>
    <div class="card-body p-0">
        <div class="overflow-x-auto">
            <table class="table">
                <thead class="table-header">
                    <tr>
                        <th class="table-header-cell">Waktu</th>
                        <th class="table-header-cell">Siswa</th>
                        <th class="table-header-cell">Kelas</th>
                        <th class="table-header-cell">Mesin</th>
                        <th class="table-header-cell">Tipe</th>
                        <th class="table-header-cell">Status</th>
                    </tr>
                </thead>
                <tbody class="table-body">
                    <?php
                    $logs = [
                        ['time' => '07:15:23', 'name' => 'Ahmad Fauzi', 'class' => 'XII RPL 1', 'device' => 'Gerbang Utama', 'type' => 'Masuk', 'status' => 'Tepat Waktu'],
                        ['time' => '07:16:45', 'name' => 'Siti Nurhaliza', 'class' => 'XII RPL 1', 'device' => 'Gerbang Utama', 'type' => 'Masuk', 'status' => 'Tepat Waktu'],
                        ['time' => '07:18:12', 'name' => 'Budi Santoso', 'class' => 'XI TKJ 2', 'device' => 'Gedung A', 'type' => 'Masuk', 'status' => 'Terlambat'],
                        ['time' => '13:45:30', 'name' => 'Dewi Lestari', 'class' => 'X MM 1', 'device' => 'Gerbang Utama', 'type' => 'Pulang', 'status' => 'Normal'],
                        ['time' => '13:47:15', 'name' => 'Eko Prasetyo', 'class' => 'XII RPL 2', 'device' => 'Gerbang Utama', 'type' => 'Pulang', 'status' => 'Normal'],
                    ];
                    foreach ($logs as $log):
                    ?>
                        <tr class="hover:bg-gray-50 transition-colors animate-fade-in">
                            <td class="table-cell">
                                <span class="flex items-center text-gray-600">
                                    <span class="material-symbols-outlined text-sm mr-1">schedule</span>
                                    <?= $log['time'] ?>
                                </span>
                            </td>
                            <td class="table-cell font-medium"><?= $log['name'] ?></td>
                            <td class="table-cell text-gray-600"><?= $log['class'] ?></td>
                            <td class="table-cell">
                                <span class="flex items-center text-gray-600">
                                    <span class="material-symbols-outlined text-sm mr-1">router</span>
                                    <?= $log['device'] ?>
                                </span>
                            </td>
                            <td class="table-cell">
                                <?php if ($log['type'] === 'Masuk'): ?>
                                    <span class="badge-primary">
                                        <span class="material-symbols-outlined text-xs mr-1">login</span>
                                        <?= $log['type'] ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge-secondary">
                                        <span class="material-symbols-outlined text-xs mr-1">logout</span>
                                        <?= $log['type'] ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="table-cell">
                                <?php if ($log['status'] === 'Tepat Waktu' || $log['status'] === 'Normal'): ?>
                                    <span class="badge-success"><?= $log['status'] ?></span>
                                <?php else: ?>
                                    <span class="badge-warning"><?= $log['status'] ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Auto refresh data setiap 30 detik
    // setInterval(() => {
    //     location.reload();
    // }, 30000);
</script>
<?= $this->endSection() ?>