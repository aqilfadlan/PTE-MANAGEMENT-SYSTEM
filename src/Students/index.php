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

$search = trim($_GET['search'] ?? '');
$grade  = (int)($_GET['grade'] ?? 0);
$status = $_GET['status'] ?? '';
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 10;
$offset = ($page - 1) * $limit;

try {
    $conn = getConnection();

    // Grades for filter dropdown
    $gradeStmt = oci_parse($conn, 'SELECT grade_id, name FROM GRADE ORDER BY grade_level');
    oci_execute($gradeStmt);
    $grades = [];
    while ($r = oci_fetch_assoc($gradeStmt)) $grades[] = $r;
    oci_free_statement($gradeStmt);

    $where  = '1=1';
    $params = [];
    if ($search !== '') {
        $where .= " AND (LOWER(s.fullname) LIKE LOWER(:search) OR LOWER(s.ic_number) LIKE LOWER(:search2))";
        $params[':search']  = '%' . $search . '%';
        $params[':search2'] = '%' . $search . '%';
    }
    if ($grade > 0) {
        $where .= ' AND s.grade_id = :grade';
        $params[':grade'] = $grade;
    }
    if (in_array($status, ['ACTIVE', 'INACTIVE'])) {
        $where .= ' AND s.status = :status';
        $params[':status'] = $status;
    }

    $countSql  = "SELECT COUNT(*) AS total FROM STUDENT s WHERE $where";
    $countStmt = oci_parse($conn, $countSql);
    foreach ($params as $k => &$v) oci_bind_by_name($countStmt, $k, $v);
    oci_execute($countStmt);
    $total      = (int)oci_fetch_assoc($countStmt)['TOTAL'];
    $totalPages = ceil($total / $limit);
    oci_free_statement($countStmt);

    $sql  = "SELECT s.student_id, s.fullname, s.ic_number, s.phone, s.status,
                    g.name AS grade_name,
                    p.fullname AS parent_name, p.phone AS parent_phone
             FROM   STUDENT s
             JOIN   GRADE   g ON g.grade_id  = s.grade_id
             JOIN   PARENT  p ON p.parent_id = s.parent_id
             WHERE  $where
             ORDER  BY s.fullname
             OFFSET :offset ROWS FETCH NEXT :limit ROWS ONLY";
    $stmt = oci_parse($conn, $sql);
    foreach ($params as $k => &$v) oci_bind_by_name($stmt, $k, $v);
    oci_bind_by_name($stmt, ':offset', $offset);
    oci_bind_by_name($stmt, ':limit',  $limit);
    oci_execute($stmt);

    $students = [];
    while ($row = oci_fetch_assoc($stmt)) $students[] = $row;
    oci_free_statement($stmt);
    oci_close($conn);
} catch (\RuntimeException $e) {
    $students = []; $grades = []; $total = 0; $totalPages = 1;
}

$pageTitle = 'Students — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="ml-64 p-8 min-h-screen">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Students</h1>
            <p class="text-slate-500 text-sm mt-1">Manage student records</p>
        </div>
        <a href="/PTE-MANAGEMENT-SYSTEM/src/Students/create.php"
           class="bg-indigo-800 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 inline-flex items-center gap-2 text-sm">
            <i class="ti ti-plus"></i> Add Student
        </a>
    </div>

    <?php require_once '../../views/partials/flash.php'; ?>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4 mb-4">
        <form method="GET" class="flex gap-3 items-end flex-wrap">
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-medium text-slate-500 mb-1">Search</label>
                <input type="text" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="Name or IC number…"
                       class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Grade</label>
                <select name="grade" class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Grades</option>
                    <?php foreach ($grades as $g): ?>
                    <option value="<?= $g['GRADE_ID'] ?>" <?= $grade === (int)$g['GRADE_ID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['NAME'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
                <select name="status" class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Status</option>
                    <option value="ACTIVE"   <?= $status === 'ACTIVE'   ? 'selected' : '' ?>>Active</option>
                    <option value="INACTIVE" <?= $status === 'INACTIVE' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <button type="submit" class="bg-indigo-800 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 text-sm inline-flex items-center gap-2">
                <i class="ti ti-search"></i> Search
            </button>
            <?php if ($search !== '' || $grade > 0 || $status !== ''): ?>
            <a href="/PTE-MANAGEMENT-SYSTEM/src/Students/index.php"
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
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Name</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">IC Number</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Grade</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Parent</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Status</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                <tr>
                    <td colspan="6" class="text-center py-10 text-slate-400">
                        <i class="ti ti-school text-3xl block mb-2"></i>
                        No students found.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($students as $s): ?>
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-800">
                        <a href="/PTE-MANAGEMENT-SYSTEM/src/Students/show.php?id=<?= $s['STUDENT_ID'] ?>"
                           class="hover:text-indigo-700">
                            <?= htmlspecialchars($s['FULLNAME'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </td>
                    <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($s['IC_NUMBER'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($s['GRADE_NAME'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-slate-600">
                        <?= htmlspecialchars($s['PARENT_NAME'], ENT_QUOTES, 'UTF-8') ?>
                        <span class="block text-xs text-slate-400"><?= htmlspecialchars($s['PARENT_PHONE'], ENT_QUOTES, 'UTF-8') ?></span>
                    </td>
                    <td class="px-4 py-3">
                        <?php if ($s['STATUS'] === 'ACTIVE'): ?>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                        <?php else: ?>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="/PTE-MANAGEMENT-SYSTEM/src/Students/show.php?id=<?= $s['STUDENT_ID'] ?>"
                           class="inline-flex items-center gap-1 text-slate-500 hover:text-slate-700 text-xs font-medium mr-2">
                            <i class="ti ti-eye"></i> View
                        </a>
                        <a href="/PTE-MANAGEMENT-SYSTEM/src/Students/edit.php?id=<?= $s['STUDENT_ID'] ?>"
                           class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-xs font-medium mr-2">
                            <i class="ti ti-pencil"></i> Edit
                        </a>
                        <button onclick="confirmDelete(<?= $s['STUDENT_ID'] ?>, '<?= htmlspecialchars($s['FULLNAME'], ENT_QUOTES, 'UTF-8') ?>')"
                                class="inline-flex items-center gap-1 text-red-500 hover:text-red-700 text-xs font-medium">
                            <i class="ti ti-trash"></i> Delete
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="flex items-center justify-between mt-4 text-sm text-slate-500">
        <span>Showing <?= count($students) ?> of <?= $total ?> students</span>
        <div class="flex gap-1">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&grade=<?= $grade ?>&status=<?= urlencode($status) ?>"
               class="px-3 py-1 rounded-lg <?= $i === $page ? 'bg-indigo-800 text-white' : 'bg-white border border-slate-200 hover:bg-slate-50' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>
</main>

<div id="delete-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                <i class="ti ti-trash text-red-600 text-lg"></i>
            </div>
            <div>
                <h3 class="font-semibold text-slate-800">Delete Student</h3>
                <p class="text-sm text-slate-500">This action cannot be undone.</p>
            </div>
        </div>
        <p class="text-sm text-slate-600 mb-5">Are you sure you want to delete <strong id="delete-name"></strong>?</p>
        <form method="POST" action="/PTE-MANAGEMENT-SYSTEM/src/Students/delete.php">
            <input type="hidden" name="id" id="delete-id">
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="document.getElementById('delete-modal').classList.add('hidden')"
                        class="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm">Delete</button>
            </div>
        </form>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('delete-id').value = id;
    document.getElementById('delete-name').textContent = name;
    document.getElementById('delete-modal').classList.remove('hidden');
}
</script>

<?php require_once '../../views/layout/footer.php'; ?>
