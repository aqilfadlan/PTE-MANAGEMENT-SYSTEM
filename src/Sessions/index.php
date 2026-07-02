<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/login');
    exit;
}

$role      = $_SESSION['role'];
$userId    = (int)$_SESSION['user_id'];

$classId     = (int)($_GET['class_id']  ?? 0);
$statusFilter = $_GET['status']         ?? '';
$dateFrom    = trim($_GET['date_from']  ?? '');
$dateTo      = trim($_GET['date_to']    ?? '');
$page        = max(1, (int)($_GET['page'] ?? 1));
$limit       = 15;
$offset      = ($page - 1) * $limit;

try {
    $conn = getConnection();

    // Classes dropdown — tutors see only own classes
    if ($role === 'TUTOR') {
        $clsSql  = "SELECT class_id, name FROM CLASS WHERE user_id = :uid AND status = 'ACTIVE' ORDER BY name";
        $clsStmt = oci_parse($conn, $clsSql);
        oci_bind_by_name($clsStmt, ':uid', $userId);
    } else {
        $clsSql  = "SELECT class_id, name FROM CLASS WHERE status = 'ACTIVE' ORDER BY name";
        $clsStmt = oci_parse($conn, $clsSql);
    }
    oci_execute($clsStmt);
    $classes = [];
    while ($r = oci_fetch_assoc($clsStmt)) $classes[] = $r;
    oci_free_statement($clsStmt);

    // Build WHERE
    $where  = '1=1';
    $params = [];

    // Tutors can only see sessions for their own classes
    if ($role === 'TUTOR') {
        $where .= ' AND cs.user_id = :uid';
        $params[':uid'] = $userId;
    }
    if ($classId > 0) {
        $where .= ' AND cs.class_id = :class_id';
        $params[':class_id'] = $classId;
    }
    if (in_array($statusFilter, ['SCHEDULED', 'COMPLETED', 'CANCELLED'])) {
        $where .= ' AND cs.status = :status';
        $params[':status'] = $statusFilter;
    }
    if ($dateFrom !== '') {
        $where .= " AND cs.session_date >= TO_DATE(:date_from, 'YYYY-MM-DD')";
        $params[':date_from'] = $dateFrom;
    }
    if ($dateTo !== '') {
        $where .= " AND cs.session_date <= TO_DATE(:date_to, 'YYYY-MM-DD')";
        $params[':date_to'] = $dateTo;
    }

    // Count
    $countSql  = "SELECT COUNT(*) AS total
                  FROM   CLASS_SESSION cs
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

    // List
    $sql  = "SELECT cs.session_id,
                    TO_CHAR(cs.session_date, 'YYYY-MM-DD') AS session_date,
                    cs.start_time, cs.end_time, cs.status, cs.notes,
                    c.name     AS class_name, c.class_id,
                    s.name     AS subject_name,
                    g.name     AS grade_name,
                    u.fullname AS tutor_name,
                    COUNT(sa.attendance_id) AS att_count
             FROM   CLASS_SESSION cs
             JOIN   CLASS         c  ON c.class_id   = cs.class_id
             JOIN   SUBJECT       s  ON s.subject_id = c.subject_id
             JOIN   GRADE         g  ON g.grade_id   = c.grade_id
             JOIN   USERS         u  ON u.user_id    = cs.user_id
             LEFT   JOIN STUDENT_ATTENDANCE sa ON sa.session_id = cs.session_id
             WHERE  $where
             GROUP  BY cs.session_id, cs.session_date, cs.start_time, cs.end_time,
                       cs.status, cs.notes, c.name, c.class_id,
                       s.name, g.name, u.fullname
             ORDER  BY cs.session_date DESC, cs.start_time
             OFFSET :offset ROWS FETCH NEXT :limit ROWS ONLY";
    $stmt = oci_parse($conn, $sql);
    foreach ($params as $k => &$v) oci_bind_by_name($stmt, $k, $v);
    unset($v);
    oci_bind_by_name($stmt, ':offset', $offset);
    oci_bind_by_name($stmt, ':limit',  $limit);
    oci_execute($stmt);
    $sessions = [];
    while ($r = oci_fetch_assoc($stmt)) $sessions[] = $r;
    oci_free_statement($stmt);
    oci_close($conn);
} catch (\RuntimeException $e) {
    $sessions = []; $classes = []; $total = 0; $totalPages = 1;
}

$statusColors = [
    'SCHEDULED' => 'bg-yellow-100 text-yellow-700',
    'COMPLETED' => 'bg-green-100 text-green-700',
    'CANCELLED' => 'bg-red-100 text-red-700',
];

$pageTitle = 'Sessions — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="pt-14 md:pt-0 md:ml-64 p-4 sm:p-8 min-h-screen">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Sessions</h1>
            <p class="text-slate-500 text-sm mt-1">All class sessions</p>
        </div>
        <?php if (in_array($role, ['OWNER', 'ADMIN'])): ?>
        <a href="/PTE-MANAGEMENT-SYSTEM/schedule/generate"
           class="bg-indigo-800 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 inline-flex items-center gap-2 text-sm">
            <i class="ti ti-calendar-plus"></i> Generate Sessions
        </a>
        <?php endif; ?>
    </div>

    <?php require_once '../../views/partials/flash.php'; ?>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4 mb-4">
        <form method="GET" class="flex gap-3 items-end flex-wrap">
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Class</label>
                <select name="class_id" class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
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
                <select name="status" class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All</option>
                    <option value="SCHEDULED" <?= $statusFilter === 'SCHEDULED' ? 'selected' : '' ?>>Scheduled</option>
                    <option value="COMPLETED" <?= $statusFilter === 'COMPLETED' ? 'selected' : '' ?>>Completed</option>
                    <option value="CANCELLED" <?= $statusFilter === 'CANCELLED' ? 'selected' : '' ?>>Cancelled</option>
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
            <?php if ($classId > 0 || $statusFilter !== '' || $dateFrom !== '' || $dateTo !== ''): ?>
            <a href="/PTE-MANAGEMENT-SYSTEM/sessions"
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
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Class</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Subject / Grade</th>
                    <?php if ($role !== 'TUTOR'): ?>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Tutor</th>
                    <?php endif; ?>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Time</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Attendance</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Status</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($sessions)): ?>
                <tr>
                    <td colspan="<?= $role === 'TUTOR' ? 7 : 8 ?>" class="text-center py-10 text-slate-400">
                        <i class="ti ti-calendar-event text-3xl block mb-2"></i>
                        No sessions found.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($sessions as $ses): ?>
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-800 whitespace-nowrap">
                        <?= date('d M Y', strtotime($ses['SESSION_DATE'])) ?>
                        <span class="block text-xs text-slate-400"><?= date('D', strtotime($ses['SESSION_DATE'])) ?></span>
                    </td>
                    <td class="px-4 py-3 text-slate-700">
                        <a href="/PTE-MANAGEMENT-SYSTEM/classes/show?id=<?= (int)$ses['CLASS_ID'] ?>"
                           class="hover:text-indigo-700 font-medium">
                            <?= htmlspecialchars($ses['CLASS_NAME'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </td>
                    <td class="px-4 py-3 text-slate-500 text-xs">
                        <?= htmlspecialchars($ses['SUBJECT_NAME'], ENT_QUOTES, 'UTF-8') ?>
                        <span class="block text-slate-400"><?= htmlspecialchars($ses['GRADE_NAME'], ENT_QUOTES, 'UTF-8') ?></span>
                    </td>
                    <?php if ($role !== 'TUTOR'): ?>
                    <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($ses['TUTOR_NAME'], ENT_QUOTES, 'UTF-8') ?></td>
                    <?php endif; ?>
                    <td class="px-4 py-3 text-slate-600 whitespace-nowrap">
                        <?= htmlspecialchars($ses['START_TIME'], ENT_QUOTES, 'UTF-8') ?> – <?= htmlspecialchars($ses['END_TIME'], ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td class="px-4 py-3 text-slate-600">
                        <?php if ((int)$ses['ATT_COUNT'] > 0): ?>
                            <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2 py-0.5 rounded-full"><?= (int)$ses['ATT_COUNT'] ?> marked</span>
                        <?php else: ?>
                            <span class="text-slate-300 text-xs">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium <?= $statusColors[$ses['STATUS']] ?? 'bg-slate-100 text-slate-600' ?>">
                            <?= htmlspecialchars(ucfirst(strtolower($ses['STATUS'])), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="/PTE-MANAGEMENT-SYSTEM/sessions/show?id=<?= (int)$ses['SESSION_ID'] ?>"
                           class="inline-flex items-center gap-1 text-slate-500 hover:text-slate-700 text-xs font-medium mr-2">
                            <i class="ti ti-eye"></i> View
                        </a>
                        <?php if ($ses['STATUS'] !== 'CANCELLED'): ?>
                        <a href="/PTE-MANAGEMENT-SYSTEM/attendance/take?session_id=<?= (int)$ses['SESSION_ID'] ?>"
                           class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                            <i class="ti ti-clipboard-check"></i> Attendance
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="flex items-center justify-between mt-4 text-sm text-slate-500">
        <span>Showing <?= count($sessions) ?> of <?= $total ?> sessions</span>
        <?php
            $baseParams = ['class_id' => $classId, 'status' => $statusFilter, 'date_from' => $dateFrom, 'date_to' => $dateTo];
            require_once '../../views/partials/pagination.php';
        ?>
    </div>
    <?php endif; ?>
</main>

<?php require_once '../../views/layout/footer.php'; ?>
