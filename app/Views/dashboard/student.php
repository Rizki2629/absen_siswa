<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_student') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Student Profile Card -->
<div class="card mb-8">
    <div class="relative">
        <!-- Cover Background -->
        <div class="h-32 bg-gradient-to-r from-primary-500 to-primary-700 rounded-t-xl"></div>

        <!-- Profile Content -->
        <div class="px-6 pb-6">
            <div class="flex flex-col md:flex-row md:items-end md:space-x-6">
                <!-- Profile Photo -->
                <div class="-mt-16 mb-4 md:mb-0">
                    <div class="bg-white p-2 rounded-xl inline-block shadow-lg">
                        <div class="bg-gradient-to-br from-primary-100 to-primary-200 rounded-lg p-6">
                            <span class="material-symbols-outlined text-primary-600" style="font-size: 80px;">account_circle</span>
                        </div>
                    </div>
                </div>

                <!-- Profile Info -->
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-900 mb-1"><?= esc($student['name'] ?? 'Nama Siswa') ?></h2>
                    <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-3">
                        <span class="flex items-center">
                            <span class="material-symbols-outlined text-sm mr-1">badge</span>
                            NIS: <?= esc($student['nis'] ?? '00000') ?>
                        </span>
                        <span class="flex items-center">
                            <span class="material-symbols-outlined text-sm mr-1">class</span>
                            <?= esc($student['class'] ?? 'Kelas') ?>
                        </span>
                        <span class="flex items-center">
                            <span class="material-symbols-outlined text-sm mr-1">school</span>
                            <?= esc($student['major'] ?? 'Jurusan') ?>
                        </span>
                    </div>
                </div>

                <!-- Today's Status -->
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <p class="text-xs text-gray-500 mb-2">Status Hari Ini</p>
                    <?php if (isset($todayAttendance)): ?>
                        <?php if ($todayAttendance['status'] === 'hadir' || $todayAttendance['status'] === 'terlambat'): ?>
                            <div class="flex items-center space-x-2">
                                <span class="badge-success text-base px-3 py-1">
                                    <span class="material-symbols-outlined text-sm mr-1">check_circle</span>
                                    Hadir
                                </span>
                            </div>
                            <div class="mt-2 space-y-1 text-xs text-gray-600">
                                <p class="flex items-center">
                                    <span class="material-symbols-outlined text-xs mr-1">login</span>
                                    Masuk: <?= $todayAttendance['check_in'] ?? '-' ?>
                                </p>
                                <p class="flex items-center">
                                    <span class="material-symbols-outlined text-xs mr-1">logout</span>
                                    Pulang: <?= $todayAttendance['check_out'] ?? 'Belum' ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <span class="badge-danger text-base px-3 py-1">
                                <span class="material-symbols-outlined text-sm mr-1">cancel</span>
                                Belum Hadir
                            </span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">

    <!-- Kehadiran Bulan Ini -->
    <div class="card hover:scale-105 transition-transform duration-300">
        <div class="card-body">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-success-100 rounded-full p-3">
                    <span class="material-symbols-outlined text-success-600 text-2xl">check_circle</span>
                </div>
                <span class="badge-success"><?= round(($stats['present'] ?? 0) / ($stats['total_days'] ?? 1) * 100) ?>%</span>
            </div>
            <p class="text-sm text-gray-500 mb-1">Kehadiran</p>
            <h3 class="text-2xl font-bold text-gray-900"><?= $stats['present'] ?? 0 ?> Hari</h3>
        </div>
    </div>

    <!-- Keterlambatan -->
    <div class="card hover:scale-105 transition-transform duration-300">
        <div class="card-body">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-warning-100 rounded-full p-3">
                    <span class="material-symbols-outlined text-warning-600 text-2xl">schedule</span>
                </div>
                <span class="badge-warning"><?= $stats['late'] ?? 0 ?>x</span>
            </div>
            <p class="text-sm text-gray-500 mb-1">Terlambat</p>
            <h3 class="text-2xl font-bold text-gray-900"><?= $stats['late'] ?? 0 ?> Hari</h3>
        </div>
    </div>

    <!-- Sakit -->
    <div class="card hover:scale-105 transition-transform duration-300">
        <div class="card-body">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-primary-100 rounded-full p-3">
                    <span class="material-symbols-outlined text-primary-600 text-2xl">medication</span>
                </div>
                <span class="badge-primary"><?= $stats['sick'] ?? 0 ?>x</span>
            </div>
            <p class="text-sm text-gray-500 mb-1">Sakit</p>
            <h3 class="text-2xl font-bold text-gray-900"><?= $stats['sick'] ?? 0 ?> Hari</h3>
        </div>
    </div>

    <!-- Alpha -->
    <div class="card hover:scale-105 transition-transform duration-300">
        <div class="card-body">
            <div class="flex items-center justify-between mb-3">
                <div class="bg-danger-100 rounded-full p-3">
                    <span class="material-symbols-outlined text-danger-600 text-2xl">cancel</span>
                </div>
                <span class="badge-danger"><?= $stats['absent'] ?? 0 ?>x</span>
            </div>
            <p class="text-sm text-gray-500 mb-1">Alpha</p>
            <h3 class="text-2xl font-bold text-gray-900"><?= $stats['absent'] ?? 0 ?> Hari</h3>
        </div>
    </div>

</div>

<!-- Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Attendance Chart -->
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h3 class="font-bold text-gray-900 flex items-center">
                    <span class="material-symbols-outlined mr-2 text-primary-600">bar_chart</span>
                    Grafik Kehadiran Bulan Ini
                </h3>
                <select class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-primary-500">
                    <option value="<?= date('m') ?>"><?= Date('F Y') ?></option>
                    <option value="<?= date('m', strtotime('-1 month')) ?>"><?= date('F Y', strtotime('-1 month')) ?></option>
                    <option value="<?= date('m', strtotime('-2 months')) ?>"><?= date('F Y', strtotime('-2 months')) ?></option>
                </select>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-7 gap-3">
                    <?php
                    for ($i = 1; $i <= 31; $i++):
                        $status = ['hadir', 'terlambat', 'alpha', 'sakit', 'hadir', 'hadir', 'izin'];
                        $randomStatus = $status[array_rand($status)];
                        $colors = [
                            'hadir' => 'bg-success-500',
                            'terlambat' => 'bg-warning-500',
                            'alpha' => 'bg-danger-500',
                            'sakit' => 'bg-primary-500',
                            'izin' => 'bg-info-500',
                            'weekend' => 'bg-gray-200'
                        ];
                        $color = $colors[$randomStatus];
                    ?>
                        <div class="text-center">
                            <div class="<?= $color ?> rounded-lg h-12 flex items-center justify-center mb-1 hover:scale-110 transition-transform cursor-pointer"
                                title="<?= ucfirst($randomStatus) ?>">
                                <span class="text-white text-xs font-medium"><?= $i ?></span>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
                <div class="flex items-center justify-center space-x-4 mt-6 text-xs">
                    <div class="flex items-center space-x-1">
                        <div class="w-3 h-3 bg-success-500 rounded"></div>
                        <span class="text-gray-600">Hadir</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <div class="w-3 h-3 bg-warning-500 rounded"></div>
                        <span class="text-gray-600">Terlambat</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <div class="w-3 h-3 bg-primary-500 rounded"></div>
                        <span class="text-gray-600">Sakit/Izin</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <div class="w-3 h-3 bg-danger-500 rounded"></div>
                        <span class="text-gray-600">Alpha</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <div class="w-3 h-3 bg-gray-200 rounded"></div>
                        <span class="text-gray-600">Libur</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Attendance -->
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h3 class="font-bold text-gray-900 flex items-center">
                    <span class="material-symbols-outlined mr-2 text-success-600">history</span>
                    Riwayat Kehadiran Terbaru
                </h3>
                <a href="<?= base_url('student/attendance') ?>" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                    Lihat Semua
                    <span class="material-symbols-outlined text-sm align-middle">arrow_forward</span>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="divide-y divide-gray-200">
                    <?php
                    $attendance = [
                        ['date' => '2026-02-07', 'check_in' => '07:15:23', 'check_out' => '13:45:10', 'status' => 'Tepat Waktu'],
                        ['date' => '2026-02-06', 'check_in' => '07:25:45', 'check_out' => '13:50:30', 'status' => 'Terlambat'],
                        ['date' => '2026-02-05', 'check_in' => '07:10:12', 'check_out' => '13:42:25', 'status' => 'Tepat Waktu'],
                        ['date' => '2026-02-04', 'check_in' => '07:12:35', 'check_out' => '13:48:15', 'status' => 'Tepat Waktu'],
                        ['date' => '2026-02-03', 'check_in' => '-', 'check_out' => '-', 'status' => 'Sakit'],
                    ];
                    foreach ($attendance as $att):
                    ?>
                        <div class="p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                                        <p class="text-xs text-gray-500"><?= date('M', strtotime($att['date'])) ?></p>
                                        <p class="text-2xl font-bold text-gray-900"><?= date('d', strtotime($att['date'])) ?></p>
                                        <p class="text-xs text-gray-500"><?= date('D', strtotime($att['date'])) ?></p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 mb-1"><?= date('l, d F Y', strtotime($att['date'])) ?></p>
                                        <div class="flex space-x-4 text-xs text-gray-600">
                                            <span class="flex items-center">
                                                <span class="material-symbols-outlined text-xs mr-1">login</span>
                                                Masuk: <?= $att['check_in'] ?>
                                            </span>
                                            <span class="flex items-center">
                                                <span class="material-symbols-outlined text-xs mr-1">logout</span>
                                                Pulang: <?= $att['check_out'] ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <?php if ($att['status'] === 'Tepat Waktu'): ?>
                                        <span class="badge-success"><?= $att['status'] ?></span>
                                    <?php elseif ($att['status'] === 'Terlambat'): ?>
                                        <span class="badge-warning"><?= $att['status'] ?></span>
                                    <?php elseif ($att['status'] === 'Sakit'): ?>
                                        <span class="badge-primary"><?= $att['status'] ?></span>
                                    <?php else: ?>
                                        <span class="badge-danger"><?= $att['status'] ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>

    <!-- Sidebar -->
    <div class="space-y-6">

        <!-- Percentage Card -->
        <div class="card bg-gradient-to-br from-primary-500 to-primary-700 text-white">
            <div class="card-body text-center">
                <span class="material-symbols-outlined text-6xl mb-3 block">workspace_premium</span>
                <p class="text-sm opacity-90 mb-2">Persentase Kehadiran</p>
                <h2 class="text-5xl font-bold mb-2"><?= round(($stats['present'] ?? 0) / ($stats['total_days'] ?? 1) * 100) ?>%</h2>
                <p class="text-xs opacity-75">Bulan <?= date('F Y') ?></p>
                <?php if (($stats['present'] ?? 0) / ($stats['total_days'] ?? 1) * 100 >= 90): ?>
                    <div class="mt-4 bg-white bg-opacity-20 rounded-lg p-3">
                        <p class="text-sm font-medium">🎉 Prestasi Luar Biasa!</p>
                        <p class="text-xs opacity-90 mt-1">Pertahankan kehadiranmu!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Notifications -->
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <h3 class="font-bold text-gray-900 flex items-center">
                    <span class="material-symbols-outlined mr-2 text-primary-600">notifications</span>
                    Notifikasi Terbaru
                </h3>
                <a href="<?= base_url('student/notifications') ?>" class="text-xs text-primary-600 hover:text-primary-700">
                    Semua
                </a>
            </div>
            <div class="card-body p-0">
                <div class="divide-y divide-gray-200">
                    <?php
                    $notifications = [
                        ['icon' => 'check_circle', 'color' => 'success', 'title' => 'Kehadiran Tercatat', 'time' => '2 jam lalu', 'desc' => 'Scan masuk berhasil pukul 07:15'],
                        ['icon' => 'info', 'color' => 'primary', 'title' => 'Pengumuman', 'time' => '1 hari lalu', 'desc' => 'Jadwal ujian akan dimulai minggu depan'],
                        ['icon' => 'warning', 'color' => 'warning', 'title' => 'Peringatan', 'time' => '2 hari lalu', 'desc' => 'Anda terlambat 3x bulan ini'],
                    ];
                    foreach ($notifications as $notif):
                    ?>
                        <div class="p-3 hover:bg-gray-50 transition-colors cursor-pointer">
                            <div class="flex space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-<?= $notif['color'] ?>-100 rounded-full p-2">
                                        <span class="material-symbols-outlined text-<?= $notif['color'] ?>-600 text-sm"><?= $notif['icon'] ?></span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900"><?= $notif['title'] ?></p>
                                    <p class="text-xs text-gray-500 mt-1"><?= $notif['desc'] ?></p>
                                    <p class="text-xs text-gray-400 mt-1"><?= $notif['time'] ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Tips -->
        <div class="alert-info">
            <span class="material-symbols-outlined text-xl">lightbulb</span>
            <div>
                <p class="font-medium mb-1">Tips</p>
                <p class="text-sm">Pastikan fingerprint Anda dalam kondisi bersih saat scan untuk hasil terbaik.</p>
            </div>
        </div>

    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Add interactivity here
    console.log('Student dashboard loaded');
</script>
<?= $this->endSection() ?>