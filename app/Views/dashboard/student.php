<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_student') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$totalPresent   = (int)($stats['present'] ?? 0);
$totalLate      = (int)($stats['late'] ?? 0);
$totalSick      = (int)(($stats['sick'] ?? 0) + ($stats['izin'] ?? 0));
$totalAbsent    = (int)($stats['absent'] ?? 0);
$totalDays      = max(1, (int)($stats['total_days'] ?? 20));
$attendanceRate = round(($totalPresent / $totalDays) * 100);
/* ---- COMPACT_REDESIGN_V2 ---- */

$dayNames   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
$monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$now        = new DateTime();
$todayLabel = $dayNames[(int)$now->format('w')] . ', ' . $now->format('d') . ' ' . $monthNames[(int)$now->format('n') - 1] . ' ' . $now->format('Y');
$firstName  = explode(' ', $student['name'] ?? 'Siswa')[0];

$todayStatus = $todayAttendance['status'] ?? null;
$isHadir     = in_array($todayStatus, ['hadir', 'terlambat']);
$isAlpha     = $todayStatus === 'alpha';
$statusLabel = $isHadir ? ($todayStatus === 'terlambat' ? 'Terlambat' : 'Hadir') : ($isAlpha ? 'Alpha' : ($todayStatus ? ucfirst($todayStatus) : 'Belum Tercatat'));
$statusColor = $isHadir ? ($todayStatus === 'terlambat' ? 'warning' : 'success') : ($isAlpha ? 'danger' : 'primary');
$statusIcon  = $isHadir ? ($todayStatus === 'terlambat' ? 'timer' : 'check_circle') : ($isAlpha ? 'cancel' : 'schedule');
?>

<!-- HERO BANNER -->
<div class="relative overflow-hidden rounded-2xl mb-5 shadow-md"
     style="background: linear-gradient(135deg, #4338ca 0%, #6366f1 55%, #818cf8 100%);">
    <div class="absolute -top-8 -right-8 w-36 h-36 rounded-full opacity-10 bg-white pointer-events-none"></div>
    <div class="absolute bottom-0 right-14 w-20 h-20 rounded-full opacity-10 bg-white pointer-events-none" style="transform:translateY(40%);"></div>

    <div class="relative px-5 py-4 md:px-6 md:py-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            <!-- Greeting -->
            <div class="text-white min-w-0">
                <p class="text-xs font-medium mb-1 flex items-center gap-1" style="color:rgba(199,210,254,0.85);">
                    <span class="material-symbols-outlined" style="font-size:13px;">calendar_today</span>
                    <?= $todayLabel ?>
                </p>
                <h1 class="text-xl font-bold mb-2.5 tracking-tight">
                    Halo, <?= esc($firstName) ?>! 👋
                </h1>
                <div class="flex flex-wrap gap-1.5">
                    <span class="inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-full"
                          style="background:rgba(255,255,255,0.18);color:#e0e7ff;">
                        <span class="material-symbols-outlined" style="font-size:12px;">badge</span>
                        <?= esc($student['nis'] ?? '-') ?>
                    </span>
                    <span class="inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-full"
                          style="background:rgba(255,255,255,0.18);color:#e0e7ff;">
                        <span class="material-symbols-outlined" style="font-size:12px;">class</span>
                        <?= esc($student['class'] ?? '-') ?>
                    </span>
                    <span class="inline-flex items-center gap-1 text-xs font-medium px-2.5 py-1 rounded-full"
                          style="background:rgba(255,255,255,0.18);color:#e0e7ff;">
                        <span class="material-symbols-outlined" style="font-size:12px;">school</span>
                        <?= esc($student['major'] ?? '-') ?>
                    </span>
                </div>
            </div>

            <!-- Today Status -->
            <div class="rounded-xl px-4 py-3 flex-shrink-0"
                 style="background:rgba(255,255,255,0.15);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.25);min-width:180px;">
                <p class="text-xs font-bold uppercase tracking-widest mb-2" style="color:rgba(199,210,254,0.85);">Status Hari Ini</p>

                <?php if ($todayStatus): ?>
                    <div class="flex items-center gap-2 mb-2">
                        <div class="rounded-full p-1.5 flex-shrink-0
                            <?= $statusColor === 'success' ? 'bg-success-400' : ($statusColor === 'warning' ? 'bg-warning-400' : ($statusColor === 'danger' ? 'bg-danger-400' : 'bg-primary-300')) ?>">
                            <span class="material-symbols-outlined text-white" style="font-size:16px;"><?= $statusIcon ?></span>
                        </div>
                        <span class="text-white font-bold text-sm"><?= $statusLabel ?></span>
                    </div>
                    <?php if ($isHadir): ?>
                        <div class="space-y-1 text-xs" style="color:rgba(199,210,254,0.9);">
                            <p class="flex items-center gap-1">
                                <span class="material-symbols-outlined" style="font-size:12px;">login</span>
                                Masuk: <span class="font-semibold text-white ml-1"><?= esc($todayAttendance['check_in'] ?? '-') ?></span>
                            </p>
                            <p class="flex items-center gap-1">
                                <span class="material-symbols-outlined" style="font-size:12px;">logout</span>
                                Pulang: <span class="font-semibold text-white ml-1"><?= esc($todayAttendance['check_out'] ?? 'Belum') ?></span>
                            </p>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="flex items-center gap-2">
                        <div class="bg-warning-400 rounded-full p-1.5 flex-shrink-0">
                            <span class="material-symbols-outlined text-white" style="font-size:16px;">schedule</span>
                        </div>
                        <div>
                            <p class="text-white font-bold text-sm">Belum Tercatat</p>
                            <p class="text-xs mt-0.5" style="color:rgba(199,210,254,0.8);">Data belum tersedia</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<!-- STATS GRID -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">

    <!-- Hadir -->
    <div class="card border-l-4 border-success-500 hover:shadow-md transition-shadow">
        <div class="card-body py-3 px-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium mb-0.5">Hadir</p>
                    <h3 class="text-2xl font-bold text-gray-900 leading-none"><?= $totalPresent ?></h3>
                    <p class="text-xs text-gray-400 mt-0.5">hari</p>
                </div>
                <div class="bg-success-100 rounded-xl p-2.5">
                    <span class="material-symbols-outlined text-success-600" style="font-size:22px;">check_circle</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Terlambat -->
    <div class="card border-l-4 border-warning-500 hover:shadow-md transition-shadow">
        <div class="card-body py-3 px-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium mb-0.5">Terlambat</p>
                    <h3 class="text-2xl font-bold text-gray-900 leading-none"><?= $totalLate ?></h3>
                    <p class="text-xs text-gray-400 mt-0.5">hari</p>
                </div>
                <div class="bg-warning-100 rounded-xl p-2.5">
                    <span class="material-symbols-outlined text-warning-600" style="font-size:22px;">timer</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sakit / Izin -->
    <div class="card border-l-4 border-primary-400 hover:shadow-md transition-shadow">
        <div class="card-body py-3 px-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium mb-0.5">Sakit/Izin</p>
                    <h3 class="text-2xl font-bold text-gray-900 leading-none"><?= $totalSick ?></h3>
                    <p class="text-xs text-gray-400 mt-0.5">hari</p>
                </div>
                <div class="bg-primary-100 rounded-xl p-2.5">
                    <span class="material-symbols-outlined text-primary-500" style="font-size:22px;">medical_information</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpha -->
    <div class="card border-l-4 border-danger-500 hover:shadow-md transition-shadow">
        <div class="card-body py-3 px-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium mb-0.5">Alpha</p>
                    <h3 class="text-2xl font-bold text-gray-900 leading-none"><?= $totalAbsent ?></h3>
                    <p class="text-xs text-gray-400 mt-0.5">hari</p>
                </div>
                <div class="bg-danger-100 rounded-xl p-2.5">
                    <span class="material-symbols-outlined text-danger-600" style="font-size:22px;">cancel</span>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ATTENDANCE RATE + QUICK ACCESS -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    <!-- Attendance Rate Card -->
    <div class="card">
        <div class="card-header py-3 px-4">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-primary-600" style="font-size:18px;">analytics</span>
                Tingkat Kehadiran
            </h3>
        </div>
        <div class="card-body py-4 px-4 flex flex-col items-center">
            <div class="relative w-24 h-24 mb-3">
                <svg class="w-full h-full" style="transform:rotate(-90deg);" viewBox="0 0 36 36">
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e0e7ff" stroke-width="3"/>
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#6366f1" stroke-width="3"
                            stroke-dasharray="<?= $attendanceRate ?> <?= 100 - $attendanceRate ?>"
                            stroke-linecap="round"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-lg font-extrabold text-gray-900 leading-none"><?= $attendanceRate ?>%</span>
                    <span class="text-xs text-gray-400 mt-0.5">Hadir</span>
                </div>
            </div>

            <p class="text-xs text-gray-500 text-center mb-2.5">
                <span class="font-semibold text-gray-700"><?= $totalPresent ?></span> dari
                <span class="font-semibold text-gray-700"><?= $totalDays ?></span> hari bulan ini
            </p>

            <?php if ($attendanceRate >= 90): ?>
                <span class="inline-flex items-center gap-1 bg-success-100 text-success-700 text-xs font-semibold px-3 py-1 rounded-full">
                    <span class="material-symbols-outlined" style="font-size:13px;">star</span> Sangat Baik!
                </span>
            <?php elseif ($attendanceRate >= 75): ?>
                <span class="inline-flex items-center gap-1 bg-warning-100 text-warning-700 text-xs font-semibold px-3 py-1 rounded-full">
                    <span class="material-symbols-outlined" style="font-size:13px;">thumb_up</span> Pertahankan!
                </span>
            <?php else: ?>
                <span class="inline-flex items-center gap-1 bg-danger-100 text-danger-700 text-xs font-semibold px-3 py-1 rounded-full">
                    <span class="material-symbols-outlined" style="font-size:13px;">warning</span> Perlu Ditingkatkan
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Access Cards -->
    <div class="lg:col-span-2 grid grid-cols-3 gap-3">

        <!-- Riwayat Kehadiran -->
        <a href="<?= base_url('student/attendance') ?>"
           class="card hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group cursor-pointer">
            <div class="card-body flex flex-col items-center justify-center py-5 px-3 text-center">
                <div class="bg-primary-100 rounded-xl p-3 mb-2 group-hover:bg-primary-200 transition-colors">
                    <span class="material-symbols-outlined text-primary-600" style="font-size:24px;">calendar_month</span>
                </div>
                <h4 class="font-semibold text-gray-800 text-xs leading-tight mb-0.5">Riwayat Kehadiran</h4>
                <p class="text-xs text-gray-400">Lihat rekap</p>
            </div>
        </a>

        <!-- 7 Kebiasaan -->
        <a href="<?= base_url('student/habits') ?>"
           class="card hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group cursor-pointer">
            <div class="card-body flex flex-col items-center justify-center py-5 px-3 text-center">
                <div class="bg-success-100 rounded-xl p-3 mb-2 group-hover:bg-success-200 transition-colors">
                    <span class="material-symbols-outlined text-success-600" style="font-size:24px;">emoji_people</span>
                </div>
                <h4 class="font-semibold text-gray-800 text-xs leading-tight mb-0.5">7 Kebiasaan</h4>
                <p class="text-xs text-gray-400">Kebiasaan harian</p>
            </div>
        </a>

        <!-- Notifikasi -->
        <a href="<?= base_url('student/notifications') ?>"
           class="card hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group cursor-pointer">
            <div class="card-body flex flex-col items-center justify-center py-5 px-3 text-center">
                <div class="relative bg-warning-100 rounded-xl p-3 mb-2 group-hover:bg-warning-200 transition-colors inline-block">
                    <span class="material-symbols-outlined text-warning-600" style="font-size:24px;">notifications</span>
                    <?php if (isset($unreadNotifications) && $unreadNotifications > 0): ?>
                        <span class="absolute -top-1 -right-1 bg-danger-500 text-white font-bold rounded-full w-4 h-4 flex items-center justify-center leading-none" style="font-size:10px;">
                            <?= $unreadNotifications > 9 ? '9+' : $unreadNotifications ?>
                        </span>
                    <?php endif; ?>
                </div>
                <h4 class="font-semibold text-gray-800 text-xs leading-tight mb-0.5">Notifikasi</h4>
                <p class="text-xs text-gray-400">
                    <?php if (isset($unreadNotifications) && $unreadNotifications > 0): ?>
                        <?= $unreadNotifications ?> pesan baru
                    <?php else: ?>
                        Tidak ada baru
                    <?php endif; ?>
                </p>
            </div>
        </a>

    </div>

</div>

<?= $this->endSection() ?>