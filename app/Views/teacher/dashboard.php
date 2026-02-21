<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_teacher') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Dashboard Guru</h2>
    <p class="text-gray-600 mt-1">Selamat datang, <?= esc(session()->get('name')) ?></p>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <a href="<?= base_url('teacher/attendance') ?>" class="bg-white rounded-2xl shadow p-6 hover:shadow-lg transition-all">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mr-4">
                <span class="material-symbols text-2xl text-primary-600">how_to_reg</span>
            </div>
            <div>
                <h4 class="text-lg font-bold text-gray-900">Daftar Hadir</h4>
                <p class="text-sm text-gray-600">Input kehadiran siswa</p>
            </div>
        </div>
    </a>

    <a href="<?= base_url('teacher/rekap') ?>" class="bg-white rounded-2xl shadow p-6 hover:shadow-lg transition-all">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mr-4">
                <span class="material-symbols text-2xl text-green-600">table_chart</span>
            </div>
            <div>
                <h4 class="text-lg font-bold text-gray-900">Rekap Kehadiran</h4>
                <p class="text-sm text-gray-600">Lihat rekap bulanan</p>
            </div>
        </div>
    </a>

    <a href="<?= base_url('teacher/habits-daily') ?>" class="bg-white rounded-2xl shadow p-6 hover:shadow-lg transition-all">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mr-4">
                <span class="material-symbols text-2xl text-amber-600">emoji_people</span>
            </div>
            <div>
                <h4 class="text-lg font-bold text-gray-900">7 Kebiasaan</h4>
                <p class="text-sm text-gray-600">Lihat rekap kebiasaan</p>
            </div>
        </div>
    </a>
</div>

<?= $this->endSection() ?>