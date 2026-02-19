<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_student') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$totalPresent = (int)($stats['present'] ?? 0);
$totalLate    = (int)($stats['late'] ?? 0);
$totalSick    = (int)(($stats['sick'] ?? 0) + ($stats['izin'] ?? 0));
$totalAbsent  = (int)($stats['absent'] ?? 0);
$totalDays    = max(1, (int)($stats['total_days'] ?? 20));
$attendanceRate = round(($totalPresent / $totalDays) * 100);

$dayNames   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
$monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$now        = new DateTime();
$todayLabel = $dayNames[(int)$now->format('w')] . ', ' . $now->format('d') . ' ' . $monthNames[(int)$now->format('n') - 1] . ' ' . $now->format('Y');
$firstName  = explode(' ', $student['name'] ?? 'Siswa')[0];

// Status helpers
$todayStatus    = $todayAttendance['status'] ?? null;
$isHadir        = in_array($todayStatus, ['hadir', 'terlambat']);
$isAlpha        = $todayStatus === 'alpha';
$statusLabel    = $isHadir ? ($todayStatus === 'terlambat' ? 'Terlambat' : 'Hadir') : ($isAlpha ? 'Alpha' : ($todayStatus ? ucfirst($todayStatus) : 'Belum Tercatat'));
$statusColor    = $isHadir ? ($todayStatus === 'terlambat' ? 'warning' : 'success') : ($isAlpha ? 'danger' : 'primary');
$statusIcon     = $isHadir ? ($todayStatus === 'terlambat' ? 'timer' : 'check_circle') : ($isAlpha ? 'cancel' : 'schedule');
?>

<!-- ═══════════════════════════════════════════════════════
     WELCOME HERO BANNER
     ═══════════════════════════════════════════════════════ -->
<div class="relative overflow-hidden rounded-2xl mb-6 shadow-lg"
     style="background: linear-gradient(135deg, #4338ca 0%, #6366f1 50%, #818cf8 100%);">
    <!-- Decorative blobs -->
    <div class="absolute -top-10 -right-10 w-48 h-48 rounded-full opacity-10 bg-white"></div>
    <div class="absolute bottom-0 right-20 w-28 h-28 rounded-full opacity-10 bg-white" style="transform:translateY(40%);"></div>
    <div class="absolute top-1/2 right-4 w-12 h-12 rounded-full opacity-15 bg-white" style="transform:translateY(-50%);"></div>

    <div class="relative px-5 py-6 md:px-8 md:py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">

            <!-- Left: Greeting -->
            <div class="text-white">
                <p class="text-sm font-medium mb-1" style="color:rgba(199,210,254,0.9);">
                    <span class="material-symbols-outlined text-sm align-middle mr-1">calendar_today</span>
                    <?= $todayLabel ?>
                </p>
                <h1 class="text-2xl md:text-3xl font-extrabold mb-3 tracking-tight">
                    Halo, <?= esc($firstName) ?>! 👋
                </h1>
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-full"
                          style="background:rgba(255,255,255,0.18);color:#e0e7ff;">
                        <span class="material-symbols-outlined text-xs">badge</span>
                        NIS: <?= esc($student['nis'] ?? '-') ?>
                    </span>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-full"
                          style="background:rgba(255,255,255,0.18);color:#e0e7ff;">
                        <span class="material-symbols-outlined text-xs">class</span>
                        <?= esc($student['class'] ?? '-') ?>
                    </span>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-full"
                          style="background:rgba(255,255,255,0.18);color:#e0e7ff;">
                        <span class="material-symbols-outlined text-xs">school</span>
                        <?= esc($student['major'] ?? '-') ?>
                    </span>
                </div>
            </div>

            <!-- Right: Today's Status Box -->
            <div class="rounded-xl p-4 min-w-[210px]"
                 style="background:rgba(255,255,255,0.15);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.25);">
                <p class="text-xs font-bold uppercase tracking-widest mb-3" style="color:rgba(199,210,254,0.9);">
                    Status Hari Ini
                </p>

                <?php if ($todayStatus): ?>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="rounded-full p-2 flex-shrink-0
                            <?= $statusColor === 'success' ? 'bg-success-400' : ($statusColor === 'warning' ? 'bg-warning-400' : ($statusColor === 'danger' ? 'bg-danger-400' : 'bg-primary-300')) ?>">
                            <span class="material-symbols-outlined text-white" style="font-size:20px;"><?= $statusIcon ?></span>
                        </div>
                        <span class="text-white font-bold text-lg leading-tight"><?= $statusLabel ?></span>
                    </div>
                    <?php if ($isHadir): ?>
                        <div class="space-y-1.5 text-xs" style="color:rgba(199,210,254,0.9);">
                            <p class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-xs">login</span>
                                Masuk: <span class="font-semibold text-white"><?= esc($todayAttendance['check_in'] ?? '-') ?></span>
                            </p>
                            <p class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-xs">logout</span>
                                Pulang: <span class="font-semibold text-white"><?= esc($todayAttendance['check_out'] ?? 'Belum') ?></span>
                            </p>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="flex items-center gap-3">
                        <div class="bg-warning-400 rounded-full p-2 flex-shrink-0">
                            <span class="material-symbols-outlined text-white" style="font-size:20px;">schedule</span>
                        </div>
                        <div>
                            <p class="text-white font-bold">Belum Tercatat</p>
                            <p class="text-xs mt-0.5" style="color:rgba(199,210,254,0.8);">Data belum tersedia</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════
     MONTHLY STATS GRID
     ═══════════════════════════════════════════════════════ -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <!-- Hadir -->
    <div class="card group hover:scale-105 transition-all duration-300 border-l-4 border-success-500">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium mb-1">Hadir Bulan Ini</p>
                    <h3 class="text-3xl font-bold text-gray-900"><?= $totalPresent ?></h3>
                    <p class="text-xs text-gray-400 mt-1">hari</p>
                </div>
                <div class="bg-success-100 rounded-xl p-3 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-success-600 text-3xl">check_circle</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Terlambat -->
    <div class="card group hover:scale-105 transition-all duration-300 border-l-4 border-warning-500">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium mb-1">Terlambat</p>
                    <h3 class="text-3xl font-bold text-gray-900"><?= $totalLate ?></h3>
                    <p class="text-xs text-gray-400 mt-1">hari</p>
                </div>
                <div class="bg-warning-100 rounded-xl p-3 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-warning-600 text-3xl">timer</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sakit / Izin -->
    <div class="card group hover:scale-105 transition-all duration-300 border-l-4 border-primary-400">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium mb-1">Sakit / Izin</p>
                    <h3 class="text-3xl font-bold text-gray-900"><?= $totalSick ?></h3>
                    <p class="text-xs text-gray-400 mt-1">hari</p>
                </div>
                <div class="bg-primary-100 rounded-xl p-3 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-primary-500 text-3xl">medical_information</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpha -->
    <div class="card group hover:scale-105 transition-all duration-300 border-l-4 border-danger-500">
        <div class="card-body">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium mb-1">Alpha</p>
                    <h3 class="text-3xl font-bold text-gray-900"><?= $totalAbsent ?></h3>
                    <p class="text-xs text-gray-400 mt-1">hari</p>
                </div>
                <div class="bg-danger-100 rounded-xl p-3 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-danger-600 text-3xl">cancel</span>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ═══════════════════════════════════════════════════════
     ATTENDANCE RATE + QUICK ACCESS
     ═══════════════════════════════════════════════════════ -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Circular Attendance Rate -->
    <div class="card">
        <div class="card-header">
            <h3 class="font-bold text-gray-900 flex items-center">
                <span class="material-symbols-outlined mr-2 text-primary-600">analytics</span>
                Tingkat Kehadiran
            </h3>
        </div>
        <div class="card-body flex flex-col items-center justify-center py-6">
            <!-- SVG Circle Progress (circumference ≈ 100 for r=15.9) -->
            <div class="relative w-36 h-36 mb-4">
                <svg class="w-full h-full" style="transform:rotate(-90deg);" viewBox="0 0 36 36">
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e0e7ff" stroke-width="2.5"/>
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#6366f1" stroke-width="2.5"
                            stroke-dasharray="<?= $attendanceRate ?> <?= 100 - $attendanceRate ?>"
                            stroke-linecap="round"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-2xl font-extrabold text-gray-900"><?= $attendanceRate ?>%</span>
                    <span class="text-xs text-gray-500">Kehadiran</span>
                </div>
            </div>

            <p class="text-sm text-gray-600 text-center mb-3">
                <?= $totalPresent ?> dari <strong><?= $totalDays ?></strong> hari sekolah bulan ini
            </p>

            <?php if ($attendanceRate >= 90): ?>
                <span class="inline-flex items-center gap-1 bg-success-100 text-success-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                    <span class="material-symbols-outlined text-xs">star</span>
                    Sangat Baik!
                </span>
            <?php elseif ($attendanceRate >= 75): ?>
                <span class="inline-flex items-center gap-1 bg-warning-100 text-warning-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                    <span class="material-symbols-outlined text-xs">thumb_up</span>
                    Pertahankan!
                </span>
            <?php else: ?>
                <span class="inline-flex items-center gap-1 bg-danger-100 text-danger-700 text-xs font-semibold px-3 py-1.5 rounded-full">
                    <span class="material-symbols-outlined text-xs">warning</span>
                    Perlu Ditingkatkan
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Access Cards (2-col on lg) -->
    <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">

        <!-- Riwayat Kehadiran -->
        <a href="<?= base_url('student/attendance') ?>"
           class="card hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group cursor-pointer">
            <div class="card-body flex flex-col items-center justify-center py-8 text-center">
                <div class="bg-primary-100 rounded-2xl p-4 mb-3 group-hover:bg-primary-200 transition-colors">
                    <span class="material-symbols-outlined text-primary-600 text-4xl">calendar_month</span>
                </div>
                <h4 class="font-bold text-gray-900 mb-1 text-sm">Riwayat Kehadiran</h4>
                <p class="text-xs text-gray-500">Lihat rekap absensi</p>
            </div>
        </a>

        <!-- 7 Kebiasaan -->
        <a href="<?= base_url('student/habits') ?>"
           class="card hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group cursor-pointer">
            <div class="card-body flex flex-col items-center justify-center py-8 text-center">
                <div class="bg-success-100 rounded-2xl p-4 mb-3 group-hover:bg-success-200 transition-colors">
                    <span class="material-symbols-outlined text-success-600 text-4xl">emoji_people</span>
                </div>
                <h4 class="font-bold text-gray-900 mb-1 text-sm">7 Kebiasaan</h4>
                <p class="text-xs text-gray-500">Pantau kebiasaan harian</p>
            </div>
        </a>

        <!-- Notifikasi -->
        <a href="<?= base_url('student/notifications') ?>"
           class="card hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group cursor-pointer">
            <div class="card-body flex flex-col items-center justify-center py-8 text-center">
                <div class="relative bg-warning-100 rounded-2xl p-4 mb-3 group-hover:bg-warning-200 transition-colors inline-block">
                    <span class="material-symbols-outlined text-warning-600 text-4xl">notifications</span>
                    <?php if (isset($unreadNotifications) && $unreadNotifications > 0): ?>
                        <span class="absolute -top-1 -right-1 bg-danger-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center leading-none">
                            <?= $unreadNotifications > 9 ? '9+' : $unreadNotifications ?>
                        </span>
                    <?php endif; ?>
                </div>
                <h4 class="font-bold text-gray-900 mb-1 text-sm">Notifikasi</h4>
                <p class="text-xs text-gray-500">
                    <?php if (isset($unreadNotifications) && $unreadNotifications > 0): ?>
                        <?= $unreadNotifications ?> pesan baru
                    <?php else: ?>
                        Tidak ada notifikasi baru
                    <?php endif; ?>
                </p>
            </div>
        </a>

    </div>

</div>

<?= $this->endSection() ?>