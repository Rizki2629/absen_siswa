<?= $this->extend('layouts/main') ?>

<?= $this->section('sidebar') ?>
<?= $this->include('partials/sidebar_teacher') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Daftar Siswa</h2>
        <p class="text-gray-600 mt-1">
            <?php if ($singleClass): ?>
                Kelas <?= esc($singleClass['name']) ?> &mdash; <?= count($students) ?> siswa
            <?php else: ?>
                Data siswa kelas Anda
            <?php endif; ?>
        </p>
    </div>
</div>

<?php if (empty($classes)): ?>
    <!-- No Class Assigned -->
    <div class="bg-white rounded-2xl shadow p-12 text-center">
        <span class="material-symbols-outlined text-6xl text-gray-300 mb-4 block">school</span>
        <p class="text-gray-500 text-lg">Anda belum ditetapkan sebagai wali kelas</p>
        <p class="text-gray-400 text-sm mt-2">Hubungi administrator untuk mengatur kelas Anda</p>
    </div>

<?php elseif (empty($students)): ?>
    <!-- No Students -->
    <div class="bg-white rounded-2xl shadow p-12 text-center">
        <span class="material-symbols-outlined text-6xl text-gray-300 mb-4 block">person_search</span>
        <p class="text-gray-500 text-lg">Belum ada siswa di kelas <?= esc($singleClass['name'] ?? '') ?></p>
        <p class="text-gray-400 text-sm mt-2">Data siswa dapat ditambahkan oleh administrator</p>
    </div>

<?php else: ?>

    <!-- Search Bar -->
    <div class="mb-4">
        <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
            <input type="text" id="searchInput" placeholder="Cari nama atau NIS..."
                oninput="filterStudents()"
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white">
        </div>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="px-6 py-4 bg-primary-600 text-white">
            <h3 class="text-lg font-bold flex items-center">
                <span class="material-symbols-outlined mr-2">groups</span>
                Siswa Kelas <?= esc($singleClass['name'] ?? '') ?>
            </h3>
        </div>

        <!-- Desktop Table -->
        <div class="overflow-x-auto hidden md:block">
            <table class="w-full" id="studentsTable">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">NIS</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">NISN</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis Kelamin</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No. HP Orang Tua</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="tableBody">
                    <?php foreach ($students as $i => $student): ?>
                        <tr class="hover:bg-gray-50 transition-colors student-row"
                            data-name="<?= strtolower(esc($student['name'])) ?>"
                            data-nis="<?= esc($student['nis']) ?>">
                            <td class="px-6 py-4 text-sm text-gray-500"><?= $i + 1 ?></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center mr-3 <?= ($student['gender'] === 'L') ? 'bg-blue-100' : 'bg-pink-100' ?>">
                                        <span class="material-symbols-outlined text-sm <?= ($student['gender'] === 'L') ? 'text-blue-600' : 'text-pink-600' ?>">person</span>
                                    </div>
                                    <span class="font-medium text-gray-900"><?= esc($student['name']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?= esc($student['nis'] ?? '-') ?></td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?= esc($student['nisn'] ?? '-') ?></td>
                            <td class="px-6 py-4">
                                <?php if ($student['gender'] === 'L'): ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                        Laki-laki
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-pink-100 text-pink-700">
                                        Perempuan
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700"><?= esc($student['parent_phone'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden divide-y divide-gray-100" id="mobileCards">
            <?php foreach ($students as $i => $student): ?>
                <div class="p-4 student-row"
                    data-name="<?= strtolower(esc($student['name'])) ?>"
                    data-nis="<?= esc($student['nis']) ?>">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center mr-3 <?= ($student['gender'] === 'L') ? 'bg-blue-100' : 'bg-pink-100' ?>">
                            <span class="material-symbols-outlined <?= ($student['gender'] === 'L') ? 'text-blue-600' : 'text-pink-600' ?>">person</span>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900"><?= esc($student['name']) ?></p>
                            <p class="text-sm text-gray-500">NIS: <?= esc($student['nis'] ?? '-') ?></p>
                        </div>
                        <div class="text-right">
                            <?php if ($student['gender'] === 'L'): ?>
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">L</span>
                            <?php else: ?>
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-pink-100 text-pink-700">P</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (!empty($student['parent_phone'])): ?>
                        <p class="text-sm text-gray-500 mt-2 ml-13">
                            <span class="material-symbols-outlined text-xs align-middle mr-1">phone</span>
                            <?= esc($student['parent_phone']) ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Empty Search Result -->
        <div id="noResult" class="hidden p-10 text-center text-gray-400">
            <span class="material-symbols-outlined text-5xl mb-3 block">search_off</span>
            Tidak ada siswa yang cocok
        </div>
    </div>

<?php endif; ?>

<script>
    function filterStudents() {
        const q = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('.student-row');
        let found = 0;

        rows.forEach(row => {
            const name = row.dataset.name || '';
            const nis = row.dataset.nis || '';
            const match = name.includes(q) || nis.includes(q);
            row.classList.toggle('hidden', !match);
            if (match) found++;
        });

        const noResult = document.getElementById('noResult');
        if (noResult) noResult.classList.toggle('hidden', found > 0);
    }
</script>

<?= $this->endSection() ?>