<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_student') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="mb-4 md:mb-6">
    <h2 class="text-xl md:text-2xl font-bold text-gray-900">Riwayat Kehadiran</h2>
    <p class="text-sm md:text-base text-gray-600 mt-1">30 hari terakhir untuk <?= esc($student['name'] ?? 'Siswa') ?></p>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-5 md:mb-6">
    <div class="bg-white rounded-2xl border border-gray-200 p-4">
        <p class="text-xs text-gray-500">Hadir</p>
        <p class="text-xl md:text-2xl font-bold text-green-600 mt-1"><?= (int) ($stats['present'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-4">
        <p class="text-xs text-gray-500">Terlambat</p>
        <p class="text-xl md:text-2xl font-bold text-yellow-600 mt-1"><?= (int) ($stats['late'] ?? 0) ?></p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-4">
        <p class="text-xs text-gray-500">Sakit / Izin</p>
        <p class="text-xl md:text-2xl font-bold text-blue-600 mt-1"><?= (int) (($stats['sick'] ?? 0) + ($stats['izin'] ?? 0)) ?></p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-200 p-4">
        <p class="text-xs text-gray-500">Alpha</p>
        <p class="text-xl md:text-2xl font-bold text-red-600 mt-1"><?= (int) ($stats['absent'] ?? 0) ?></p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow overflow-hidden">
    <div class="px-4 md:px-6 py-3 md:py-4 border-b border-gray-200">
        <h3 class="font-bold text-gray-900 text-sm md:text-base">Daftar Kehadiran</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[680px] md:min-w-[760px]">
            <thead>
                <tr class="bg-gray-50 text-left text-xs uppercase tracking-wider text-gray-600">
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Masuk</th>
                    <th class="px-4 py-3">Pulang</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Catatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($attendanceList)): ?>
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada data kehadiran.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($attendanceList as $row): ?>
                        <?php
                        $status = strtolower((string) ($row['status'] ?? ''));
                        $badgeClass = 'bg-gray-100 text-gray-700';
                        if ($status === 'hadir') {
                            $badgeClass = 'bg-green-100 text-green-700';
                        } elseif ($status === 'terlambat') {
                            $badgeClass = 'bg-yellow-100 text-yellow-700';
                        } elseif ($status === 'sakit' || $status === 'izin') {
                            $badgeClass = 'bg-blue-100 text-blue-700';
                        } elseif ($status === 'alpha') {
                            $badgeClass = 'bg-red-100 text-red-700';
                        }
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-800"><?= esc(date('d M Y', strtotime((string) $row['date']))) ?></td>
                            <td class="px-4 py-3 text-sm text-gray-700"><?= esc($row['check_in_time'] ?: '-') ?></td>
                            <td class="px-4 py-3 text-sm text-gray-700"><?= esc($row['check_out_time'] ?: '-') ?></td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold <?= $badgeClass ?>">
                                    <?= esc(ucfirst($status ?: '-')) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600"><?= esc($row['notes'] ?: '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>