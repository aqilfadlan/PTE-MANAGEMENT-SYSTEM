<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/csv_export.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/login');
    exit;
}

$role   = $_SESSION['role'];
$userId = (int)$_SESSION['user_id'];

$classId      = (int)($_GET['class_id']   ?? 0);
$studentId    = (int)($_GET['student_id'] ?? 0);
$dateFrom     = trim($_GET['date_from']   ?? '');
$dateTo       = trim($_GET['date_to']     ?? '');
$statusFilter = $_GET['att_status']       ?? '';

try {
    $conn = getConnection();

    $where  = '1=1';
    $params = [];

    if ($role === 'TUTOR') {
        $where .= ' AND cs.user_id = :tutor_id';
        $params[':tutor_id'] = $userId;
    }
    if ($classId > 0) {
        $where .= ' AND c.class_id = :class_id';
        $params[':class_id'] = $classId;
    }
    if ($studentId > 0) {
        $where .= ' AND sa.student_id = :student_id';
        $params[':student_id'] = $studentId;
    }
    if (in_array($statusFilter, ['PRESENT', 'ABSENT', 'LATE'])) {
        $where .= ' AND sa.status = :att_status';
        $params[':att_status'] = $statusFilter;
    }
    if ($dateFrom !== '') {
        $where .= " AND cs.session_date >= TO_DATE(:date_from, 'YYYY-MM-DD')";
        $params[':date_from'] = $dateFrom;
    }
    if ($dateTo !== '') {
        $where .= " AND cs.session_date <= TO_DATE(:date_to, 'YYYY-MM-DD')";
        $params[':date_to'] = $dateTo;
    }

    $sql  = "SELECT TO_CHAR(cs.session_date, 'YYYY-MM-DD') AS session_date,
                    st.fullname AS student_name,
                    c.name      AS class_name,
                    s.name      AS subject_name,
                    g.name      AS grade_name,
                    u.fullname  AS tutor_name,
                    sa.status   AS att_status,
                    sa.remarks
             FROM   STUDENT_ATTENDANCE sa
             JOIN   CLASS_SESSION      cs ON cs.session_id = sa.session_id
             JOIN   CLASS              c  ON c.class_id    = cs.class_id
             JOIN   SUBJECT            s  ON s.subject_id  = c.subject_id
             JOIN   GRADE              g  ON g.grade_id    = c.grade_id
             JOIN   STUDENT            st ON st.student_id = sa.student_id
             JOIN   USERS              u  ON u.user_id     = cs.user_id
             WHERE  $where
             ORDER  BY cs.session_date DESC, c.name, st.fullname";
    $stmt = oci_parse($conn, $sql);
    foreach ($params as $k => &$v) oci_bind_by_name($stmt, $k, $v);
    unset($v);
    oci_execute($stmt);

    $records = [];
    while ($row = oci_fetch_assoc($stmt)) $records[] = $row;
    oci_free_statement($stmt);
    oci_close($conn);
} catch (\RuntimeException $e) {
    $records = [];
}

exportCsv('attendance_' . date('Ymd_His') . '.csv', [
    'Date'     => 'SESSION_DATE',
    'Student'  => 'STUDENT_NAME',
    'Class'    => 'CLASS_NAME',
    'Subject'  => 'SUBJECT_NAME',
    'Grade'    => 'GRADE_NAME',
    'Tutor'    => 'TUTOR_NAME',
    'Status'   => 'ATT_STATUS',
    'Remarks'  => 'REMARKS',
], $records);
