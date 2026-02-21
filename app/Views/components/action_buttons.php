<!-- 
    Komponen Button Aksi Konsisten
    
    Parameter:
    - $editOnclick: string - fungsi JavaScript untuk edit (contoh: "editStudent(123)")
    - $deleteOnclick: string - fungsi JavaScript untuk delete (contoh: "deleteStudent(123)")
    - $editTooltip: string (opsional) - tooltip untuk button edit (default: "Edit data")
    - $deleteTooltip: string (opsional) - tooltip untuk button delete (default: "Hapus data")
    - $layout: string (opsional) - 'horizontal' atau 'vertical' (default: 'horizontal')
    - $buttonSize: string (opsional) - 'sm', 'md', 'lg' (default: 'sm')
-->

<?php
$editTooltip = $editTooltip ?? 'Edit data';
$deleteTooltip = $deleteTooltip ?? 'Hapus data';
$layout = $layout ?? 'horizontal';
$buttonSize = $buttonSize ?? 'sm';

// Ukuran button
$sizeClasses = [
    'sm' => 'px-3 py-2 text-sm',
    'md' => 'px-4 py-2.5 text-base',
    'lg' => 'px-5 py-3 text-lg'
];
$sizeClass = $sizeClasses[$buttonSize] ?? $sizeClasses['sm'];

// Layout
$containerClass = $layout === 'vertical' ? 'flex flex-col gap-1' : 'flex items-center justify-center gap-1';
?>

<div class="<?= $containerClass ?>">
    <button 
        onclick="<?= esc($editOnclick) ?>" 
        title="<?= esc($editTooltip) ?>"
        class="<?= $sizeClass ?> bg-primary-600 text-white hover:bg-primary-700 rounded-lg transition-colors font-medium whitespace-nowrap">
        Edit
    </button>
    <button 
        onclick="<?= esc($deleteOnclick) ?>" 
        title="<?= esc($deleteTooltip) ?>"
        class="<?= $sizeClass ?> bg-red-600 text-white hover:bg-red-700 rounded-lg transition-colors font-medium whitespace-nowrap">
        Hapus
    </button>
</div>
