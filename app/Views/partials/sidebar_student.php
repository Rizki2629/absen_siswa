<?php
/**
 * Student Sidebar Partial
 * Usage: <?= $this->include('partials/sidebar_student') ?>
 * Required variable: $activePage (string) - current active page identifier
 */
$menuItems = [
    ['url' => 'student/dashboard',     'icon' => 'dashboard',      'label' => 'Dashboard'],
    ['url' => 'student/attendance',    'icon' => 'calendar_month', 'label' => 'Riwayat Kehadiran'],
    ['url' => 'student/notifications', 'icon' => 'notifications',  'label' => 'Notifikasi'],
    ['url' => 'student/profile',       'icon' => 'person',         'label' => 'Profil Saya'],
];
?>
<?php foreach ($menuItems as $item): ?>
<a href="<?= base_url($item['url']) ?>" class="<?= ($activePage ?? '') === $item['url'] ? 'sidebar-item-active' : 'sidebar-item' ?>">
    <span class="material-symbols-outlined mr-3"><?= $item['icon'] ?></span>
    <span><?= $item['label'] ?></span>
</a>
<?php endforeach; ?>
