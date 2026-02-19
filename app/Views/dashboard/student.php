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

$dayNames   = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
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
<div class="relative overflow-hidden rounded-2xl mb-6 shadow-lg"
    style="background: linear-gradient(135deg, #4338ca 0%, #6366f1 55%, #818cf8 100%);">
    <!-- Subtle pattern background only -->
    <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(circle at 20% 50%, white 1px, transparent 1px), radial-gradient(circle at 80% 20%, white 1px, transparent 1px); background-size: 50px 50px;"></div>

    <div class="relative px-5 py-5 md:px-6 md:py-6 z-10">
        <!-- Date Header -->
        <p class="text-xs font-medium mb-3 flex items-center gap-1.5 text-white/90">
            <span class="material-symbols-outlined" style="font-size:14px;">calendar_today</span>
            <?= $todayLabel ?>
        </p>

        <!-- Main Content -->
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
            <!-- Greeting Section -->
            <div class="text-white min-w-0 flex-1">
                <h1 class="text-2xl md:text-3xl font-bold mb-3 tracking-tight">
                    Halo, <?= esc($firstName) ?>! 👋
                </h1>
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg"
                        style="background:rgba(255,255,255,0.2);color:#fff;">
                        <span class="material-symbols-outlined" style="font-size:14px;">badge</span>
                        <?= esc($student['nis'] ?? '-') ?>
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg"
                        style="background:rgba(255,255,255,0.2);color:#fff;">
                        <span class="material-symbols-outlined" style="font-size:14px;">class</span>
                        <?= esc($student['class'] ?? '-') ?>
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg"
                        style="background:rgba(255,255,255,0.2);color:#fff;">
                        <span class="material-symbols-outlined" style="font-size:14px;">school</span>
                        <?= esc($student['major'] ?? '-') ?>
                    </span>
                </div>
            </div>

            <!-- Status Today Box -->
            <div class="rounded-xl px-5 py-4 flex-shrink-0 lg:min-w-[240px]"
                style="background:rgba(255,255,255,0.15);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.3);">
                <p class="text-xs font-bold uppercase tracking-wider mb-3 text-white/90">Status Hari Ini</p>

                <?php if ($todayStatus): ?>
                    <div class="flex items-center gap-2.5 mb-3">
                        <div class="rounded-full p-2 flex-shrink-0
                            <?= $statusColor === 'success' ? 'bg-green-400' : ($statusColor === 'warning' ? 'bg-yellow-400' : ($statusColor === 'danger' ? 'bg-red-400' : 'bg-blue-300')) ?>">
                            <span class="material-symbols-outlined text-white" style="font-size:18px;"><?= $statusIcon ?></span>
                        </div>
                        <span class="text-white font-bold text-base"><?= $statusLabel ?></span>
                    </div>
                    <?php if ($isHadir): ?>
                        <div class="space-y-1.5 text-xs text-white/90">
                            <p class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined" style="font-size:14px;">login</span>
                                Masuk: <span class="font-bold text-white ml-auto"><?= esc($todayAttendance['check_in'] ?? '-') ?></span>
                            </p>
                            <p class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined" style="font-size:14px;">logout</span>
                                Pulang: <span class="font-bold text-white ml-auto"><?= esc($todayAttendance['check_out'] ?? 'Belum') ?></span>
                            </p>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="flex items-center gap-2.5">
                        <div class="bg-yellow-400 rounded-full p-2 flex-shrink-0">
                            <span class="material-symbols-outlined text-white" style="font-size:18px;">schedule</span>
                        </div>
                        <div>
                            <p class="text-white font-bold text-sm">Belum Tercatat</p>
                            <p class="text-xs mt-0.5 text-white/80">Data belum tersedia</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- STATS GRID -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <!-- Hadir -->
    <div class="card border-l-4 border-success-500 hover:shadow-lg transition-shadow duration-200">
        <div class="card-body py-4 px-4">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs text-gray-500 font-semibold mb-1 uppercase tracking-wide">Hadir</p>
                    <h3 class="text-3xl font-bold text-gray-900 leading-none"><?= $totalPresent ?></h3>
                    <p class="text-xs text-gray-400 mt-1.5">hari</p>
                </div>
                <div class="bg-success-100 rounded-xl p-3 flex-shrink-0">
                    <span class="material-symbols-outlined text-success-600" style="font-size:26px;">check_circle</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Terlambat -->
    <div class="card border-l-4 border-warning-500 hover:shadow-lg transition-shadow duration-200">
        <div class="card-body py-4 px-4">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs text-gray-500 font-semibold mb-1 uppercase tracking-wide">Terlambat</p>
                    <h3 class="text-3xl font-bold text-gray-900 leading-none"><?= $totalLate ?></h3>
                    <p class="text-xs text-gray-400 mt-1.5">hari</p>
                </div>
                <div class="bg-warning-100 rounded-xl p-3 flex-shrink-0">
                    <span class="material-symbols-outlined text-warning-600" style="font-size:26px;">timer</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sakit / Izin -->
    <div class="card border-l-4 border-primary-400 hover:shadow-lg transition-shadow duration-200">
        <div class="card-body py-4 px-4">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs text-gray-500 font-semibold mb-1 uppercase tracking-wide">Sakit/Izin</p>
                    <h3 class="text-3xl font-bold text-gray-900 leading-none"><?= $totalSick ?></h3>
                    <p class="text-xs text-gray-400 mt-1.5">hari</p>
                </div>
                <div class="bg-primary-100 rounded-xl p-3 flex-shrink-0">
                    <span class="material-symbols-outlined text-primary-500" style="font-size:26px;">medical_information</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpha -->
    <div class="card border-l-4 border-danger-500 hover:shadow-lg transition-shadow duration-200">
        <div class="card-body py-4 px-4">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-xs text-gray-500 font-semibold mb-1 uppercase tracking-wide">Alpha</p>
                    <h3 class="text-3xl font-bold text-gray-900 leading-none"><?= $totalAbsent ?></h3>
                    <p class="text-xs text-gray-400 mt-1.5">hari</p>
                </div>
                <div class="bg-danger-100 rounded-xl p-3 flex-shrink-0">
                    <span class="material-symbols-outlined text-danger-600" style="font-size:26px;">cancel</span>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ATTENDANCE RATE + QUICK ACCESS -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">

    <!-- Attendance Rate Card -->
    <div class="card shadow-md hover:shadow-lg transition-shadow">
        <div class="card-header py-4 px-5 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary-600" style="font-size:20px;">analytics</span>
                Tingkat Kehadiran
            </h3>
        </div>
        <div class="card-body py-6 px-5 flex flex-col items-center">
            <div class="relative w-28 h-28 mb-4">
                <svg class="w-full h-full" style="transform:rotate(-90deg);" viewBox="0 0 36 36">
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#e5e7eb" stroke-width="3.5" />
                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="#6366f1" stroke-width="3.5"
                        stroke-dasharray="<?= $attendanceRate ?> <?= 100 - $attendanceRate ?>"
                        stroke-linecap="round" />
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-2xl font-extrabold text-gray-900 leading-none"><?= $attendanceRate ?>%</span>
                    <span class="text-xs text-gray-400 mt-1">Hadir</span>
                </div>
            </div>

            <p class="text-sm text-gray-600 text-center mb-4">
                <span class="font-bold text-gray-900"><?= $totalPresent ?></span> dari
                <span class="font-bold text-gray-900"><?= $totalDays ?></span> hari bulan ini
            </p>

            <?php if ($attendanceRate >= 90): ?>
                <span class="inline-flex items-center gap-1.5 bg-success-100 text-success-700 text-xs font-bold px-4 py-2 rounded-lg">
                    <span class="material-symbols-outlined" style="font-size:14px;">star</span> Sangat Baik!
                </span>
            <?php elseif ($attendanceRate >= 75): ?>
                <span class="inline-flex items-center gap-1.5 bg-warning-100 text-warning-700 text-xs font-bold px-4 py-2 rounded-lg">
                    <span class="material-symbols-outlined" style="font-size:14px;">thumb_up</span> Pertahankan!
                </span>
            <?php else: ?>
                <span class="inline-flex items-center gap-1.5 bg-danger-100 text-danger-700 text-xs font-bold px-4 py-2 rounded-lg">
                    <span class="material-symbols-outlined" style="font-size:14px;">warning</span> Perlu Ditingkatkan
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Access Cards -->
    <div class="lg:col-span-2 grid grid-cols-3 gap-4">

        <!-- Riwayat Kehadiran -->
        <a href="<?= base_url('student/attendance') ?>"
            class="card shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-200 group cursor-pointer">
            <div class="card-body flex flex-col items-center justify-center py-6 px-4 text-center">
                <div class="bg-primary-100 rounded-xl p-4 mb-3 group-hover:bg-primary-200 transition-colors">
                    <span class="material-symbols-outlined text-primary-600" style="font-size:28px;">calendar_month</span>
                </div>
                <h4 class="font-bold text-gray-800 text-sm leading-tight mb-1">Riwayat Kehadiran</h4>
                <p class="text-xs text-gray-500">Lihat rekap</p>
            </div>
        </a>

        <!-- 7 Kebiasaan -->
        <a href="<?= base_url('student/habits') ?>"
            class="card shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-200 group cursor-pointer">
            <div class="card-body flex flex-col items-center justify-center py-6 px-4 text-center">
                <div class="bg-success-100 rounded-xl p-4 mb-3 group-hover:bg-success-200 transition-colors">
                    <span class="material-symbols-outlined text-success-600" style="font-size:28px;">emoji_people</span>
                </div>
                <h4 class="font-bold text-gray-800 text-sm leading-tight mb-1">7 Kebiasaan</h4>
                <p class="text-xs text-gray-500">Kebiasaan harian</p>
            </div>
        </a>

        <!-- Notifikasi -->
        <a href="<?= base_url('student/notifications') ?>"
            class="card shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-200 group cursor-pointer">
            <div class="card-body flex flex-col items-center justify-center py-6 px-4 text-center">
                <div class="relative bg-warning-100 rounded-xl p-4 mb-3 group-hover:bg-warning-200 transition-colors inline-block">
                    <span class="material-symbols-outlined text-warning-600" style="font-size:28px;">notifications</span>
                    <?php if (isset($unreadNotifications) && $unreadNotifications > 0): ?>
                        <span class="absolute -top-1 -right-1 bg-danger-500 text-white font-bold rounded-full w-5 h-5 flex items-center justify-center leading-none text-xs">
                            <?= $unreadNotifications > 9 ? '9+' : $unreadNotifications ?>
                        </span>
                    <?php endif; ?>
                </div>
                <h4 class="font-bold text-gray-800 text-sm leading-tight mb-1">Notifikasi</h4>
                <p class="text-xs text-gray-500">
                    <?php if (isset($unreadNotifications) && $unreadNotifications > 0): ?>
                        <?= $unreadNotifications ?> baru
                    <?php else: ?>
                        Tidak ada baru
                    <?php endif; ?>
                </p>
            </div>
        </a>

    </div>

</div>

<?= $this->endSection() ?>