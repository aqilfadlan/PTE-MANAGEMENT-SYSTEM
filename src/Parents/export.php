<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/csv_export.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/login');
    exit;
}
if (!in_array($_SESSION['role'], ['OWNER', 'ADMIN'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/dashboard');
    exit;
}

$search = trim($_GET['search'] ?? '');

try {
    $conn = getConnection();

    $where  = '1=1';
    $params = [];
    if ($search !== '') {
        $where .= ' AND (LOWER(p.fullname) LIKE LOWER(:search) OR LOWER(p.phone) LIKE LOWER(:search2) OR LOWER(p.email) LIKE LOWER(:search3))';
        $params[':search']  = '%' . $search . '%';
        $params[':search2'] = '%' . $search . '%';
        $params[':search3'] = '%' . $search . '%';
    }

    $sql  = "SELECT p.fullname, p.ic_number, p.email, p.phone,
                    COUNT(s.student_id) AS student_count
             FROM   PARENT p
             LEFT   JOIN STUDENT s ON s.parent_id = p.parent_id
             WHERE  $where
             GROUP  BY p.fullname, p.ic_number, p.email, p.phone
             ORDER  BY p.fullname";
    $stmt = oci_parse($conn, $sql);
    foreach ($params as $k => &$v) oci_bind_by_name($stmt, $k, $v);
    unset($v);
    oci_execute($stmt);

    $parents = [];
    while ($row = oci_fetch_assoc($stmt)) $parents[] = $row;
    oci_free_statement($stmt);
    oci_close($conn);
} catch (\RuntimeException $e) {
    $parents = [];
}

exportCsv('parents_' . date('Ymd_His') . '.csv', [
    'Name'      => 'FULLNAME',
    'IC Number' => 'IC_NUMBER',
    'Phone'     => 'PHONE',
    'Email'     => 'EMAIL',
    'Students'  => 'STUDENT_COUNT',
], $parents);
