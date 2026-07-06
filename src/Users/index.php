<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/login');
    exit;
}
if ($_SESSION['role'] !== 'OWNER') {
    header('Location: /PTE-MANAGEMENT-SYSTEM/dashboard');
    exit;
}

$search = trim($_GET['search'] ?? '');
$role   = $_GET['role'] ?? '';
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 10;
$offset = ($page - 1) * $limit;

try {
    $conn = getConnection();

    $where  = "WHERE u.user_id != :self";
    $params = [':self' => $_SESSION['user_id']];

    if ($search !== '') {
        $where .= " AND (LOWER(u.fullname) LIKE LOWER(:search) OR LOWER(u.email) LIKE LOWER(:search2))";
        $params[':search']  = '%' . $search . '%';
        $params[':search2'] = '%' . $search . '%';
    }
    if ($role !== '') {
        $where .= " AND u.role = :role";
        $params[':role'] = $role;
    }

    $countSql  = "SELECT COUNT(*) AS total FROM USERS u $where";
    $countStmt = oci_parse($conn, $countSql);
    foreach ($params as $k => &$v) oci_bind_by_name($countStmt, $k, $v);
    unset($v);
    oci_execute($countStmt);
    $total     = (int)oci_fetch_assoc($countStmt)['TOTAL'];
    $totalPages = max(1, (int)ceil($total / $limit));
    oci_free_statement($countStmt);

    if ($page > $totalPages) {
        $page   = $totalPages;
        $offset = ($page - 1) * $limit;
    }

    $sql  = "SELECT u.user_id, u.fullname, u.email, u.phone, u.role, u.is_active, u.created_at
             FROM   USERS u
             $where
             ORDER  BY u.created_at DESC
             OFFSET :offset ROWS FETCH NEXT :limit ROWS ONLY";
    $stmt = oci_parse($conn, $sql);
    foreach ($params as $k => &$v) oci_bind_by_name($stmt, $k, $v);
    unset($v);
    oci_bind_by_name($stmt, ':offset', $offset);
    oci_bind_by_name($stmt, ':limit',  $limit);
    oci_execute($stmt);

    $users = [];
    while ($row = oci_fetch_assoc($stmt)) $users[] = $row;
    oci_free_statement($stmt);
    oci_close($conn);
} catch (\RuntimeException $e) {
    $users = [];
    $total = 0;
    $totalPages = 1;
}

$pageTitle = 'Users — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="pt-16 md:pt-10 md:ml-64 px-4 sm:px-8 pb-4 sm:pb-8 min-h-screen">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Users</h1>
            <p class="text-slate-500 text-sm mt-1">Manage admin and tutor accounts</p>
        </div>
        <a href="/PTE-MANAGEMENT-SYSTEM/users/create"
           class="bg-indigo-800 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 inline-flex items-center gap-2 text-sm">
            <i class="ti ti-plus"></i> Add User
        </a>
    </div>

    <?php require_once '../../views/partials/flash.php'; ?>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4 mb-4">
        <form method="GET" class="flex gap-3 items-end">
            <div class="flex-1">
                <label class="block text-xs font-medium text-slate-500 mb-1">Search</label>
                <input type="text" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="Name or email…"
                       class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Role</label>
                <select name="role" class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Roles</option>
                    <option value="ADMIN"  <?= $role === 'ADMIN'  ? 'selected' : '' ?>>Admin</option>
                    <option value="TUTOR"  <?= $role === 'TUTOR'  ? 'selected' : '' ?>>Tutor</option>
                </select>
            </div>
            <button type="submit" class="bg-indigo-800 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 text-sm inline-flex items-center gap-2">
                <i class="ti ti-search"></i> Search
            </button>
            <?php if ($search !== '' || $role !== ''): ?>
            <a href="/PTE-MANAGEMENT-SYSTEM/users"
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
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Email</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Phone</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Role</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Status</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                <tr>
                    <td colspan="6" class="text-center py-10 text-slate-400">
                        <i class="ti ti-users text-3xl block mb-2"></i>
                        No users found.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($users as $u): ?>
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-800">
                        <?= htmlspecialchars($u['FULLNAME'], ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($u['EMAIL'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-slate-600"><?= htmlspecialchars($u['PHONE'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3">
                        <?php
                        $roleColors = ['ADMIN' => 'bg-indigo-100 text-indigo-700', 'TUTOR' => 'bg-blue-100 text-blue-700'];
                        $rc = $roleColors[$u['ROLE']] ?? 'bg-slate-100 text-slate-600';
                        ?>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium <?= $rc ?>">
                            <?= htmlspecialchars($u['ROLE'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <?php if ($u['IS_ACTIVE'] == 1): ?>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Active</span>
                        <?php else: ?>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="/PTE-MANAGEMENT-SYSTEM/users/edit?id=<?= $u['USER_ID'] ?>"
                           class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-xs font-medium mr-3">
                            <i class="ti ti-pencil"></i> Edit
                        </a>
                        <button onclick="confirmDelete(<?= $u['USER_ID'] ?>, '<?= htmlspecialchars($u['FULLNAME'], ENT_QUOTES, 'UTF-8') ?>')"
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
        <span>Showing <?= count($users) ?> of <?= $total ?> users</span>
        <?php
            $baseParams = ['search' => $search, 'role' => $role];
            require_once '../../views/partials/pagination.php';
        ?>
    </div>
    <?php endif; ?>
</main>

<!-- Delete confirmation modal -->
<div id="delete-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                <i class="ti ti-trash text-red-600 text-lg"></i>
            </div>
            <div>
                <h3 class="font-semibold text-slate-800">Delete User</h3>
                <p class="text-sm text-slate-500">This action cannot be undone.</p>
            </div>
        </div>
        <p class="text-sm text-slate-600 mb-5">Are you sure you want to delete <strong id="delete-name"></strong>?</p>
        <form method="POST" action="/PTE-MANAGEMENT-SYSTEM/users/delete">
            <input type="hidden" name="id" id="delete-id">
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="document.getElementById('delete-modal').classList.add('hidden')"
                        class="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm">Cancel</button>
                <button type="submit"
                        class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm">Delete</button>
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
