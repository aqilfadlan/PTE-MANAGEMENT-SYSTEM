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
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 10;
$offset = ($page - 1) * $limit;

try {
    $conn = getConnection();

    $where  = '1=1';
    $params = [];
    if ($search !== '') {
        $where .= " AND (LOWER(p.fullname) LIKE LOWER(:search) OR LOWER(p.phone) LIKE LOWER(:search2) OR LOWER(p.email) LIKE LOWER(:search3))";
        $params[':search']  = '%' . $search . '%';
        $params[':search2'] = '%' . $search . '%';
        $params[':search3'] = '%' . $search . '%';
    }

    $countSql  = "SELECT COUNT(*) AS total FROM PARENT p WHERE $where";
    $countStmt = oci_parse($conn, $countSql);
    foreach ($params as $k => &$v) oci_bind_by_name($countStmt, $k, $v);
    oci_execute($countStmt);
    $total      = (int)oci_fetch_assoc($countStmt)['TOTAL'];
    $totalPages = ceil($total / $limit);
    oci_free_statement($countStmt);

    $sql  = "SELECT p.parent_id, p.fullname, p.ic_number, p.email, p.phone,
                    (SELECT COUNT(*) FROM STUDENT s WHERE s.parent_id = p.parent_id) AS student_count
             FROM   PARENT p
             WHERE  $where
             ORDER  BY p.fullname
             OFFSET :offset ROWS FETCH NEXT :limit ROWS ONLY";
    $stmt = oci_parse($conn, $sql);
    foreach ($params as $k => &$v) oci_bind_by_name($stmt, $k, $v);
    oci_bind_by_name($stmt, ':offset', $offset);
    oci_bind_by_name($stmt, ':limit',  $limit);
    oci_execute($stmt);

    $parents = [];
    while ($row = oci_fetch_assoc($stmt)) $parents[] = $row;
    oci_free_statement($stmt);
    oci_close($conn);
} catch (\RuntimeException $e) {
    $parents    = [];
    $total      = 0;
    $totalPages = 1;
}

$pageTitle = 'Parents — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="ml-64 p-8 min-h-screen">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Parents</h1>
            <p class="text-slate-500 text-sm mt-1">Manage parent / guardian records</p>
        </div>
        <a href="/PTE-MANAGEMENT-SYSTEM/src/Parents/create.php"
           class="bg-indigo-800 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 inline-flex items-center gap-2 text-sm">
            <i class="ti ti-plus"></i> Add Parent
        </a>
    </div>

    <?php require_once '../../views/partials/flash.php'; ?>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4 mb-4">
        <form method="GET" class="flex gap-3 items-end">
            <div class="flex-1">
                <label class="block text-xs font-medium text-slate-500 mb-1">Search</label>
                <input type="text" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="Name, phone or email…"
                       class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit" class="bg-indigo-800 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 text-sm inline-flex items-center gap-2">
                <i class="ti ti-search"></i> Search
            </button>
            <?php if ($search !== ''): ?>
            <a href="/PTE-MANAGEMENT-SYSTEM/src/Parents/index.php"
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
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Phone</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Email</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Students</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($parents)): ?>
                <tr>
                    <td colspan="6" class="text-center py-10 text-slate-400">
                        <i class="ti ti-user-heart text-3xl block mb-2"></i>
                        No parents found.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($parents as $p): ?>
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-800"><?= htmlspecialchars($p['FULLNAME'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($p['IC_NUMBER'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($p['PHONE'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($p['EMAIL'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                            <?= (int)$p['STUDENT_COUNT'] ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="/PTE-MANAGEMENT-SYSTEM/src/Parents/edit.php?id=<?= $p['PARENT_ID'] ?>"
                           class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-xs font-medium mr-3">
                            <i class="ti ti-pencil"></i> Edit
                        </a>
                        <button onclick="confirmDelete(<?= $p['PARENT_ID'] ?>, '<?= htmlspecialchars($p['FULLNAME'], ENT_QUOTES, 'UTF-8') ?>')"
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
        <span>Showing <?= count($parents) ?> of <?= $total ?> parents</span>
        <div class="flex gap-1">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"
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
                <h3 class="font-semibold text-slate-800">Delete Parent</h3>
                <p class="text-sm text-slate-500">This action cannot be undone.</p>
            </div>
        </div>
        <p class="text-sm text-slate-600 mb-5">Are you sure you want to delete <strong id="delete-name"></strong>?</p>
        <form method="POST" action="/PTE-MANAGEMENT-SYSTEM/src/Parents/delete.php">
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
