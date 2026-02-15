<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_admin') ?>
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
        </div>
    </div>
</div>



<!-- Recent Logs -->
<div class="w-full md:w-full lg:w-3/4 xl:w-2/3 mt-8">
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
                            <th class="table-header-cell">Status</th>
                        </tr>
                    </thead>
                    <tbody class="table-body">
                        <?php
                        foreach ($logs as $log):
                        ?>
                            <tr class="hover:bg-gray-50 transition-colors animate-fade-in">
                                <td class="table-cell">
                                    <span class="flex items-center text-gray-600">
                                        <span class="material-symbols-outlined text-sm mr-1">schedule</span>
                                        <?= $log['att_time'] ?>
                                    </span>
                                </td>
                                <td class="table-cell font-medium"><?= $log['student_name'] ?></td>
                                <td class="table-cell text-gray-600"><?= $log['class_name'] ?></td>
                                <td class="table-cell">
                                    <span class="flex items-center text-gray-600">
                                        <span class="material-symbols-outlined text-sm mr-1">router</span>
                                        <?= $log['device_name'] ?>
                                    </span>
                                </td>
                                <td class="table-cell">
                                    <span class="badge-primary"><?= $log['status'] ?? '-' ?></span>
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