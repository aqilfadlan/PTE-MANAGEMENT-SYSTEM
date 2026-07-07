<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/login');
    exit;
}

$role = $_SESSION['role'];

// This landing page is for tutors to pick a session to take attendance for.
// Owners/Admins already have a full attendance history view.
if ($role !== 'TUTOR') {
    header('Location: /PTE-MANAGEMENT-SYSTEM/attendance/report');
    exit;
}

$userId = (int)$_SESSION['user_id'];

try {
    $conn = getConnection();

    // Today's + upcoming scheduled sessions for this tutor
    $upcomingSql = "SELECT cs.session_id, TO_CHAR(cs.session_date, 'YYYY-MM-DD') AS session_date,
                           cs.start_time, cs.end_time, cs.status,
                           c.name AS class_name, c.class_id,
                           s.name AS subject_name, g.name AS grade_name,
                           COUNT(sa.attendance_id) AS att_count
                    FROM   CLASS_SESSION cs
                    JOIN   CLASS         c  ON c.class_id   = cs.class_id
                    JOIN   SUBJECT       s  ON s.subject_id = c.subject_id
                    JOIN   GRADE         g  ON g.grade_id   = c.grade_id
                    LEFT   JOIN STUDENT_ATTENDANCE sa ON sa.session_id = cs.session_id
                    WHERE  cs.user_id = :tutor_id
                    AND    cs.status = 'SCHEDULED'
                    AND    cs.session_date >= TRUNC(SYSDATE)
                    GROUP  BY cs.session_id, cs.session_date, cs.start_time, cs.end_time,
                              cs.status, c.name, c.class_id, s.name, g.name
                    ORDER  BY cs.session_date, cs.start_time";
    $upcomingStmt = oci_parse($conn, $upcomingSql);
    oci_bind_by_name($upcomingStmt, ':tutor_id', $userId);
    oci_execute($upcomingStmt);
    $upcoming = [];
    while ($r = oci_fetch_assoc($upcomingStmt)) $upcoming[] = $r;
    oci_free_statement($upcomingStmt);

    // Recently completed sessions — last 10, most recent first
    $recentSql = "SELECT cs.session_id, TO_CHAR(cs.session_date, 'YYYY-MM-DD') AS session_date,
                         cs.start_time, cs.end_time, cs.status,
                         c.name AS class_name, c.class_id,
                         s.name AS subject_name, g.name AS grade_name,
                         COUNT(sa.attendance_id) AS att_count
                  FROM   CLASS_SESSION cs
                  JOIN   CLASS         c  ON c.class_id   = cs.class_id
                  JOIN   SUBJECT       s  ON s.subject_id = c.subject_id
                  JOIN   GRADE         g  ON g.grade_id   = c.grade_id
                  LEFT   JOIN STUDENT_ATTENDANCE sa ON sa.session_id = cs.session_id
                  WHERE  cs.user_id = :tutor_id2
                  AND    (cs.status = 'COMPLETED' OR cs.session_date < TRUNC(SYSDATE))
                  GROUP  BY cs.session_id, cs.session_date, cs.start_time, cs.end_time,
                            cs.status, c.name, c.class_id, s.name, g.name
                  ORDER  BY cs.session_date DESC, cs.start_time DESC
                  OFFSET 0 ROWS FETCH NEXT 10 ROWS ONLY";
    $recentStmt = oci_parse($conn, $recentSql);
    oci_bind_by_name($recentStmt, ':tutor_id2', $userId);
    oci_execute($recentStmt);
    $recent = [];
    while ($r = oci_fetch_assoc($recentStmt)) $recent[] = $r;
    oci_free_statement($recentStmt);

    oci_close($conn);
} catch (\RuntimeException $e) {
    $upcoming = [];
    $recent   = [];
}

$statusColors = [
    'SCHEDULED' => 'bg-yellow-100 text-yellow-700',
    'COMPLETED' => 'bg-green-100 text-green-700',
    'CANCELLED' => 'bg-red-100 text-red-700',
];

$pageTitle = 'Attendance — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="pt-16 md:pt-10 md:ml-64 px-4 sm:px-8 pb-4 sm:pb-8 min-h-screen">
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-slate-800">Attendance</h1>
        <p class="text-slate-500 text-sm mt-1">Pick a session below to take attendance</p>
    </div>

    <?php require_once '../../views/partials/flash.php'; ?>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden mb-6">
        <div class="px-4 py-3 border-b border-slate-200">
            <p class="text-sm font-medium text-slate-500 uppercase tracking-wide">Today &amp; Upcoming</p>
        </div>
        <?php if (empty($upcoming)): ?>
        <div class="text-center py-10 text-slate-400">
            <i class="ti ti-calendar-event text-3xl block mb-2"></i>
            No upcoming sessions scheduled.
        </div>
        <?php else: ?>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Date</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Class</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Subject / Grade</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Time</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Status</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($upcoming as $ses): ?>
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-800 whitespace-nowrap">
                        <?= date('d M Y', strtotime($ses['SESSION_DATE'])) ?>
                        <span class="block text-xs text-slate-400"><?= date('D', strtotime($ses['SESSION_DATE'])) ?></span>
                    </td>
                    <td class="px-4 py-3 text-slate-700">
                        <a href="/PTE-MANAGEMENT-SYSTEM/classes/show?id=<?= (int)$ses['CLASS_ID'] ?>" class="hover:text-indigo-700 font-medium">
                            <?= htmlspecialchars($ses['CLASS_NAME'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </td>
                    <td class="px-4 py-3 text-slate-500 text-xs">
                        <?= htmlspecialchars($ses['SUBJECT_NAME'], ENT_QUOTES, 'UTF-8') ?>
                        <span class="block text-slate-400"><?= htmlspecialchars($ses['GRADE_NAME'], ENT_QUOTES, 'UTF-8') ?></span>
                    </td>
                    <td class="px-4 py-3 text-slate-600 whitespace-nowrap">
                        <?= htmlspecialchars($ses['START_TIME'], ENT_QUOTES, 'UTF-8') ?> – <?= htmlspecialchars($ses['END_TIME'], ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium <?= $statusColors[$ses['STATUS']] ?? 'bg-slate-100 text-slate-600' ?>">
                            <?= htmlspecialchars(ucfirst(strtolower($ses['STATUS'])), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="/PTE-MANAGEMENT-SYSTEM/attendance/take?session_id=<?= (int)$ses['SESSION_ID'] ?>"
                           class="bg-indigo-800 text-white px-3 py-1.5 rounded-lg hover:bg-indigo-700 inline-flex items-center gap-1.5 text-xs font-medium">
                            <i class="ti ti-clipboard-check"></i> <?= (int)$ses['ATT_COUNT'] > 0 ? 'Edit Attendance' : 'Take Attendance' ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
            <p class="text-sm font-medium text-slate-500 uppercase tracking-wide">Recent Sessions</p>
            <a href="/PTE-MANAGEMENT-SYSTEM/sessions" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">View all sessions</a>
        </div>
        <?php if (empty($recent)): ?>
        <div class="text-center py-10 text-slate-400">
            <i class="ti ti-clipboard-check text-3xl block mb-2"></i>
            No past sessions yet.
        </div>
        <?php else: ?>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Date</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Class</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Subject / Grade</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Attendance</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Status</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent as $ses): ?>
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-800 whitespace-nowrap">
                        <?= date('d M Y', strtotime($ses['SESSION_DATE'])) ?>
                        <span class="block text-xs text-slate-400"><?= date('D', strtotime($ses['SESSION_DATE'])) ?></span>
                    </td>
                    <td class="px-4 py-3 text-slate-700">
                        <a href="/PTE-MANAGEMENT-SYSTEM/classes/show?id=<?= (int)$ses['CLASS_ID'] ?>" class="hover:text-indigo-700 font-medium">
                            <?= htmlspecialchars($ses['CLASS_NAME'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </td>
                    <td class="px-4 py-3 text-slate-500 text-xs">
                        <?= htmlspecialchars($ses['SUBJECT_NAME'], ENT_QUOTES, 'UTF-8') ?>
                        <span class="block text-slate-400"><?= htmlspecialchars($ses['GRADE_NAME'], ENT_QUOTES, 'UTF-8') ?></span>
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
                        <?php if ($ses['STATUS'] !== 'CANCELLED'): ?>
                        <a href="/PTE-MANAGEMENT-SYSTEM/attendance/take?session_id=<?= (int)$ses['SESSION_ID'] ?>"
                           class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                            <i class="ti ti-clipboard-check"></i> <?= (int)$ses['ATT_COUNT'] > 0 ? 'Edit' : 'Take' ?>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</main>

<?php require_once '../../views/layout/footer.php'; ?>
