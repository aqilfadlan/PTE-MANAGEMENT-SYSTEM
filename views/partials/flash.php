<?php
if (!empty($_SESSION['flash_success'])): ?>
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 mb-4 flex items-center gap-2">
        <i class="ti ti-circle-check text-lg"></i>
        <span><?= htmlspecialchars($_SESSION['flash_success'], ENT_QUOTES, 'UTF-8') ?></span>
    </div>
<?php
    unset($_SESSION['flash_success']);
endif;

if (!empty($_SESSION['flash_error'])): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-4 flex items-center gap-2">
        <i class="ti ti-alert-circle text-lg"></i>
        <span><?= htmlspecialchars($_SESSION['flash_error'], ENT_QUOTES, 'UTF-8') ?></span>
    </div>
<?php
    unset($_SESSION['flash_error']);
endif;
