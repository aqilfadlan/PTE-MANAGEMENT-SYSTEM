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

$search  = trim($_GET['search']  ?? '');
$subject = (int)($_GET['subject'] ?? 0);
$grade   = (int)($_GET['grade']   ?? 0);
$status  = $_GET['status'] ?? '';

try {
    $conn = getConnection();

    $where  = '1=1';
    $params = [];
    if ($search !== '') {
        $where .= ' AND LOWER(c.name) LIKE LOWER(:search)';
        $params[':search'] = '%' . $search . '%';
    }
    if ($subject > 0) {
        $where .= ' AND c.subject_id = :subject';
        $params[':subject'] = $subject;
    }
    if ($grade > 0) {
        $where .= ' AND c.grade_id = :grade';
        $params[':grade'] = $grade;
    }
    if (in_array($status, ['ACTIVE', 'INACTIVE'])) {
        $where .= ' AND c.status = :status';
        $params[':status'] = $status;
    }

    $sql  = "SELECT c.name, c.fee, c.max_students, c.status,
                    s.name      AS subject_name,
                    g.name      AS grade_name,
                    u.fullname  AS tutor_name,
                    COUNT(cs.student_id) AS enrolled_count
             FROM   CLASS   c
             JOIN   SUBJECT s  ON s.subject_id = c.subject_id
             JOIN   GRADE   g  ON g.grade_id   = c.grade_id
             JOIN   USERS   u  ON u.user_id     = c.user_id
             LEFT   JOIN CLASS_STUDENT cs ON cs.class_id = c.class_id
             WHERE  $where
             GROUP  BY c.name, c.fee, c.max_students, c.status,
                       s.name, g.name, u.fullname
             ORDER  BY c.name";
    $stmt = oci_parse($conn, $sql);
    foreach ($params as $k => &$v) oci_bind_by_name($stmt, $k, $v);
    unset($v);
    oci_execute($stmt);

    $classes = [];
    while ($row = oci_fetch_assoc($stmt)) $classes[] = $row;
    oci_free_statement($stmt);
    oci_close($conn);
} catch (\RuntimeException $e) {
    $classes = [];
}

exportCsv('classes_' . date('Ymd_His') . '.csv', [
    'Class'    => 'NAME',
    'Subject'  => 'SUBJECT_NAME',
    'Grade'    => 'GRADE_NAME',
    'Tutor'    => 'TUTOR_NAME',
    'Fee'      => 'FEE',
    'Enrolled' => 'ENROLLED_COUNT',
    'Max'      => 'MAX_STUDENTS',
    'Status'   => 'STATUS',
], $classes);
