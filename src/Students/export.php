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
$grade  = (int)($_GET['grade'] ?? 0);
$status = $_GET['status'] ?? '';

try {
    $conn = getConnection();

    $where  = '1=1';
    $params = [];
    if ($search !== '') {
        $where .= ' AND (LOWER(s.fullname) LIKE LOWER(:search) OR LOWER(s.ic_number) LIKE LOWER(:search2))';
        $params[':search']  = '%' . $search . '%';
        $params[':search2'] = '%' . $search . '%';
    }
    if ($grade > 0) {
        $where .= ' AND s.grade_id = :grade';
        $params[':grade'] = $grade;
    }
    if (in_array($status, ['ACTIVE', 'INACTIVE'])) {
        $where .= ' AND s.status = :status';
        $params[':status'] = $status;
    }

    $sql  = "SELECT s.fullname, s.ic_number, s.phone, s.status,
                    g.name AS grade_name,
                    p.fullname AS parent_name, p.phone AS parent_phone
             FROM   STUDENT s
             JOIN   GRADE   g ON g.grade_id  = s.grade_id
             JOIN   PARENT  p ON p.parent_id = s.parent_id
             WHERE  $where
             ORDER  BY s.fullname";
    $stmt = oci_parse($conn, $sql);
    foreach ($params as $k => &$v) oci_bind_by_name($stmt, $k, $v);
    unset($v);
    oci_execute($stmt);

    $students = [];
    while ($row = oci_fetch_assoc($stmt)) $students[] = $row;
    oci_free_statement($stmt);
    oci_close($conn);
} catch (\RuntimeException $e) {
    $students = [];
}

exportCsv('students_' . date('Ymd_His') . '.csv', [
    'Name'          => 'FULLNAME',
    'IC Number'     => 'IC_NUMBER',
    'Phone'         => 'PHONE',
    'Grade'         => 'GRADE_NAME',
    'Parent'        => 'PARENT_NAME',
    'Parent Phone'  => 'PARENT_PHONE',
    'Status'        => 'STATUS',
], $students);
