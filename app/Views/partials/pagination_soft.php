<?php
$ariaLabel = $ariaLabel ?? 'Pagination';
$infoId = $infoId ?? 'paginationInfo';
$numbersId = $numbersId ?? 'paginationNumbers';
$prevId = $prevId ?? 'prevPageBtn';
$nextId = $nextId ?? 'nextPageBtn';
$prevHandler = $prevHandler ?? 'goToPage(currentPage - 1)';
$nextHandler = $nextHandler ?? 'goToPage(currentPage + 1)';
$infoText = $infoText ?? 'Memuat data...';
$containerClass = $containerClass ?? 'mt-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3';
?>

<div class="<?= esc($containerClass) ?>">
    <p id="<?= esc($infoId) ?>" class="text-sm text-gray-600"><?= esc($infoText) ?></p>
    <nav class="flex items-center gap-x-2" aria-label="<?= esc($ariaLabel) ?>">
        <button id="<?= esc($prevId) ?>" type="button" onclick="<?= esc($prevHandler, 'attr') ?>"
            class="h-10 px-4 rounded-xl border border-gray-200 bg-white text-gray-700 font-medium hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-1.5">
            <span class="material-symbols-outlined text-base">chevron_left</span>
            <span>Sebelumnya</span>
        </button>

        <div id="<?= esc($numbersId) ?>" class="flex items-center gap-x-1.5"></div>

        <button id="<?= esc($nextId) ?>" type="button" onclick="<?= esc($nextHandler, 'attr') ?>"
            class="h-10 px-4 rounded-xl border border-gray-200 bg-white text-gray-700 font-medium hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-1.5">
            <span>Berikutnya</span>
            <span class="material-symbols-outlined text-base">chevron_right</span>
        </button>
    </nav>
</div>