<?php

/**
 * Guru Piket Sidebar Partial
 * Usage: <?= $this->include('partials/sidebar_guru_piket') ?>
 * Required variable: $activePage (string) - current active page identifier
 */
$menuItems = [
    ['url' => 'guru-piket/dashboard',   'icon' => 'dashboard',  'label' => 'Dashboard'],
    ['url' => 'guru-piket/monitoring',   'icon' => 'visibility', 'label' => 'Monitoring Real-time'],
    ['url' => 'guru-piket/daily-recap',  'icon' => 'today',      'label' => 'Rekap Harian'],
    ['url' => 'guru-piket/exceptions',   'icon' => 'edit_note',  'label' => 'Input Ketidakhadiran'],
    ['url' => 'guru-piket/reports',      'icon' => 'assessment', 'label' => 'Laporan'],
];
?>
<?php foreach ($menuItems as $item): ?>
    <a href="<?= base_url($item['url']) ?>" class="<?= ($activePage ?? '') === $item['url'] ? 'sidebar-item-active' : 'sidebar-item' ?>">
        <span class="material-symbols-outlined mr-3"><?= $item['icon'] ?></span>
        <span><?= $item['label'] ?></span>
    </a>
<?php endforeach; ?>