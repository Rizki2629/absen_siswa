<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_student') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Student Profile Card -->
<div class="card w-full mb-6 md:mb-8">
    <div class="relative">
        <!-- Cover Background -->
        <div class="h-24 md:h-32 bg-gradient-to-r from-primary-500 to-primary-700 rounded-t-xl"></div>

        <!-- Profile Content -->
        <div class="px-4 md:px-6 pb-4 md:pb-6">
            <div class="space-y-4">
                <div class="flex flex-col md:flex-row md:items-end md:space-x-6 gap-3 md:gap-0">
                    <!-- Profile Photo -->
                    <div class="-mt-12 md:-mt-16 mb-2 md:mb-0">
                        <div class="bg-white p-2 rounded-xl inline-block shadow-lg">
                            <div class="bg-gradient-to-br from-primary-100 to-primary-200 rounded-lg p-4 md:p-6">
                                <span class="material-symbols-outlined text-primary-600" style="font-size: 64px;">account_circle</span>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Info -->
                    <div class="flex-1 min-w-0">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-900 mb-1"><?= esc($student['name'] ?? 'Nama Siswa') ?></h2>
                        <div class="flex flex-wrap gap-4 text-sm text-gray-600">
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
                </div>

                <!-- Today's Status -->
                <div class="bg-gray-50 rounded-xl p-3 md:p-4 border border-gray-200 w-full">
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

<?= $this->endSection() ?>