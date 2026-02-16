<?php

/**
 * Admin Sidebar Partial
 * Usage: <?= $this->include('partials/sidebar_admin') ?>
 * Required variable: $activePage (string) - current active page identifier
 */
$menuGroups = [
    [
        'type' => 'single',
        'url' => 'admin/dashboard',
        'icon' => 'dashboard',
        'label' => 'Dashboard'
    ],
    [
        'type' => 'group',
        'icon' => 'fingerprint',
        'label' => 'Absensi',
        'items' => [
            ['url' => 'admin/devices',         'icon' => 'devices',          'label' => 'Mesin Fingerprint'],
            ['url' => 'admin/device-mapping',  'icon' => 'link',             'label' => 'Mapping ID Mesin'],
            ['url' => 'admin/attendance-logs', 'icon' => 'description',      'label' => 'Log Absensi'],
            ['url' => 'admin/attendance',      'icon' => 'how_to_reg',       'label' => 'Daftar Hadir'],
            ['url' => 'admin/rekap',           'icon' => 'table_chart',      'label' => 'Rekap Daftar Hadir'],
            ['url' => 'admin/shifts',          'icon' => 'schedule',         'label' => 'Pengaturan Shift'],
        ]
    ],
    [
        'type' => 'group',
        'icon' => 'folder_shared',
        'label' => 'Data Master',
        'items' => [
            ['url' => 'admin/students',       'icon' => 'groups',            'label' => 'Data Siswa'],
            ['url' => 'admin/teachers',       'icon' => 'person',            'label' => 'Data Guru'],
            ['url' => 'admin/classes',        'icon' => 'class',             'label' => 'Data Kelas'],
        ]
    ],
    [
        'type' => 'group',
        'icon' => 'emoji_people',
        'label' => '7 Kebiasaan',
        'items' => [
            ['url' => 'admin/habits-daily',    'icon' => 'today',             'label' => 'Rekap Harian'],
            ['url' => 'admin/habits-monthly',  'icon' => 'date_range',        'label' => 'Rekap Bulanan'],
        ]
    ],
    [
        'type' => 'group',
        'icon' => 'settings',
        'label' => 'Sistem',
        'items' => [
            ['url' => 'admin/users',          'icon' => 'manage_accounts',   'label' => 'Manajemen User'],
            ['url' => 'admin/reports',        'icon' => 'assessment',        'label' => 'Laporan'],
            ['url' => 'admin/calendar',       'icon' => 'calendar_month',    'label' => 'Kalender'],
        ]
    ],
];

// Check which group should be expanded based on active page
$expandedGroup = null;
foreach ($menuGroups as $index => $group) {
    if ($group['type'] === 'group') {
        foreach ($group['items'] as $item) {
            if (($activePage ?? '') === $item['url']) {
                $expandedGroup = $index;
                break 2;
            }
        }
    }
}
?>

<style>
    .menu-group-header {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        color: #6B7280;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 500;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .menu-group-header:hover {
        color: #374151;
        background-color: rgba(0, 0, 0, 0.02);
    }

    .menu-group-header.expanded {
        color: #4f46e5;
    }

    .menu-group-items {
        display: none;
        padding-left: 0.5rem;
    }

    .menu-group-items.show {
        display: block;
    }

    .menu-group-item {
        display: flex;
        align-items: center;
        padding: 0.65rem 1rem;
        padding-left: 2.5rem;
        color: #6B7280;
        text-decoration: none;
        transition: all 0.2s;
        font-size: 0.9rem;
    }

    .menu-group-item:hover {
        background-color: rgba(79, 70, 229, 0.05);
        color: #4f46e5;
    }

    .menu-group-item.active {
        background-color: rgba(79, 70, 229, 0.1);
        color: #4f46e5;
        font-weight: 600;
        border-left: 3px solid #4f46e5;
    }

    .chevron-icon {
        margin-left: auto;
        transition: transform 0.2s;
    }

    .chevron-icon.rotated {
        transform: rotate(90deg);
    }
</style>

<?php foreach ($menuGroups as $index => $group): ?>
    <?php if ($group['type'] === 'single'): ?>
        <a href="<?= base_url($group['url']) ?>" class="<?= ($activePage ?? '') === $group['url'] ? 'sidebar-item-active' : 'sidebar-item' ?>">
            <span class="material-symbols-outlined mr-3"><?= $group['icon'] ?></span>
            <span><?= $group['label'] ?></span>
        </a>
    <?php else: ?>
        <div class="menu-group">
            <div class="menu-group-header <?= $expandedGroup === $index ? 'expanded' : '' ?>" onclick="toggleGroup(this)">
                <span class="material-symbols-outlined mr-3"><?= $group['icon'] ?></span>
                <span><?= $group['label'] ?></span>
                <span class="material-symbols-outlined chevron-icon <?= $expandedGroup === $index ? 'rotated' : '' ?>">chevron_right</span>
            </div>
            <div class="menu-group-items <?= $expandedGroup === $index ? 'show' : '' ?>">
                <?php foreach ($group['items'] as $item): ?>
                    <a href="<?= base_url($item['url']) ?>" class="menu-group-item <?= ($activePage ?? '') === $item['url'] ? 'active' : '' ?>">
                        <span class="material-symbols-outlined mr-3" style="font-size: 1.25rem;"><?= $item['icon'] ?></span>
                        <span><?= $item['label'] ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<script>
    function toggleGroup(header) {
        const items = header.nextElementSibling;
        const chevron = header.querySelector('.chevron-icon');
        const isExpanded = items.classList.contains('show');

        // Close all other groups
        document.querySelectorAll('.menu-group-items').forEach(group => {
            if (group !== items) {
                group.classList.remove('show');
            }
        });
        document.querySelectorAll('.menu-group-header').forEach(h => {
            if (h !== header) {
                h.classList.remove('expanded');
            }
        });
        document.querySelectorAll('.chevron-icon').forEach(c => {
            if (c !== chevron) {
                c.classList.remove('rotated');
            }
        });

        // Toggle current group
        items.classList.toggle('show');
        header.classList.toggle('expanded');
        chevron.classList.toggle('rotated');
    }
</script>