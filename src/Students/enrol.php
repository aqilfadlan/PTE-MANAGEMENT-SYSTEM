<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/login');
    exit;
}
if (!in_array($_SESSION['role'], ['OWNER', 'ADMIN'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/dashboard');
    exit;
}

$classId = (int)($_GET['class_id'] ?? 0);
if ($classId === 0) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/classes');
    exit;
}

// ── Handle POST: enrol one or more students ───────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentIds = $_POST['student_ids'] ?? ($_POST['student_id'] ?? []);
    if (!is_array($studentIds)) $studentIds = [$studentIds];
    $studentIds = array_unique(array_filter(array_map('intval', $studentIds), fn($id) => $id > 0));

    if (!empty($studentIds)) {
        try {
            $conn = getConnection();
            $enrolledCount = 0;
            $skippedCount  = 0;

            foreach ($studentIds as $studentId) {
                $chkSql  = 'SELECT COUNT(*) AS cnt FROM CLASS_STUDENT WHERE class_id = :class_id AND student_id = :student_id';
                $chkStmt = oci_parse($conn, $chkSql);
                oci_bind_by_name($chkStmt, ':class_id',   $classId);
                oci_bind_by_name($chkStmt, ':student_id', $studentId);
                oci_execute($chkStmt);
                $already = (int)oci_fetch_assoc($chkStmt)['CNT'] > 0;
                oci_free_statement($chkStmt);

                if ($already) {
                    $skippedCount++;
                    continue;
                }

                $sql  = 'INSERT INTO CLASS_STUDENT (class_id, student_id) VALUES (:class_id, :student_id)';
                $stmt = oci_parse($conn, $sql);
                oci_bind_by_name($stmt, ':class_id',   $classId);
                oci_bind_by_name($stmt, ':student_id', $studentId);
                oci_execute($stmt);
                oci_free_statement($stmt);
                $enrolledCount++;
            }

            oci_commit($conn);
            oci_close($conn);

            if ($enrolledCount > 0) {
                $_SESSION['flash_success'] = $enrolledCount . ' student' . ($enrolledCount === 1 ? '' : 's') . ' enrolled successfully'
                    . ($skippedCount > 0 ? ", $skippedCount already enrolled." : '.');
            } else {
                $_SESSION['flash_error'] = 'Selected student(s) are already enrolled.';
            }
        } catch (\RuntimeException $e) {
            $_SESSION['flash_error'] = 'Could not enrol student(s). Please try again.';
        }
    }
    header('Location: /PTE-MANAGEMENT-SYSTEM/students/enrol?class_id=' . $classId);
    exit;
}

// ── Fetch class info ──────────────────────────────────────────────────────────
try {
    $conn = getConnection();

    $sql  = 'SELECT c.class_id, c.name, c.max_students, c.status, c.grade_id,
                    s.name AS subject_name, g.name AS grade_name
             FROM   CLASS   c
             JOIN   SUBJECT s ON s.subject_id = c.subject_id
             JOIN   GRADE   g ON g.grade_id   = c.grade_id
             WHERE  c.class_id = :id';
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':id', $classId);
    oci_execute($stmt);
    $class = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);

    if (!$class) {
        oci_close($conn);
        header('Location: /PTE-MANAGEMENT-SYSTEM/classes');
        exit;
    }

    // Already enrolled students
    $enrSql  = "SELECT s.student_id, s.fullname, s.status AS student_status,
                       g.name AS grade_name,
                       TO_CHAR(cs.enrolled_at, 'YYYY-MM-DD') AS enrolled_at
                FROM   CLASS_STUDENT cs
                JOIN   STUDENT s ON s.student_id = cs.student_id
                JOIN   GRADE   g ON g.grade_id   = s.grade_id
                WHERE  cs.class_id = :id
                ORDER  BY s.fullname";
    $enrStmt = oci_parse($conn, $enrSql);
    oci_bind_by_name($enrStmt, ':id', $classId);
    oci_execute($enrStmt);
    $enrolled = [];
    while ($r = oci_fetch_assoc($enrStmt)) $enrolled[] = $r;
    oci_free_statement($enrStmt);

    // Collect enrolled IDs for exclusion
    $enrolledIds = array_column($enrolled, 'STUDENT_ID');

    // Search for students to add — grade filter defaults to the class's own
    // grade so staff see relevant students first; "All grades" (grade=0) or
    // another grade is still explicitly selectable via the dropdown.
    $search      = trim($_GET['search'] ?? '');
    $gradeFilter = isset($_GET['grade']) ? (int)$_GET['grade'] : (int)$class['GRADE_ID'];

    $where  = 's.student_id NOT IN (SELECT student_id FROM CLASS_STUDENT WHERE class_id = :class_id)';
    $params = [':class_id' => $classId];

    if ($search !== '') {
        $where .= ' AND LOWER(s.fullname) LIKE LOWER(:search)';
        $params[':search'] = '%' . $search . '%';
    }
    if ($gradeFilter > 0) {
        $where .= ' AND s.grade_id = :grade';
        $params[':grade'] = $gradeFilter;
    }

    $avaSql  = "SELECT s.student_id, s.fullname, s.status,
                       g.name AS grade_name
                FROM   STUDENT s
                JOIN   GRADE   g ON g.grade_id = s.grade_id
                WHERE  $where
                AND    s.status = 'ACTIVE'
                ORDER  BY s.fullname
                FETCH NEXT 50 ROWS ONLY";
    $avaStmt = oci_parse($conn, $avaSql);
    foreach ($params as $k => &$v) oci_bind_by_name($avaStmt, $k, $v);
    unset($v);
    oci_execute($avaStmt);
    $available = [];
    while ($r = oci_fetch_assoc($avaStmt)) $available[] = $r;
    oci_free_statement($avaStmt);

    // Grade dropdown for filter
    $gradeStmt = oci_parse($conn, 'SELECT grade_id, name FROM GRADE ORDER BY grade_level');
    oci_execute($gradeStmt);
    $grades = [];
    while ($r = oci_fetch_assoc($gradeStmt)) $grades[] = $r;
    oci_free_statement($gradeStmt);

    oci_close($conn);
} catch (\RuntimeException $e) {
    $_SESSION['flash_error'] = 'Database error.';
    header('Location: /PTE-MANAGEMENT-SYSTEM/classes');
    exit;
}

$pageTitle = 'Manage Enrolment — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="pt-16 md:pt-10 md:ml-64 px-4 sm:px-8 pb-4 sm:pb-8 min-h-screen">
    <div class="mb-6 flex items-center gap-3">
        <a href="/PTE-MANAGEMENT-SYSTEM/classes/show?id=<?= $classId ?>" class="text-slate-400 hover:text-slate-600">
            <i class="ti ti-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Manage Enrolment</h1>
            <p class="text-slate-500 text-sm mt-1">
                <?= htmlspecialchars($class['NAME'], ENT_QUOTES, 'UTF-8') ?>
                &middot; <?= htmlspecialchars($class['SUBJECT_NAME'], ENT_QUOTES, 'UTF-8') ?>
                &middot; <?= htmlspecialchars($class['GRADE_NAME'], ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>
    </div>

    <?php require_once '../../views/partials/flash.php'; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Currently Enrolled -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-800">
                    Currently Enrolled
                    <span class="ml-2 bg-indigo-100 text-indigo-800 text-xs font-medium px-2 py-0.5 rounded-full">
                        <?= count($enrolled) ?> / <?= (int)$class['MAX_STUDENTS'] ?>
                    </span>
                </h2>
            </div>
            <?php if (empty($enrolled)): ?>
            <div class="px-6 py-8 text-center text-slate-400">
                <i class="ti ti-users text-3xl block mb-2"></i>
                No students enrolled yet.
            </div>
            <?php else: ?>
            <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                <?php foreach ($enrolled as $s): ?>
                <div class="px-4 py-3 flex items-center justify-between hover:bg-slate-50">
                    <div>
                        <p class="text-sm font-medium text-slate-800"><?= htmlspecialchars($s['FULLNAME'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="text-xs text-slate-400"><?= htmlspecialchars($s['GRADE_NAME'], ENT_QUOTES, 'UTF-8') ?>
                            &middot; Since <?= date('d M Y', strtotime($s['ENROLLED_AT'])) ?></p>
                    </div>
                    <form method="POST" action="/PTE-MANAGEMENT-SYSTEM/classes/show?id=<?= $classId ?>"
                          onsubmit="return confirm('Remove this student from the class?')">
                        <input type="hidden" name="action"     value="unenrol">
                        <input type="hidden" name="student_id" value="<?= (int)$s['STUDENT_ID'] ?>">
                        <button type="submit"
                                class="inline-flex items-center gap-1 text-red-500 hover:text-red-700 text-xs font-medium">
                            <i class="ti ti-user-minus"></i> Remove
                        </button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Add Students -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-800">Add Students</h2>
            </div>

            <!-- Search / filter -->
            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50">
                <form method="GET" class="flex gap-2">
                    <input type="hidden" name="class_id" value="<?= $classId ?>">
                    <input type="text" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="Search name…"
                           class="flex-1 border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <select name="grade"
                            class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">All grades</option>
                        <?php foreach ($grades as $g): ?>
                        <option value="<?= (int)$g['GRADE_ID'] ?>" <?= $gradeFilter === (int)$g['GRADE_ID'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g['NAME'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit"
                            class="bg-indigo-800 text-white px-3 py-1.5 rounded-lg hover:bg-indigo-700 text-sm">
                        <i class="ti ti-search"></i>
                    </button>
                </form>
                <?php if ($gradeFilter === (int)$class['GRADE_ID'] && !isset($_GET['grade'])): ?>
                <p class="text-xs text-slate-400 mt-2">Showing <?= htmlspecialchars($class['GRADE_NAME'], ENT_QUOTES, 'UTF-8') ?> students by default — switch to "All grades" to enrol from other grades.</p>
                <?php endif; ?>
            </div>

            <?php if (empty($available)): ?>
            <div class="px-6 py-8 text-center text-slate-400 text-sm">
                <?= ($search !== '' || $gradeFilter > 0) ? 'No matching students found.' : 'All active students are already enrolled.' ?>
            </div>
            <?php else: ?>
            <form method="POST" id="bulk-enrol-form">
                <div class="px-4 py-2.5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <label class="flex items-center gap-2 text-xs font-medium text-slate-600 cursor-pointer">
                        <input type="checkbox" id="select-all-checkbox"
                               class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                        Select all (<?= count($available) ?>)
                    </label>
                    <button type="submit" id="bulk-enrol-submit" disabled
                            class="bg-indigo-100 text-indigo-400 px-3 py-1.5 rounded-lg text-xs font-medium inline-flex items-center gap-1.5
                                   disabled:cursor-not-allowed enabled:bg-indigo-800 enabled:text-white enabled:hover:bg-indigo-700 transition">
                        <i class="ti ti-users-plus"></i> Enrol Selected (<span id="selected-count">0</span>)
                    </button>
                </div>
                <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                    <?php foreach ($available as $s): ?>
                    <label class="px-4 py-3 flex items-center gap-3 hover:bg-slate-50 cursor-pointer">
                        <input type="checkbox" name="student_ids[]" value="<?= (int)$s['STUDENT_ID'] ?>"
                               class="student-checkbox w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-slate-800 truncate"><?= htmlspecialchars($s['FULLNAME'], ENT_QUOTES, 'UTF-8') ?></p>
                            <p class="text-xs text-slate-400"><?= htmlspecialchars($s['GRADE_NAME'], ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </form>
            <?php endif; ?>
        </div>

    </div>
</main>

<script>
(function () {
    var selectAll  = document.getElementById('select-all-checkbox');
    var checkboxes = document.querySelectorAll('.student-checkbox');
    var submitBtn  = document.getElementById('bulk-enrol-submit');
    var countEl    = document.getElementById('selected-count');
    if (!submitBtn) return;

    function updateState() {
        var checked = document.querySelectorAll('.student-checkbox:checked').length;
        countEl.textContent = checked;
        submitBtn.disabled = checked === 0;
        if (selectAll) selectAll.checked = checked > 0 && checked === checkboxes.length;
    }

    checkboxes.forEach(function (cb) {
        cb.addEventListener('change', updateState);
    });

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(function (cb) { cb.checked = selectAll.checked; });
            updateState();
        });
    }

    updateState();
})();
</script>

<?php require_once '../../views/layout/footer.php'; ?>
