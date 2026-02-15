<?php

/**
 * Admin Sidebar Partial
 * Usage: <?= $this->include('partials/sidebar_admin') ?>
 * Required variable: $activePage (string) - current active page identifier
 */
$menuItems = [
    ['url' => 'admin/dashboard',       'icon' => 'dashboard',        'label' => 'Dashboard'],
    ['url' => 'admin/devices',         'icon' => 'devices',          'label' => 'Mesin Fingerprint'],
    ['url' => 'admin/device-mapping',  'icon' => 'link',             'label' => 'Mapping ID Mesin'],
    ['url' => 'admin/attendance-logs', 'icon' => 'description',      'label' => 'Log Absensi'],
    ['url' => 'admin/attendance',      'icon' => 'how_to_reg',       'label' => 'Daftar Hadir'],
    ['url' => 'admin/shifts',         'icon' => 'schedule',          'label' => 'Pengaturan Shift'],
    ['url' => 'admin/students',       'icon' => 'groups',            'label' => 'Data Siswa'],
    ['url' => 'admin/classes',        'icon' => 'class',             'label' => 'Data Kelas'],
    ['url' => 'admin/users',          'icon' => 'manage_accounts',   'label' => 'Manajemen User'],
    ['url' => 'admin/reports',        'icon' => 'assessment',        'label' => 'Laporan'],
    ['url' => 'admin/calendar',       'icon' => 'calendar_month',    'label' => 'Kalender'],
];
?>
<?php foreach ($menuItems as $item): ?>
    <a href="<?= base_url($item['url']) ?>" class="<?= ($activePage ?? '') === $item['url'] ? 'sidebar-item-active' : 'sidebar-item' ?>">
        <span class="material-symbols-outlined mr-3"><?= $item['icon'] ?></span>
        <span><?= $item['label'] ?></span>
    </a>
<?php endforeach; ?>