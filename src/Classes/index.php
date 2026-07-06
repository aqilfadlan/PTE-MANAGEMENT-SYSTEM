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

$search  = trim($_GET['search']  ?? '');
$subject = (int)($_GET['subject'] ?? 0);
$grade   = (int)($_GET['grade']   ?? 0);
$status  = $_GET['status'] ?? '';
$page    = max(1, (int)($_GET['page'] ?? 1));
$limit   = 10;
$offset  = ($page - 1) * $limit;

try {
    $conn = getConnection();

    // Dropdowns
    $subjStmt = oci_parse($conn, 'SELECT subject_id, name FROM SUBJECT ORDER BY name');
    oci_execute($subjStmt);
    $subjects = [];
    while ($r = oci_fetch_assoc($subjStmt)) $subjects[] = $r;
    oci_free_statement($subjStmt);

    $gradeStmt = oci_parse($conn, 'SELECT grade_id, name FROM GRADE ORDER BY grade_level');
    oci_execute($gradeStmt);
    $grades = [];
    while ($r = oci_fetch_assoc($gradeStmt)) $grades[] = $r;
    oci_free_statement($gradeStmt);

    // Build WHERE
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

    // Count
    $countSql  = "SELECT COUNT(*) AS total
                  FROM   CLASS c
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
    $sql  = "SELECT c.class_id, c.name, c.fee, c.max_students, c.status,
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
             GROUP  BY c.class_id, c.name, c.fee, c.max_students, c.status,
                       s.name, g.name, u.fullname
             ORDER  BY c.name
             OFFSET :offset ROWS FETCH NEXT :limit ROWS ONLY";
    $stmt = oci_parse($conn, $sql);
    foreach ($params as $k => &$v) oci_bind_by_name($stmt, $k, $v);
    unset($v);
    oci_bind_by_name($stmt, ':offset', $offset);
    oci_bind_by_name($stmt, ':limit',  $limit);
    oci_execute($stmt);
    $classes = [];
    while ($r = oci_fetch_assoc($stmt)) $classes[] = $r;
    oci_free_statement($stmt);
    oci_close($conn);
} catch (\RuntimeException $e) {
    $classes = []; $subjects = []; $grades = []; $total = 0; $totalPages = 1;
}

$pageTitle = 'Classes — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="pt-16 md:pt-10 md:ml-64 px-4 sm:px-8 pb-4 sm:pb-8 min-h-screen">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Classes</h1>
            <p class="text-slate-500 text-sm mt-1">Manage all tuition classes</p>
        </div>
        <a href="/PTE-MANAGEMENT-SYSTEM/classes/create"
           class="bg-indigo-800 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 inline-flex items-center gap-2 text-sm">
            <i class="ti ti-plus"></i> Add Class
        </a>
    </div>

    <?php require_once '../../views/partials/flash.php'; ?>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4 mb-4">
        <form method="GET" class="flex gap-3 items-end flex-wrap">
            <div class="flex-1 min-w-40">
                <label class="block text-xs font-medium text-slate-500 mb-1">Search</label>
                <input type="text" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="Class name…"
                       class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Subject</label>
                <select name="subject" class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Subjects</option>
                    <?php foreach ($subjects as $s): ?>
                    <option value="<?= (int)$s['SUBJECT_ID'] ?>" <?= $subject === (int)$s['SUBJECT_ID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['NAME'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Grade</label>
                <select name="grade" class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Grades</option>
                    <?php foreach ($grades as $g): ?>
                    <option value="<?= (int)$g['GRADE_ID'] ?>" <?= $grade === (int)$g['GRADE_ID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['NAME'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
                <select name="status" class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All</option>
                    <option value="ACTIVE"   <?= $status === 'ACTIVE'   ? 'selected' : '' ?>>Active</option>
                    <option value="INACTIVE" <?= $status === 'INACTIVE' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <button type="submit"
                    class="bg-indigo-800 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 text-sm inline-flex items-center gap-2">
                <i class="ti ti-search"></i> Search
            </button>
            <?php if ($search !== '' || $subject > 0 || $grade > 0 || $status !== ''): ?>
            <a href="/PTE-MANAGEMENT-SYSTEM/classes"
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
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Class</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Subject</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Grade</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Tutor</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Fee</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Enrolled</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Status</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($classes)): ?>
                <tr>
                    <td colspan="8" class="text-center py-10 text-slate-400">
                        <i class="ti ti-books text-3xl block mb-2"></i>
                        No classes found.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($classes as $c): ?>
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-800">
                        <a href="/PTE-MANAGEMENT-SYSTEM/classes/show?id=<?= (int)$c['CLASS_ID'] ?>"
                           class="hover:text-indigo-700">
                            <?= htmlspecialchars($c['NAME'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </td>
                    <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($c['SUBJECT_NAME'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($c['GRADE_NAME'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($c['TUTOR_NAME'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-slate-600">RM <?= number_format((float)$c['FEE'], 2) ?></td>
                    <td class="px-4 py-3 text-slate-600">
                        <?= (int)$c['ENROLLED_COUNT'] ?> / <?= (int)$c['MAX_STUDENTS'] ?>
                    </td>
                    <td class="px-4 py-3">
                        <?php if ($c['STATUS'] === 'ACTIVE'): ?>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                        <?php else: ?>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="/PTE-MANAGEMENT-SYSTEM/classes/show?id=<?= (int)$c['CLASS_ID'] ?>"
                           class="inline-flex items-center gap-1 text-slate-500 hover:text-slate-700 text-xs font-medium mr-2">
                            <i class="ti ti-eye"></i> View
                        </a>
                        <a href="/PTE-MANAGEMENT-SYSTEM/classes/edit?id=<?= (int)$c['CLASS_ID'] ?>"
                           class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                            <i class="ti ti-pencil"></i> Edit
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="flex items-center justify-between mt-4 text-sm text-slate-500">
        <span>Showing <?= count($classes) ?> of <?= $total ?> classes</span>
        <?php
            $baseParams = ['search' => $search, 'subject' => $subject, 'grade' => $grade, 'status' => $status];
            require_once '../../views/partials/pagination.php';
        ?>
    </div>
    <?php endif; ?>
</main>

<?php require_once '../../views/layout/footer.php'; ?>
