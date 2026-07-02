<?php
/**
 * @var int   $page
 * @var int   $totalPages
 * @var array $baseParams
 */
if ($totalPages <= 1) {
    return;
}

$window = 2;
$start  = max(1, $page - $window);
$end    = min($totalPages, $page + $window);

function paginationUrl(int $targetPage, array $baseParams): string
{
    $params = $baseParams;
    $params['page'] = $targetPage;
    return '?' . http_build_query($params);
}
?>
<div class="flex gap-1">
    <?php if ($page > 1): ?>
    <a href="<?= paginationUrl($page - 1, $baseParams) ?>"
       class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
        <i class="ti ti-chevron-left"></i>
    </a>
    <?php endif; ?>

    <?php if ($start > 1): ?>
        <a href="<?= paginationUrl(1, $baseParams) ?>"
           class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">1</a>
        <?php if ($start > 2): ?>
            <span class="px-2 py-1 text-slate-400">…</span>
        <?php endif; ?>
    <?php endif; ?>

    <?php for ($i = $start; $i <= $end; $i++): ?>
        <a href="<?= paginationUrl($i, $baseParams) ?>"
           <?= $i === $page ? 'aria-current="page"' : '' ?>
           class="px-3 py-1.5 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 <?= $i === $page ? 'bg-indigo-800 text-white font-medium' : 'bg-white border border-slate-200 hover:bg-slate-50' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>

    <?php if ($end < $totalPages): ?>
        <?php if ($end < $totalPages - 1): ?>
            <span class="px-2 py-1 text-slate-400">…</span>
        <?php endif; ?>
        <a href="<?= paginationUrl($totalPages, $baseParams) ?>"
           class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"><?= $totalPages ?></a>
    <?php endif; ?>

    <?php if ($page < $totalPages): ?>
    <a href="<?= paginationUrl($page + 1, $baseParams) ?>"
       class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500">
        <i class="ti ti-chevron-right"></i>
    </a>
    <?php endif; ?>
</div>
