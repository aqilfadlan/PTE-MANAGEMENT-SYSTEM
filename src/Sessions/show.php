<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/login');
    exit;
}

$role      = $_SESSION['role'];
$userId    = (int)$_SESSION['user_id'];
$sessionId = (int)($_GET['id'] ?? 0);

if ($sessionId === 0) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/sessions');
    exit;
}

// ── Handle status update (OWNER/ADMIN only) ───────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($role, ['OWNER', 'ADMIN'])) {
    $newStatus = $_POST['status'] ?? '';
    $notes     = trim($_POST['notes'] ?? '');
    if (in_array($newStatus, ['SCHEDULED', 'COMPLETED', 'CANCELLED'])) {
        try {
            $conn = getConnection();
            $sql  = 'UPDATE CLASS_SESSION SET status = :status, notes = :notes WHERE session_id = :id';
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':status', $newStatus);
            oci_bind_by_name($stmt, ':notes',  $notes);
            oci_bind_by_name($stmt, ':id',     $sessionId);
            oci_execute($stmt);
            oci_commit($conn);
            oci_free_statement($stmt);
            oci_close($conn);
            $_SESSION['flash_success'] = 'Session updated.';
        } catch (\RuntimeException $e) {
            $_SESSION['flash_error'] = 'Update failed.';
        }
    }
    header('Location: /PTE-MANAGEMENT-SYSTEM/sessions/show?id=' . $sessionId);
    exit;
}

// ── Fetch session data ────────────────────────────────────────────────────────
try {
    $conn = getConnection();

    $sql  = "SELECT cs.session_id, cs.status, cs.notes,
                    TO_CHAR(cs.session_date, 'YYYY-MM-DD') AS session_date,
                    cs.start_time, cs.end_time,
                    c.class_id, c.name     AS class_name, c.max_students,
                    s.name     AS subject_name,
                    g.name     AS grade_name,
                    u.fullname AS tutor_name, u.user_id AS tutor_id
             FROM   CLASS_SESSION cs
             JOIN   CLASS         c  ON c.class_id   = cs.class_id
             JOIN   SUBJECT       s  ON s.subject_id = c.subject_id
             JOIN   GRADE         g  ON g.grade_id   = c.grade_id
             JOIN   USERS         u  ON u.user_id    = cs.user_id
             WHERE  cs.session_id = :id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':id', $sessionId);
    oci_execute($stmt);
    $session = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);

    if (!$session) {
        oci_close($conn);
        $_SESSION['flash_error'] = 'Session not found.';
        header('Location: /PTE-MANAGEMENT-SYSTEM/sessions');
        exit;
    }

    // Tutors can only view their own sessions
    if ($role === 'TUTOR' && (int)$session['TUTOR_ID'] !== $userId) {
        oci_close($conn);
        header('Location: /PTE-MANAGEMENT-SYSTEM/sessions');
        exit;
    }

    // Attendance records
    $attSql  = "SELECT sa.attendance_id, sa.status AS att_status, sa.remarks,
                       st.student_id, st.fullname
                FROM   STUDENT_ATTENDANCE sa
                JOIN   STUDENT st ON st.student_id = sa.student_id
                WHERE  sa.session_id = :id
                ORDER  BY st.fullname";
    $attStmt = oci_parse($conn, $attSql);
    oci_bind_by_name($attStmt, ':id', $sessionId);
    oci_execute($attStmt);
    $attendance = [];
    while ($r = oci_fetch_assoc($attStmt)) $attendance[] = $r;
    oci_free_statement($attStmt);

    // Counts
    $present = 0; $absent = 0; $late = 0;
    foreach ($attendance as $a) {
        if ($a['ATT_STATUS'] === 'PRESENT') $present++;
        elseif ($a['ATT_STATUS'] === 'ABSENT')  $absent++;
        elseif ($a['ATT_STATUS'] === 'LATE')    $late++;
    }

    // Total enrolled in this class (for comparison)
    $cntSql  = 'SELECT COUNT(*) AS total FROM CLASS_STUDENT WHERE class_id = :cid';
    $cntStmt = oci_parse($conn, $cntSql);
    $classId = (int)$session['CLASS_ID'];
    oci_bind_by_name($cntStmt, ':cid', $classId);
    oci_execute($cntStmt);
    $totalEnrolled = (int)oci_fetch_assoc($cntStmt)['TOTAL'];
    oci_free_statement($cntStmt);

    oci_close($conn);
} catch (\RuntimeException $e) {
    $_SESSION['flash_error'] = 'Database error.';
    header('Location: /PTE-MANAGEMENT-SYSTEM/sessions');
    exit;
}

$statusColors = [
    'SCHEDULED' => 'bg-yellow-100 text-yellow-700',
    'COMPLETED' => 'bg-green-100 text-green-700',
    'CANCELLED' => 'bg-red-100 text-red-700',
];
$attColors = [
    'PRESENT' => 'bg-green-100 text-green-700',
    'ABSENT'  => 'bg-red-100 text-red-700',
    'LATE'    => 'bg-yellow-100 text-yellow-700',
];

$pageTitle = 'Session — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="pt-16 md:pt-10 md:ml-64 px-4 sm:px-8 pb-4 sm:pb-8 min-h-screen">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/PTE-MANAGEMENT-SYSTEM/sessions?class_id=<?= (int)$session['CLASS_ID'] ?>"
               class="text-slate-400 hover:text-slate-600">
                <i class="ti ti-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-xl font-semibold text-slate-800">
                    <?= htmlspecialchars($session['CLASS_NAME'], ENT_QUOTES, 'UTF-8') ?>
                </h1>
                <p class="text-slate-500 text-sm mt-1">
                    <?= date('l, d M Y', strtotime($session['SESSION_DATE'])) ?>
                    &middot; <?= htmlspecialchars($session['START_TIME'], ENT_QUOTES, 'UTF-8') ?> – <?= htmlspecialchars($session['END_TIME'], ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>
        </div>
        <?php if ($session['STATUS'] !== 'CANCELLED'): ?>
        <a href="/PTE-MANAGEMENT-SYSTEM/attendance/take?session_id=<?= $sessionId ?>"
           class="bg-indigo-800 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 inline-flex items-center gap-2 text-sm">
            <i class="ti ti-clipboard-check"></i> Take Attendance
        </a>
        <?php endif; ?>
    </div>

    <?php require_once '../../views/partials/flash.php'; ?>

    <!-- Summary cards -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Status</p>
            <span class="px-2 py-1 rounded-full text-xs font-medium <?= $statusColors[$session['STATUS']] ?? 'bg-slate-100 text-slate-600' ?>">
                <?= htmlspecialchars(ucfirst(strtolower($session['STATUS'])), ENT_QUOTES, 'UTF-8') ?>
            </span>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Enrolled</p>
            <p class="text-2xl font-bold text-slate-800"><?= $totalEnrolled ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Present</p>
            <p class="text-2xl font-bold text-green-600"><?= $present ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Absent</p>
            <p class="text-2xl font-bold text-red-500"><?= $absent ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Late</p>
            <p class="text-2xl font-bold text-yellow-500"><?= $late ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Attendance table -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-800">Attendance (<?= count($attendance) ?> marked)</h2>
                <?php if ($session['STATUS'] !== 'CANCELLED'): ?>
                <a href="/PTE-MANAGEMENT-SYSTEM/attendance/take?session_id=<?= $sessionId ?>"
                   class="text-xs text-indigo-600 hover:text-indigo-800 font-medium inline-flex items-center gap-1">
                    <i class="ti ti-pencil"></i> Edit
                </a>
                <?php endif; ?>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Student</th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($attendance)): ?>
                    <tr><td colspan="3" class="text-center py-8 text-slate-400">No attendance recorded yet.</td></tr>
                    <?php else: ?>
                    <?php foreach ($attendance as $a): ?>
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-800">
                            <a href="/PTE-MANAGEMENT-SYSTEM/students/show?id=<?= (int)$a['STUDENT_ID'] ?>"
                               class="hover:text-indigo-700">
                                <?= htmlspecialchars($a['FULLNAME'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium <?= $attColors[$a['ATT_STATUS']] ?? 'bg-slate-100 text-slate-600' ?>">
                                <?= htmlspecialchars(ucfirst(strtolower($a['ATT_STATUS'])), ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-500 text-xs"><?= htmlspecialchars($a['REMARKS'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Session info + status update -->
        <div class="space-y-4">
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                <h2 class="text-sm font-semibold text-slate-800 mb-4">Session Info</h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-slate-400">Subject</dt>
                        <dd class="text-slate-800 font-medium"><?= htmlspecialchars($session['SUBJECT_NAME'], ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Grade</dt>
                        <dd class="text-slate-800"><?= htmlspecialchars($session['GRADE_NAME'], ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Tutor</dt>
                        <dd class="text-slate-800"><?= htmlspecialchars($session['TUTOR_NAME'], ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <?php if (!empty($session['NOTES'])): ?>
                    <div>
                        <dt class="text-xs text-slate-400">Notes</dt>
                        <dd class="text-slate-700"><?= htmlspecialchars($session['NOTES'], ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <?php endif; ?>
                </dl>
            </div>

            <?php if (in_array($role, ['OWNER', 'ADMIN'])): ?>
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                <h2 class="text-sm font-semibold text-slate-800 mb-4">Update Session</h2>
                <form method="POST" class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Status</label>
                        <select name="status"
                                class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="SCHEDULED" <?= $session['STATUS'] === 'SCHEDULED' ? 'selected' : '' ?>>Scheduled</option>
                            <option value="COMPLETED" <?= $session['STATUS'] === 'COMPLETED' ? 'selected' : '' ?>>Completed</option>
                            <option value="CANCELLED" <?= $session['STATUS'] === 'CANCELLED' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-700 mb-1">Notes</label>
                        <textarea name="notes" rows="3"
                                  class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                  placeholder="Optional notes…"><?= htmlspecialchars($session['NOTES'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>
                    <button type="submit"
                            class="w-full bg-indigo-800 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 text-sm inline-flex items-center justify-center gap-2">
                        <i class="ti ti-device-floppy"></i> Save
                    </button>
                </form>
            </div>
            <?php endif; ?>
        </div>

    </div>
</main>

<?php require_once '../../views/layout/footer.php'; ?>
