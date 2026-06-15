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
    header('Location: /PTE-MANAGEMENT-SYSTEM/src/Students/index.php');
    exit;
}

try {
    $conn = getConnection();

    $sql  = 'SELECT s.student_id, s.fullname, s.ic_number, s.phone, s.status, s.created_at,
                    g.name AS grade_name, g.grade_level,
                    p.parent_id, p.fullname AS parent_name, p.phone AS parent_phone, p.email AS parent_email
             FROM   STUDENT s
             JOIN   GRADE   g ON g.grade_id  = s.grade_id
             JOIN   PARENT  p ON p.parent_id = s.parent_id
             WHERE  s.student_id = :id';
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':id', $id);
    oci_execute($stmt);
    $student = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);

    if (!$student) {
        oci_close($conn);
        $_SESSION['flash_error'] = 'Student not found.';
        header('Location: /PTE-MANAGEMENT-SYSTEM/src/Students/index.php');
        exit;
    }

    // Enrolled classes
    $clsSql  = 'SELECT c.class_id, c.name AS class_name, c.fee,
                       s.name AS subject_name, u.fullname AS tutor_name,
                       cs.enrolled_at
                FROM   CLASS_STUDENT cs
                JOIN   CLASS         c  ON c.class_id   = cs.class_id
                JOIN   SUBJECT       s  ON s.subject_id = c.subject_id
                JOIN   USERS         u  ON u.user_id    = c.user_id
                WHERE  cs.student_id = :id
                ORDER  BY cs.enrolled_at DESC';
    $clsStmt = oci_parse($conn, $clsSql);
    oci_bind_by_name($clsStmt, ':id', $id);
    oci_execute($clsStmt);
    $classes = [];
    while ($r = oci_fetch_assoc($clsStmt)) $classes[] = $r;
    oci_free_statement($clsStmt);

    // Recent attendance (last 10)
    $attSql  = 'SELECT sa.status AS att_status, sa.remarks,
                       cs.session_date, cs.start_time,
                       c.name AS class_name
                FROM   STUDENT_ATTENDANCE sa
                JOIN   CLASS_SESSION      cs ON cs.session_id = sa.session_id
                JOIN   CLASS              c  ON c.class_id    = cs.class_id
                WHERE  sa.student_id = :id
                ORDER  BY cs.session_date DESC
                FETCH NEXT 10 ROWS ONLY';
    $attStmt = oci_parse($conn, $attSql);
    oci_bind_by_name($attStmt, ':id', $id);
    oci_execute($attStmt);
    $attendance = [];
    while ($r = oci_fetch_assoc($attStmt)) $attendance[] = $r;
    oci_free_statement($attStmt);

    oci_close($conn);
} catch (\RuntimeException $e) {
    $_SESSION['flash_error'] = 'Database error.';
    header('Location: /PTE-MANAGEMENT-SYSTEM/src/Students/index.php');
    exit;
}

$pageTitle = htmlspecialchars($student['FULLNAME'], ENT_QUOTES, 'UTF-8') . ' — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="ml-64 p-8 min-h-screen">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/PTE-MANAGEMENT-SYSTEM/src/Students/index.php" class="text-slate-400 hover:text-slate-600">
                <i class="ti ti-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-xl font-semibold text-slate-800"><?= htmlspecialchars($student['FULLNAME'], ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="text-slate-500 text-sm mt-1"><?= htmlspecialchars($student['GRADE_NAME'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="/PTE-MANAGEMENT-SYSTEM/src/Students/edit.php?id=<?= $id ?>"
               class="bg-indigo-100 text-indigo-800 px-4 py-2 rounded-lg hover:bg-indigo-200 inline-flex items-center gap-2 text-sm">
                <i class="ti ti-pencil"></i> Edit
            </a>
        </div>
    </div>

    <?php require_once '../../views/partials/flash.php'; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Student Info -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <h2 class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-4">Student Details</h2>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-slate-400 text-xs">IC Number</dt>
                    <dd class="text-slate-800 font-medium"><?= htmlspecialchars($student['IC_NUMBER'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div>
                    <dt class="text-slate-400 text-xs">Phone</dt>
                    <dd class="text-slate-800 font-medium"><?= htmlspecialchars($student['PHONE'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div>
                    <dt class="text-slate-400 text-xs">Status</dt>
                    <dd>
                        <?php if ($student['STATUS'] === 'ACTIVE'): ?>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                        <?php else: ?>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Inactive</span>
                        <?php endif; ?>
                    </dd>
                </div>
                <div>
                    <dt class="text-slate-400 text-xs">Enrolled Since</dt>
                    <dd class="text-slate-800 font-medium"><?= date('d M Y', strtotime($student['CREATED_AT'])) ?></dd>
                </div>
            </dl>
        </div>

        <!-- Parent Info -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <h2 class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-4">Parent / Guardian</h2>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-slate-400 text-xs">Name</dt>
                    <dd class="text-slate-800 font-medium"><?= htmlspecialchars($student['PARENT_NAME'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div>
                    <dt class="text-slate-400 text-xs">Phone</dt>
                    <dd class="text-slate-800 font-medium"><?= htmlspecialchars($student['PARENT_PHONE'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div>
                    <dt class="text-slate-400 text-xs">Email</dt>
                    <dd class="text-slate-800 font-medium"><?= htmlspecialchars($student['PARENT_EMAIL'] ?? '—', ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
            </dl>
            <a href="/PTE-MANAGEMENT-SYSTEM/src/Parents/edit.php?id=<?= $student['PARENT_ID'] ?>"
               class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 mt-4">
                <i class="ti ti-pencil"></i> Edit parent
            </a>
        </div>

        <!-- Stats -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <h2 class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-4">Summary</h2>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-slate-400 text-xs">Classes Enrolled</dt>
                    <dd class="text-2xl font-bold text-indigo-800"><?= count($classes) ?></dd>
                </div>
                <div>
                    <dt class="text-slate-400 text-xs">Recent Attendance Records</dt>
                    <dd class="text-2xl font-bold text-indigo-800"><?= count($attendance) ?></dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Enrolled Classes -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-800">Enrolled Classes</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Class</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Subject</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Tutor</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Fee</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Enrolled</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($classes)): ?>
                <tr><td colspan="5" class="text-center py-8 text-slate-400">Not enrolled in any class.</td></tr>
                <?php else: ?>
                <?php foreach ($classes as $c): ?>
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-800"><?= htmlspecialchars($c['CLASS_NAME'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($c['SUBJECT_NAME'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($c['TUTOR_NAME'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-slate-600">RM <?= number_format((float)$c['FEE'], 2) ?></td>
                    <td class="px-4 py-3 text-slate-400 text-xs"><?= date('d M Y', strtotime($c['ENROLLED_AT'])) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Recent Attendance -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-sm font-semibold text-slate-800">Recent Attendance</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Date</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Class</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Time</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($attendance)): ?>
                <tr><td colspan="4" class="text-center py-8 text-slate-400">No attendance records yet.</td></tr>
                <?php else: ?>
                <?php foreach ($attendance as $a):
                    $attColors = ['PRESENT' => 'bg-green-100 text-green-700', 'ABSENT' => 'bg-red-100 text-red-700', 'LATE' => 'bg-yellow-100 text-yellow-700'];
                    $ac = $attColors[$a['ATT_STATUS']] ?? 'bg-slate-100 text-slate-600';
                ?>
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="px-4 py-3 text-slate-800"><?= date('d M Y', strtotime($a['SESSION_DATE'])) ?></td>
                    <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($a['CLASS_NAME'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($a['START_TIME'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium <?= $ac ?>">
                            <?= htmlspecialchars($a['ATT_STATUS'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once '../../views/layout/footer.php'; ?>
