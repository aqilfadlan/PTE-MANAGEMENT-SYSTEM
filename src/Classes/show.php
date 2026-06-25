<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/src/Auth/login.php');
    exit;
}
if (!in_array($_SESSION['role'], ['OWNER', 'ADMIN'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/src/Dashboard/index.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id === 0) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/src/Classes/index.php');
    exit;
}

// ── Handle POST actions before any output ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    try {
        $conn = getConnection();

        if ($action === 'add_schedule') {
            $day   = $_POST['daysofweek']    ?? '';
            $start = $_POST['start_time']    ?? '';
            $end   = $_POST['end_time']      ?? '';
            $from  = $_POST['effective_from'] ?? '';
            $toRaw = trim($_POST['effective_to'] ?? '');
            $hasTo = ($toRaw !== '');

            if ($hasTo) {
                $sql  = "INSERT INTO CLASS_SCHEDULE (class_id, daysofweek, start_time, end_time, effective_from, effective_to)
                         VALUES (:class_id, :day, :start, :end,
                                 TO_DATE(:from, 'YYYY-MM-DD'), TO_DATE(:to, 'YYYY-MM-DD'))";
                $stmt = oci_parse($conn, $sql);
                oci_bind_by_name($stmt, ':class_id', $id);
                oci_bind_by_name($stmt, ':day',      $day);
                oci_bind_by_name($stmt, ':start',    $start);
                oci_bind_by_name($stmt, ':end',      $end);
                oci_bind_by_name($stmt, ':from',     $from);
                oci_bind_by_name($stmt, ':to',       $toRaw);
            } else {
                $sql  = "INSERT INTO CLASS_SCHEDULE (class_id, daysofweek, start_time, end_time, effective_from)
                         VALUES (:class_id, :day, :start, :end, TO_DATE(:from, 'YYYY-MM-DD'))";
                $stmt = oci_parse($conn, $sql);
                oci_bind_by_name($stmt, ':class_id', $id);
                oci_bind_by_name($stmt, ':day',      $day);
                oci_bind_by_name($stmt, ':start',    $start);
                oci_bind_by_name($stmt, ':end',      $end);
                oci_bind_by_name($stmt, ':from',     $from);
            }
            oci_execute($stmt);
            oci_commit($conn);
            oci_free_statement($stmt);
            $_SESSION['flash_success'] = 'Schedule added.';

        } elseif ($action === 'delete_schedule') {
            $schId = (int)($_POST['schedule_id'] ?? 0);
            $sql   = 'DELETE FROM CLASS_SCHEDULE WHERE schedule_id = :id AND class_id = :class_id';
            $stmt  = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':id',       $schId);
            oci_bind_by_name($stmt, ':class_id', $id);
            oci_execute($stmt);
            oci_commit($conn);
            oci_free_statement($stmt);
            $_SESSION['flash_success'] = 'Schedule removed.';

        } elseif ($action === 'unenrol') {
            $stuId = (int)($_POST['student_id'] ?? 0);
            $sql   = 'DELETE FROM CLASS_STUDENT WHERE class_id = :class_id AND student_id = :student_id';
            $stmt  = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':class_id',   $id);
            oci_bind_by_name($stmt, ':student_id', $stuId);
            oci_execute($stmt);
            oci_commit($conn);
            oci_free_statement($stmt);
            $_SESSION['flash_success'] = 'Student removed from class.';
        }

        oci_close($conn);
    } catch (\RuntimeException $e) {
        $_SESSION['flash_error'] = 'Action failed. Please try again.';
    }
    header('Location: /PTE-MANAGEMENT-SYSTEM/src/Classes/show.php?id=' . $id);
    exit;
}

// ── Fetch data ────────────────────────────────────────────────────────────────
try {
    $conn = getConnection();

    $sql  = "SELECT c.class_id, c.name, c.fee, c.max_students, c.status,
                    s.name      AS subject_name, s.code AS subject_code,
                    g.name      AS grade_name,
                    u.fullname  AS tutor_name, u.email AS tutor_email,
                    TO_CHAR(c.created_at, 'YYYY-MM-DD') AS created_at
             FROM   CLASS   c
             JOIN   SUBJECT s ON s.subject_id = c.subject_id
             JOIN   GRADE   g ON g.grade_id   = c.grade_id
             JOIN   USERS   u ON u.user_id    = c.user_id
             WHERE  c.class_id = :id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':id', $id);
    oci_execute($stmt);
    $class = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);

    if (!$class) {
        oci_close($conn);
        $_SESSION['flash_error'] = 'Class not found.';
        header('Location: /PTE-MANAGEMENT-SYSTEM/src/Classes/index.php');
        exit;
    }

    // Enrolled students
    $stuSql  = "SELECT s.student_id, s.fullname, s.status AS student_status,
                       g.name AS grade_name,
                       TO_CHAR(cs.enrolled_at, 'YYYY-MM-DD') AS enrolled_at
                FROM   CLASS_STUDENT cs
                JOIN   STUDENT s ON s.student_id = cs.student_id
                JOIN   GRADE   g ON g.grade_id   = s.grade_id
                WHERE  cs.class_id = :id
                ORDER  BY s.fullname";
    $stuStmt = oci_parse($conn, $stuSql);
    oci_bind_by_name($stuStmt, ':id', $id);
    oci_execute($stuStmt);
    $students = [];
    while ($r = oci_fetch_assoc($stuStmt)) $students[] = $r;
    oci_free_statement($stuStmt);

    // Schedules
    $schSql  = "SELECT schedule_id, daysofweek, start_time, end_time,
                       TO_CHAR(effective_from, 'YYYY-MM-DD') AS effective_from,
                       TO_CHAR(effective_to,   'YYYY-MM-DD') AS effective_to
                FROM   CLASS_SCHEDULE
                WHERE  class_id = :id
                ORDER  BY CASE daysofweek
                            WHEN 'MON' THEN 1 WHEN 'TUE' THEN 2 WHEN 'WED' THEN 3
                            WHEN 'THU' THEN 4 WHEN 'FRI' THEN 5 WHEN 'SAT' THEN 6
                            WHEN 'SUN' THEN 7 END,
                          start_time";
    $schStmt = oci_parse($conn, $schSql);
    oci_bind_by_name($schStmt, ':id', $id);
    oci_execute($schStmt);
    $schedules = [];
    while ($r = oci_fetch_assoc($schStmt)) $schedules[] = $r;
    oci_free_statement($schStmt);

    // Recent sessions (last 5)
    $sesSql  = "SELECT session_id,
                       TO_CHAR(session_date, 'YYYY-MM-DD') AS session_date,
                       start_time, end_time, status
                FROM   CLASS_SESSION
                WHERE  class_id = :id
                ORDER  BY session_date DESC
                FETCH NEXT 5 ROWS ONLY";
    $sesStmt = oci_parse($conn, $sesSql);
    oci_bind_by_name($sesStmt, ':id', $id);
    oci_execute($sesStmt);
    $sessions = [];
    while ($r = oci_fetch_assoc($sesStmt)) $sessions[] = $r;
    oci_free_statement($sesStmt);

    oci_close($conn);
} catch (\RuntimeException $e) {
    $_SESSION['flash_error'] = 'Database error.';
    header('Location: /PTE-MANAGEMENT-SYSTEM/src/Classes/index.php');
    exit;
}

$dayLabels = [
    'MON' => 'Monday', 'TUE' => 'Tuesday', 'WED' => 'Wednesday',
    'THU' => 'Thursday', 'FRI' => 'Friday', 'SAT' => 'Saturday', 'SUN' => 'Sunday',
];
$sesColors = [
    'SCHEDULED' => 'bg-yellow-100 text-yellow-700',
    'COMPLETED' => 'bg-green-100 text-green-700',
    'CANCELLED' => 'bg-red-100 text-red-700',
];

$pageTitle = htmlspecialchars($class['NAME'], ENT_QUOTES, 'UTF-8') . ' — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="ml-64 p-8 min-h-screen">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/PTE-MANAGEMENT-SYSTEM/src/Classes/index.php" class="text-slate-400 hover:text-slate-600">
                <i class="ti ti-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-xl font-semibold text-slate-800"><?= htmlspecialchars($class['NAME'], ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="text-slate-500 text-sm mt-1">
                    <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2 py-0.5 rounded"><?= htmlspecialchars($class['SUBJECT_CODE'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?= htmlspecialchars($class['SUBJECT_NAME'], ENT_QUOTES, 'UTF-8') ?>
                    &middot; <?= htmlspecialchars($class['GRADE_NAME'], ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="/PTE-MANAGEMENT-SYSTEM/src/Students/enrol.php?class_id=<?= $id ?>"
               class="bg-indigo-100 text-indigo-800 px-4 py-2 rounded-lg hover:bg-indigo-200 inline-flex items-center gap-2 text-sm">
                <i class="ti ti-user-plus"></i> Manage Enrolment
            </a>
            <a href="/PTE-MANAGEMENT-SYSTEM/src/Classes/edit.php?id=<?= $id ?>"
               class="bg-indigo-800 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 inline-flex items-center gap-2 text-sm">
                <i class="ti ti-pencil"></i> Edit
            </a>
        </div>
    </div>

    <?php require_once '../../views/partials/flash.php'; ?>

    <!-- Info cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Monthly Fee</p>
            <p class="text-2xl font-bold text-indigo-800">RM <?= number_format((float)$class['FEE'], 2) ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Enrolled</p>
            <p class="text-2xl font-bold text-indigo-800"><?= count($students) ?><span class="text-sm font-normal text-slate-400"> / <?= (int)$class['MAX_STUDENTS'] ?></span></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Tutor</p>
            <p class="text-sm font-semibold text-slate-800 mt-1"><?= htmlspecialchars($class['TUTOR_NAME'], ENT_QUOTES, 'UTF-8') ?></p>
            <p class="text-xs text-slate-400 truncate"><?= htmlspecialchars($class['TUTOR_EMAIL'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Status</p>
            <div class="mt-2">
                <?php if ($class['STATUS'] === 'ACTIVE'): ?>
                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                <?php else: ?>
                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Inactive</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Schedules -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-slate-800">Schedules</h2>
                <button onclick="document.getElementById('add-schedule-modal').classList.remove('hidden')"
                        class="text-xs text-indigo-600 hover:text-indigo-800 inline-flex items-center gap-1 font-medium">
                    <i class="ti ti-plus"></i> Add
                </button>
            </div>
            <?php if (empty($schedules)): ?>
            <p class="text-slate-400 text-sm">No schedules defined yet.</p>
            <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($schedules as $sch): ?>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2 py-0.5 rounded w-10 text-center">
                            <?= substr($dayLabels[$sch['DAYSOFWEEK']] ?? $sch['DAYSOFWEEK'], 0, 3) ?>
                        </span>
                        <span class="text-sm text-slate-700"><?= htmlspecialchars($sch['START_TIME'], ENT_QUOTES, 'UTF-8') ?> – <?= htmlspecialchars($sch['END_TIME'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <form method="POST" onsubmit="return confirm('Remove this schedule?')">
                        <input type="hidden" name="action"      value="delete_schedule">
                        <input type="hidden" name="schedule_id" value="<?= (int)$sch['SCHEDULE_ID'] ?>">
                        <button type="submit" class="text-red-400 hover:text-red-600">
                            <i class="ti ti-trash text-xs"></i>
                        </button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Recent Sessions -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-slate-800">Recent Sessions</h2>
                <a href="/PTE-MANAGEMENT-SYSTEM/src/Sessions/index.php?class_id=<?= $id ?>"
                   class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">View all</a>
            </div>
            <?php if (empty($sessions)): ?>
            <p class="text-slate-400 text-sm">No sessions generated yet.</p>
            <?php else: ?>
            <table class="w-full text-sm">
                <thead>
                    <tr>
                        <th class="text-left pb-2 text-xs text-slate-400 uppercase font-medium">Date</th>
                        <th class="text-left pb-2 text-xs text-slate-400 uppercase font-medium">Time</th>
                        <th class="text-left pb-2 text-xs text-slate-400 uppercase font-medium">Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($sessions as $ses): ?>
                <tr class="border-t border-slate-100">
                    <td class="py-2 text-slate-700"><?= date('d M Y', strtotime($ses['SESSION_DATE'])) ?></td>
                    <td class="py-2 text-slate-500"><?= htmlspecialchars($ses['START_TIME'], ENT_QUOTES, 'UTF-8') ?> – <?= htmlspecialchars($ses['END_TIME'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="py-2">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium <?= $sesColors[$ses['STATUS']] ?? 'bg-slate-100 text-slate-600' ?>">
                            <?= htmlspecialchars(ucfirst(strtolower($ses['STATUS'])), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Enrolled Students -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-800">Enrolled Students (<?= count($students) ?>)</h2>
            <a href="/PTE-MANAGEMENT-SYSTEM/src/Students/enrol.php?class_id=<?= $id ?>"
               class="text-xs text-indigo-600 hover:text-indigo-800 font-medium inline-flex items-center gap-1">
                <i class="ti ti-user-plus"></i> Manage Enrolment
            </a>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Student</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Grade</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Status</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Enrolled</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                <tr><td colspan="5" class="text-center py-8 text-slate-400">No students enrolled yet.</td></tr>
                <?php else: ?>
                <?php foreach ($students as $s): ?>
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-800">
                        <a href="/PTE-MANAGEMENT-SYSTEM/src/Students/show.php?id=<?= (int)$s['STUDENT_ID'] ?>"
                           class="hover:text-indigo-700">
                            <?= htmlspecialchars($s['FULLNAME'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </td>
                    <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($s['GRADE_NAME'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3">
                        <?php if ($s['STUDENT_STATUS'] === 'ACTIVE'): ?>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                        <?php else: ?>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-slate-400 text-xs"><?= date('d M Y', strtotime($s['ENROLLED_AT'])) ?></td>
                    <td class="px-4 py-3 text-right">
                        <form method="POST" onsubmit="return confirm('Remove this student from the class?')">
                            <input type="hidden" name="action"     value="unenrol">
                            <input type="hidden" name="student_id" value="<?= (int)$s['STUDENT_ID'] ?>">
                            <button type="submit"
                                    class="inline-flex items-center gap-1 text-red-500 hover:text-red-700 text-xs font-medium">
                                <i class="ti ti-user-minus"></i> Remove
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- Add Schedule Modal -->
<div id="add-schedule-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md">
        <h3 class="font-semibold text-slate-800 mb-4">Add Schedule</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add_schedule">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Day of Week</label>
                    <select name="daysofweek" required
                            class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="MON">Monday</option>
                        <option value="TUE">Tuesday</option>
                        <option value="WED">Wednesday</option>
                        <option value="THU">Thursday</option>
                        <option value="FRI">Friday</option>
                        <option value="SAT">Saturday</option>
                        <option value="SUN">Sunday</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Start Time</label>
                        <input type="time" name="start_time" required
                               class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">End Time</label>
                        <input type="time" name="end_time" required
                               class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Effective From</label>
                        <input type="date" name="effective_from" required
                               class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Effective To <span class="text-slate-400 font-normal">(optional)</span></label>
                        <input type="date" name="effective_to"
                               class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
            </div>
            <div class="flex gap-3 mt-5 justify-end">
                <button type="button" onclick="document.getElementById('add-schedule-modal').classList.add('hidden')"
                        class="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm">Cancel</button>
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-indigo-800 text-white hover:bg-indigo-700 text-sm">Save Schedule</button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../../views/layout/footer.php'; ?>
