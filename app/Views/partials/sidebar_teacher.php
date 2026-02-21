<?php

/**
 * Teacher Sidebar Partial
 * Usage: <?= $this->include('partials/sidebar_teacher') ?>
 * Required variable: $activePage (string) - current active page identifier
 */
$menuItems = [
    ['url' => 'teacher/dashboard',       'icon' => 'dashboard',        'label' => 'Dashboard'],
    ['url' => 'teacher/students',        'icon' => 'groups',           'label' => 'Daftar Siswa'],
    ['url' => 'teacher/attendance',      'icon' => 'how_to_reg',       'label' => 'Daftar Hadir'],
    ['url' => 'teacher/rekap',           'icon' => 'table_chart',      'label' => 'Rekap Daftar Hadir'],
    ['url' => 'teacher/habits-daily',    'icon' => 'today',            'label' => 'Rekap Harian 7 Kebiasaan'],
    ['url' => 'teacher/habits-monthly',  'icon' => 'date_range',       'label' => 'Rekap Bulanan 7 Kebiasaan'],
];
?>
<?php foreach ($menuItems as $item): ?>
    <a href="<?= base_url($item['url']) ?>" class="<?= ($activePage ?? '') === $item['url'] ? 'sidebar-item-active' : 'sidebar-item' ?>">
        <span class="material-symbols mr-3"><?= $item['icon'] ?></span>
        <span><?= $item['label'] ?></span>
    </a>
<?php endforeach; ?>