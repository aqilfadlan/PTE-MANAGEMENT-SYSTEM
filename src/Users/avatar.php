<?php

session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id === 0) {
    http_response_code(404);
    exit;
}

try {
    $conn = getConnection();

    $sql  = 'SELECT photo, photo_mime FROM USERS WHERE user_id = :id';
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':id', $id);
    oci_execute($stmt);
    $row = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);

    if (!$row || $row['PHOTO'] === null) {
        oci_close($conn);
        http_response_code(404);
        exit;
    }

    $lob   = $row['PHOTO'];
    $bytes = $lob->load();
    $lob->free();
    oci_close($conn);

    $mime = $row['PHOTO_MIME'] ?: 'image/jpeg';
    header('Content-Type: ' . $mime);
    header('Content-Length: ' . strlen($bytes));
    header('Cache-Control: private, max-age=3600');
    echo $bytes;
} catch (\RuntimeException $e) {
    http_response_code(500);
}
