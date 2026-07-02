<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/login');
    exit;
}

$role      = $_SESSION['role'];
$userId    = (int)$_SESSION['user_id'];
$sessionId = (int)($_GET['session_id'] ?? 0);

if ($sessionId === 0) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/sessions');
    exit;
}

// ── Handle POST: save attendance ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $records = $_POST['attendance'] ?? [];  // ['student_id' => 'PRESENT|ABSENT|LATE']
    $remarks = $_POST['remarks']    ?? [];  // ['student_id' => 'text']

    try {
        $conn = getConnection();

        foreach ($records as $stuId => $status) {
            $stuId  = (int)$stuId;
            $status = in_array($status, ['PRESENT', 'ABSENT', 'LATE']) ? $status : 'ABSENT';
            $remark = trim($remarks[$stuId] ?? '');

            // Upsert: try update first, then insert
            $updSql  = 'UPDATE STUDENT_ATTENDANCE SET status = :status, remarks = :remarks
                        WHERE session_id = :session_id AND student_id = :student_id';
            $updStmt = oci_parse($conn, $updSql);
            oci_bind_by_name($updStmt, ':status',     $status);
            oci_bind_by_name($updStmt, ':remarks',    $remark);
            oci_bind_by_name($updStmt, ':session_id', $sessionId);
            oci_bind_by_name($updStmt, ':student_id', $stuId);
            oci_execute($updStmt);
            $rowsUpdated = oci_num_rows($updStmt);
            oci_free_statement($updStmt);

            if ($rowsUpdated === 0) {
                $insSql  = 'INSERT INTO STUDENT_ATTENDANCE (session_id, student_id, status, remarks)
                            VALUES (:session_id, :student_id, :status, :remarks)';
                $insStmt = oci_parse($conn, $insSql);
                oci_bind_by_name($insStmt, ':session_id', $sessionId);
                oci_bind_by_name($insStmt, ':student_id', $stuId);
                oci_bind_by_name($insStmt, ':status',     $status);
                oci_bind_by_name($insStmt, ':remarks',    $remark);
                oci_execute($insStmt);
                oci_free_statement($insStmt);
            }
        }

        // Mark session as COMPLETED after saving attendance
        $updSesSql  = "UPDATE CLASS_SESSION SET status = 'COMPLETED' WHERE session_id = :id AND status = 'SCHEDULED'";
        $updSesStmt = oci_parse($conn, $updSesSql);
        oci_bind_by_name($updSesStmt, ':id', $sessionId);
        oci_execute($updSesStmt);
        oci_free_statement($updSesStmt);

        oci_commit($conn);
        oci_close($conn);
        $_SESSION['flash_success'] = 'Attendance saved successfully.';
    } catch (\RuntimeException $e) {
        $_SESSION['flash_error'] = 'Failed to save attendance. Please try again.';
    }
    header('Location: /PTE-MANAGEMENT-SYSTEM/sessions/show?id=' . $sessionId);
    exit;
}

// ── Fetch session + enrolled students ─────────────────────────────────────────
try {
    $conn = getConnection();

    $sql  = "SELECT cs.session_id, cs.status,
                    TO_CHAR(cs.session_date, 'YYYY-MM-DD') AS session_date,
                    cs.start_time, cs.end_time,
                    c.class_id, c.name AS class_name,
                    s.name AS subject_name, g.name AS grade_name,
                    u.user_id AS tutor_id, u.fullname AS tutor_name
             FROM   CLASS_SESSION cs
             JOIN   CLASS         c ON c.class_id   = cs.class_id
             JOIN   SUBJECT       s ON s.subject_id = c.subject_id
             JOIN   GRADE         g ON g.grade_id   = c.grade_id
             JOIN   USERS         u ON u.user_id    = cs.user_id
             WHERE  cs.session_id = :id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':id', $sessionId);
    oci_execute($stmt);
    $session = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);

    if (!$session) {
        oci_close($conn);
        header('Location: /PTE-MANAGEMENT-SYSTEM/sessions');
        exit;
    }

    // Tutors can only take attendance for their own sessions
    if ($role === 'TUTOR' && (int)$session['TUTOR_ID'] !== $userId) {
        oci_close($conn);
        header('Location: /PTE-MANAGEMENT-SYSTEM/sessions');
        exit;
    }

    if ($session['STATUS'] === 'CANCELLED') {
        oci_close($conn);
        $_SESSION['flash_error'] = 'Cannot take attendance for a cancelled session.';
        header('Location: /PTE-MANAGEMENT-SYSTEM/sessions/show?id=' . $sessionId);
        exit;
    }

    $classId = (int)$session['CLASS_ID'];

    // Enrolled students
    $stuSql  = 'SELECT s.student_id, s.fullname
                FROM   CLASS_STUDENT cs
                JOIN   STUDENT s ON s.student_id = cs.student_id
                WHERE  cs.class_id = :class_id
                ORDER  BY s.fullname';
    $stuStmt = oci_parse($conn, $stuSql);
    oci_bind_by_name($stuStmt, ':class_id', $classId);
    oci_execute($stuStmt);
    $students = [];
    while ($r = oci_fetch_assoc($stuStmt)) $students[] = $r;
    oci_free_statement($stuStmt);

    // Existing attendance for this session
    $attSql  = 'SELECT student_id, status, remarks
                FROM   STUDENT_ATTENDANCE
                WHERE  session_id = :id';
    $attStmt = oci_parse($conn, $attSql);
    oci_bind_by_name($attStmt, ':id', $sessionId);
    oci_execute($attStmt);
    $existing = [];
    while ($r = oci_fetch_assoc($attStmt)) {
        $existing[(int)$r['STUDENT_ID']] = $r;
    }
    oci_free_statement($attStmt);

    oci_close($conn);
} catch (\RuntimeException $e) {
    $_SESSION['flash_error'] = 'Database error.';
    header('Location: /PTE-MANAGEMENT-SYSTEM/sessions');
    exit;
}

$pageTitle = 'Take Attendance — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="pt-14 md:pt-0 md:ml-64 p-4 sm:p-8 min-h-screen">
    <div class="mb-6 flex items-center gap-3">
        <a href="/PTE-MANAGEMENT-SYSTEM/sessions/show?id=<?= $sessionId ?>"
           class="text-slate-400 hover:text-slate-600">
            <i class="ti ti-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Take Attendance</h1>
            <p class="text-slate-500 text-sm mt-1">
                <?= htmlspecialchars($session['CLASS_NAME'], ENT_QUOTES, 'UTF-8') ?>
                &middot; <?= date('d M Y', strtotime($session['SESSION_DATE'])) ?>
                &middot; <?= htmlspecialchars($session['START_TIME'], ENT_QUOTES, 'UTF-8') ?> – <?= htmlspecialchars($session['END_TIME'], ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>
    </div>

    <?php require_once '../../views/partials/flash.php'; ?>

    <?php if (empty($students)): ?>
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-10 text-center text-slate-400">
        <i class="ti ti-users text-3xl block mb-2"></i>
        No students enrolled in this class.
    </div>
    <?php else: ?>

    <!-- Quick mark all -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4 mb-4 flex items-center gap-4">
        <span class="text-sm font-medium text-slate-600">Mark all as:</span>
        <button type="button" onclick="markAll('PRESENT')"
                class="bg-green-100 text-green-700 px-3 py-1.5 rounded-lg text-sm font-medium hover:bg-green-200">
            <i class="ti ti-check"></i> Present
        </button>
        <button type="button" onclick="markAll('ABSENT')"
                class="bg-red-100 text-red-700 px-3 py-1.5 rounded-lg text-sm font-medium hover:bg-red-200">
            <i class="ti ti-x"></i> Absent
        </button>
        <button type="button" onclick="markAll('LATE')"
                class="bg-yellow-100 text-yellow-700 px-3 py-1.5 rounded-lg text-sm font-medium hover:bg-yellow-200">
            <i class="ti ti-clock"></i> Late
        </button>
        <span class="ml-auto text-xs text-slate-400"><?= count($students) ?> students</span>
    </div>

    <form method="POST">
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden mb-4">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide w-8">#</th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Student</th>
                        <th class="text-center px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Present</th>
                        <th class="text-center px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Absent</th>
                        <th class="text-center px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Late</th>
                        <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $i => $s):
                        $stuId   = (int)$s['STUDENT_ID'];
                        $current = $existing[$stuId]['STATUS'] ?? 'PRESENT';
                        $remark  = $existing[$stuId]['REMARKS'] ?? '';
                    ?>
                    <tr class="border-b border-slate-100 hover:bg-slate-50 att-row" data-id="<?= $stuId ?>">
                        <td class="px-4 py-3 text-slate-400 text-xs"><?= $i + 1 ?></td>
                        <td class="px-4 py-3 font-medium text-slate-800"><?= htmlspecialchars($s['FULLNAME'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-center">
                            <input type="radio"
                                   name="attendance[<?= $stuId ?>]"
                                   value="PRESENT"
                                   class="att-radio w-4 h-4 accent-green-600"
                                   <?= $current === 'PRESENT' ? 'checked' : '' ?>>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <input type="radio"
                                   name="attendance[<?= $stuId ?>]"
                                   value="ABSENT"
                                   class="att-radio w-4 h-4 accent-red-600"
                                   <?= $current === 'ABSENT' ? 'checked' : '' ?>>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <input type="radio"
                                   name="attendance[<?= $stuId ?>]"
                                   value="LATE"
                                   class="att-radio w-4 h-4 accent-yellow-500"
                                   <?= $current === 'LATE' ? 'checked' : '' ?>>
                        </td>
                        <td class="px-4 py-3">
                            <input type="text"
                                   name="remarks[<?= $stuId ?>]"
                                   value="<?= htmlspecialchars($remark, ENT_QUOTES, 'UTF-8') ?>"
                                   placeholder="Optional…"
                                   class="border border-slate-200 rounded px-2 py-1 text-xs w-full focus:outline-none focus:ring-1 focus:ring-indigo-400">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="bg-indigo-800 text-white px-6 py-2.5 rounded-lg hover:bg-indigo-700 inline-flex items-center gap-2 text-sm font-medium">
                <i class="ti ti-device-floppy"></i> Save Attendance
            </button>
            <a href="/PTE-MANAGEMENT-SYSTEM/sessions/show?id=<?= $sessionId ?>"
               class="bg-slate-100 text-slate-600 px-5 py-2.5 rounded-lg hover:bg-slate-200 inline-flex items-center gap-2 text-sm">
                Cancel
            </a>
        </div>
    </form>

    <?php endif; ?>
</main>

<script>
function markAll(status) {
    document.querySelectorAll('.att-row').forEach(function(row) {
        var id    = row.dataset.id;
        var radio = row.querySelector('input[value="' + status + '"]');
        if (radio) radio.checked = true;
    });
}
</script>

<?php require_once '../../views/layout/footer.php'; ?>
