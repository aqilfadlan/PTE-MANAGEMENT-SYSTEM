<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/login');
    exit;
}
if ($_SESSION['role'] !== 'OWNER') {
    header('Location: /PTE-MANAGEMENT-SYSTEM/dashboard');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /PTE-MANAGEMENT-SYSTEM/users');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
if ($id === 0 || $id === (int)$_SESSION['user_id']) {
    $_SESSION['flash_error'] = 'Invalid request.';
    header('Location: /PTE-MANAGEMENT-SYSTEM/users');
    exit;
}

try {
    $conn = getConnection();

    $sql  = 'DELETE FROM USERS WHERE user_id = :id';
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':id', $id);
    oci_execute($stmt);
    oci_commit($conn);
    oci_free_statement($stmt);
    oci_close($conn);

    $_SESSION['flash_success'] = 'User deleted successfully.';
} catch (\RuntimeException $e) {
    $_SESSION['flash_error'] = 'Could not delete user. They may have related records.';
}

header('Location: /PTE-MANAGEMENT-SYSTEM/users');
exit;
