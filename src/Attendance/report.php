<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/login');
    exit;
}

$role   = $_SESSION['role'];
$userId = (int)$_SESSION['user_id'];

$classId    = (int)($_GET['class_id']   ?? 0);
$studentId  = (int)($_GET['student_id'] ?? 0);
$dateFrom   = trim($_GET['date_from']   ?? '');
$dateTo     = trim($_GET['date_to']     ?? '');
$statusFilter = $_GET['att_status']     ?? '';
$page       = max(1, (int)($_GET['page'] ?? 1));
$limit      = 20;
$offset     = ($page - 1) * $limit;

try {
    $conn = getConnection();

    // Classes dropdown — tutors see own classes only
    if ($role === 'TUTOR') {
        $clsSql  = "SELECT class_id, name FROM CLASS WHERE user_id = :uid ORDER BY name";
        $clsStmt = oci_parse($conn, $clsSql);
        oci_bind_by_name($clsStmt, ':uid', $userId);
    } else {
        $clsSql  = 'SELECT class_id, name FROM CLASS ORDER BY name';
        $clsStmt = oci_parse($conn, $clsSql);
    }
    oci_execute($clsStmt);
    $classes = [];
    while ($r = oci_fetch_assoc($clsStmt)) $classes[] = $r;
    oci_free_statement($clsStmt);

    // Build WHERE
    $where  = '1=1';
    $params = [];

    if ($role === 'TUTOR') {
        $where .= ' AND cs.user_id = :uid';
        $params[':uid'] = $userId;
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

    // Summary counts (regardless of pagination)
    $sumSql  = "SELECT sa.status, COUNT(*) AS cnt
                FROM   STUDENT_ATTENDANCE sa
                JOIN   CLASS_SESSION      cs ON cs.session_id = sa.session_id
                JOIN   CLASS              c  ON c.class_id    = cs.class_id
                WHERE  $where
                GROUP  BY sa.status";
    $sumStmt = oci_parse($conn, $sumSql);
    foreach ($params as $k => &$v) oci_bind_by_name($sumStmt, $k, $v);
    unset($v);
    oci_execute($sumStmt);
    $summary = ['PRESENT' => 0, 'ABSENT' => 0, 'LATE' => 0];
    while ($r = oci_fetch_assoc($sumStmt)) {
        $summary[$r['STATUS']] = (int)$r['CNT'];
    }
    oci_free_statement($sumStmt);
    $totalAtt = $summary['PRESENT'] + $summary['ABSENT'] + $summary['LATE'];

    // Count for pagination
    $countSql  = "SELECT COUNT(*) AS total
                  FROM   STUDENT_ATTENDANCE sa
                  JOIN   CLASS_SESSION      cs ON cs.session_id = sa.session_id
                  JOIN   CLASS              c  ON c.class_id    = cs.class_id
                  WHERE  $where";
    $countStmt = oci_parse($conn, $countSql);
    foreach ($params as $k => &$v) oci_bind_by_name($countStmt, $k, $v);
    unset($v);
    oci_execute($countStmt);
    $total      = (int)oci_fetch_assoc($countStmt)['TOTAL'];
    $totalPages = max(1, (int)ceil($total / $limit));
    oci_free_statement($countStmt);

    if ($page > $totalPages) {
        $page   = $totalPages;
        $offset = ($page - 1) * $limit;
    }

    // Records
    $sql  = "SELECT sa.attendance_id, sa.status AS att_status, sa.remarks,
                    TO_CHAR(cs.session_date, 'YYYY-MM-DD') AS session_date,
                    cs.start_time, cs.end_time, cs.session_id,
                    c.name     AS class_name, c.class_id,
                    s.name     AS subject_name,
                    g.name     AS grade_name,
                    st.student_id, st.fullname AS student_name,
                    u.fullname AS tutor_name
             FROM   STUDENT_ATTENDANCE sa
             JOIN   CLASS_SESSION      cs ON cs.session_id = sa.session_id
             JOIN   CLASS              c  ON c.class_id    = cs.class_id
             JOIN   SUBJECT            s  ON s.subject_id  = c.subject_id
             JOIN   GRADE              g  ON g.grade_id    = c.grade_id
             JOIN   STUDENT            st ON st.student_id = sa.student_id
             JOIN   USERS              u  ON u.user_id     = cs.user_id
             WHERE  $where
             ORDER  BY cs.session_date DESC, c.name, st.fullname
             OFFSET :offset ROWS FETCH NEXT :limit ROWS ONLY";
    $stmt = oci_parse($conn, $sql);
    foreach ($params as $k => &$v) oci_bind_by_name($stmt, $k, $v);
    unset($v);
    oci_bind_by_name($stmt, ':offset', $offset);
    oci_bind_by_name($stmt, ':limit',  $limit);
    oci_execute($stmt);
    $records = [];
    while ($r = oci_fetch_assoc($stmt)) $records[] = $r;
    oci_free_statement($stmt);

    oci_close($conn);
} catch (\RuntimeException $e) {
    $records  = []; $classes = []; $total = 0; $totalPages = 1;
    $summary  = ['PRESENT' => 0, 'ABSENT' => 0, 'LATE' => 0]; $totalAtt = 0;
}

$attColors = [
    'PRESENT' => 'bg-green-100 text-green-700',
    'ABSENT'  => 'bg-red-100 text-red-700',
    'LATE'    => 'bg-yellow-100 text-yellow-700',
];

$pageTitle = 'Attendance Report — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="pt-14 md:pt-0 md:ml-64 p-4 sm:p-8 min-h-screen">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Attendance Report</h1>
            <p class="text-slate-500 text-sm mt-1">View attendance across sessions</p>
        </div>
    </div>

    <?php require_once '../../views/partials/flash.php'; ?>

    <!-- Summary cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Total Records</p>
            <p class="text-2xl font-bold text-slate-800"><?= $totalAtt ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Present</p>
            <p class="text-2xl font-bold text-green-600"><?= $summary['PRESENT'] ?></p>
            <?php if ($totalAtt > 0): ?>
            <p class="text-xs text-slate-400 mt-1"><?= round($summary['PRESENT'] / $totalAtt * 100) ?>%</p>
            <?php endif; ?>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Absent</p>
            <p class="text-2xl font-bold text-red-500"><?= $summary['ABSENT'] ?></p>
            <?php if ($totalAtt > 0): ?>
            <p class="text-xs text-slate-400 mt-1"><?= round($summary['ABSENT'] / $totalAtt * 100) ?>%</p>
            <?php endif; ?>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Late</p>
            <p class="text-2xl font-bold text-yellow-500"><?= $summary['LATE'] ?></p>
            <?php if ($totalAtt > 0): ?>
            <p class="text-xs text-slate-400 mt-1"><?= round($summary['LATE'] / $totalAtt * 100) ?>%</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4 mb-4">
        <form method="GET" class="flex gap-3 items-end flex-wrap">
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Class</label>
                <select name="class_id"
                        class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Classes</option>
                    <?php foreach ($classes as $c): ?>
                    <option value="<?= (int)$c['CLASS_ID'] ?>" <?= $classId === (int)$c['CLASS_ID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['NAME'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
                <select name="att_status"
                        class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All</option>
                    <option value="PRESENT" <?= $statusFilter === 'PRESENT' ? 'selected' : '' ?>>Present</option>
                    <option value="ABSENT"  <?= $statusFilter === 'ABSENT'  ? 'selected' : '' ?>>Absent</option>
                    <option value="LATE"    <?= $statusFilter === 'LATE'    ? 'selected' : '' ?>>Late</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">From</label>
                <input type="date" name="date_from" value="<?= htmlspecialchars($dateFrom, ENT_QUOTES, 'UTF-8') ?>"
                       class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">To</label>
                <input type="date" name="date_to" value="<?= htmlspecialchars($dateTo, ENT_QUOTES, 'UTF-8') ?>"
                       class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit"
                    class="bg-indigo-800 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 text-sm inline-flex items-center gap-2">
                <i class="ti ti-search"></i> Filter
            </button>
            <?php if ($classId > 0 || $studentId > 0 || $statusFilter !== '' || $dateFrom !== '' || $dateTo !== ''): ?>
            <a href="/PTE-MANAGEMENT-SYSTEM/attendance/report"
               class="bg-slate-100 text-slate-600 px-4 py-2 rounded-lg hover:bg-slate-200 text-sm inline-flex items-center gap-2">
                <i class="ti ti-x"></i> Clear
            </a>
            <?php endif; ?>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Date</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Student</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Class</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Subject / Grade</th>
                    <?php if ($role !== 'TUTOR'): ?>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Tutor</th>
                    <?php endif; ?>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Status</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                <tr>
                    <td colspan="<?= $role === 'TUTOR' ? 6 : 7 ?>" class="text-center py-10 text-slate-400">
                        <i class="ti ti-clipboard-check text-3xl block mb-2"></i>
                        No attendance records found.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($records as $r): ?>
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="px-4 py-3 text-slate-800 whitespace-nowrap">
                        <?= date('d M Y', strtotime($r['SESSION_DATE'])) ?>
                        <span class="block text-xs text-slate-400"><?= date('D', strtotime($r['SESSION_DATE'])) ?></span>
                    </td>
                    <td class="px-4 py-3 font-medium text-slate-800">
                        <a href="/PTE-MANAGEMENT-SYSTEM/students/show?id=<?= (int)$r['STUDENT_ID'] ?>"
                           class="hover:text-indigo-700">
                            <?= htmlspecialchars($r['STUDENT_NAME'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </td>
                    <td class="px-4 py-3 text-slate-600">
                        <a href="/PTE-MANAGEMENT-SYSTEM/sessions/show?id=<?= (int)$r['SESSION_ID'] ?>"
                           class="hover:text-indigo-700">
                            <?= htmlspecialchars($r['CLASS_NAME'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </td>
                    <td class="px-4 py-3 text-slate-500 text-xs">
                        <?= htmlspecialchars($r['SUBJECT_NAME'], ENT_QUOTES, 'UTF-8') ?>
                        <span class="block text-slate-400"><?= htmlspecialchars($r['GRADE_NAME'], ENT_QUOTES, 'UTF-8') ?></span>
                    </td>
                    <?php if ($role !== 'TUTOR'): ?>
                    <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($r['TUTOR_NAME'], ENT_QUOTES, 'UTF-8') ?></td>
                    <?php endif; ?>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium <?= $attColors[$r['ATT_STATUS']] ?? 'bg-slate-100 text-slate-600' ?>">
                            <?= htmlspecialchars(ucfirst(strtolower($r['ATT_STATUS'])), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-slate-500 text-xs"><?= htmlspecialchars($r['REMARKS'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="flex items-center justify-between mt-4 text-sm text-slate-500">
        <span>Showing <?= count($records) ?> of <?= $total ?> records</span>
        <?php
            $baseParams = ['class_id' => $classId, 'student_id' => $studentId, 'att_status' => $statusFilter, 'date_from' => $dateFrom, 'date_to' => $dateTo];
            require_once '../../views/partials/pagination.php';
        ?>
    </div>
    <?php endif; ?>
</main>

<?php require_once '../../views/layout/footer.php'; ?>
