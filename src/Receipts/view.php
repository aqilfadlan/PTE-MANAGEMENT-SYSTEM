<?php

require_once '../../config/database.php';
require_once '../../vendor/autoload.php';
require_once 'functions.php';

$token = trim($_GET['token'] ?? '');

if ($token === '') {
    http_response_code(404);
    echo '404 Not Found';
    exit;
}

try {
    $conn = getConnection();

    $tokStmt = oci_parse($conn, 'SELECT payment_id FROM PAYMENT WHERE receipt_token = :token');
    oci_bind_by_name($tokStmt, ':token', $token);
    oci_execute($tokStmt);
    $row = oci_fetch_assoc($tokStmt);
    oci_free_statement($tokStmt);

    if (!$row) {
        oci_close($conn);
        http_response_code(404);
        echo '404 Not Found — invalid or expired receipt link.';
        exit;
    }

    $data = fetchReceiptData($conn, (int)$row['PAYMENT_ID']);
    oci_close($conn);
} catch (\RuntimeException $e) {
    http_response_code(500);
    echo 'Database error.';
    exit;
}

if (!$data) {
    http_response_code(404);
    echo '404 Not Found';
    exit;
}

$download = isset($_GET['download']);

if ($download) {
    $pdfPath = generateReceiptPdf($data['payment'], $data['invoice'], $data['items']);
    $filename = 'receipt_' . (int)$data['payment']['PAYMENT_ID'] . '.pdf';

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($pdfPath));
    readfile($pdfPath);
    exit;
}

$receiptHtml = renderReceiptHtml($data['payment'], $data['invoice'], $data['items']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt — PTE Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont/dist/tabler-icons.min.css">
</head>
<body class="bg-slate-100 min-h-screen py-10 px-4">

<div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2 text-indigo-800 font-bold">
            <i class="ti ti-books text-xl"></i> PTE Management System
        </div>
        <a href="?token=<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>&download=1"
           class="bg-indigo-800 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 inline-flex items-center gap-2 text-sm">
            <i class="ti ti-download"></i> Download PDF
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
        <?= $receiptHtml ?>
    </div>
</div>

</body>
</html>
