<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/src/Auth/login.php');
    exit;
}
if (!in_array($_SESSION['role'], ['OWNER', 'ADMIN'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/src/Dashboard/index.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /PTE-MANAGEMENT-SYSTEM/src/Students/index.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
if ($id === 0) {
    $_SESSION['flash_error'] = 'Invalid request.';
    header('Location: /PTE-MANAGEMENT-SYSTEM/src/Students/index.php');
    exit;
}

try {
    $conn = getConnection();
    $stmt = oci_parse($conn, 'DELETE FROM STUDENT WHERE student_id = :id');
    oci_bind_by_name($stmt, ':id', $id);
    oci_execute($stmt);
    oci_commit($conn);
    oci_free_statement($stmt);
    oci_close($conn);
    $_SESSION['flash_success'] = 'Student deleted successfully.';
} catch (\RuntimeException $e) {
    $_SESSION['flash_error'] = 'Could not delete student. They may have related records.';
}

header('Location: /PTE-MANAGEMENT-SYSTEM/src/Students/index.php');
exit;
