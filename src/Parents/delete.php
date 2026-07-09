<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/login');
    exit;
}
if (!in_array($_SESSION['role'], ['OWNER', 'ADMIN'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/dashboard');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /PTE-MANAGEMENT-SYSTEM/parents');
    exit;
}

$id = (int)($_POST['id'] ?? 0);
if ($id === 0) {
    $_SESSION['flash_error'] = 'Invalid request.';
    header('Location: /PTE-MANAGEMENT-SYSTEM/parents');
    exit;
}

try {
    $conn = getConnection();
    $stmt = oci_parse($conn, 'DELETE FROM PARENT WHERE parent_id = :id');
    oci_bind_by_name($stmt, ':id', $id);

    if (!oci_execute($stmt, OCI_NO_AUTO_COMMIT)) {
        $e = oci_error($stmt);
        oci_free_statement($stmt);
        oci_close($conn);

        if ((int)$e['code'] === 2292) {
            $_SESSION['flash_error'] = 'Cannot delete this parent — they still have students or invoices linked to their account. Please remove or reassign those records first.';
        } else {
            $_SESSION['flash_error'] = 'Could not delete parent due to a database error. Please try again.';
        }
        header('Location: /PTE-MANAGEMENT-SYSTEM/parents');
        exit;
    }

    oci_commit($conn);
    oci_free_statement($stmt);
    oci_close($conn);
    $_SESSION['flash_success'] = 'Parent deleted successfully.';
} catch (\RuntimeException $e) {
    $_SESSION['flash_error'] = 'Could not delete parent. They may have students linked to them.';
}

header('Location: /PTE-MANAGEMENT-SYSTEM/parents');
exit;
