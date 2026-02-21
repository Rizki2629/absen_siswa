<?php

/**
 * Flash Messages Partial
 * Usage: <?= $this->include('partials/flash_messages') ?>
 * Displays success and error flash messages with consistent styling
 */
?>
<?php if (session()->getFlashdata('success')): ?>
    <div class="bg-success-50 border-l-4 border-success-500 p-4 mb-6 rounded-lg flex items-start">
        <span class="material-symbols text-success-500 mr-3">check_circle</span>
        <div>
            <p class="text-success-800 font-medium">Berhasil!</p>
            <p class="text-success-700 text-sm"><?= session()->getFlashdata('success') ?></p>
        </div>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="bg-danger-50 border-l-4 border-danger-500 p-4 mb-6 rounded-lg flex items-start">
        <span class="material-symbols text-danger-500 mr-3">error</span>
        <div>
            <p class="text-danger-800 font-medium">Gagal!</p>
            <p class="text-danger-700 text-sm"><?= session()->getFlashdata('error') ?></p>
        </div>
    </div>
<?php endif; ?>