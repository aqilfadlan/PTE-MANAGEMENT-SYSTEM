<?php
$flashToasts = [];

if (!empty($_SESSION['flash_success'])) {
    $flashToasts[] = ['type' => 'success', 'message' => $_SESSION['flash_success']];
    unset($_SESSION['flash_success']);
}

if (!empty($_SESSION['flash_error'])) {
    $flashToasts[] = ['type' => 'error', 'message' => $_SESSION['flash_error']];
    unset($_SESSION['flash_error']);
}

if (!empty($flashToasts)):
?>
<script type="application/json" id="flash-toasts"><?= json_encode($flashToasts, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
<?php endif; ?>
