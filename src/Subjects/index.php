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

$errors  = [];
$editing = null;

// ── POST: add or update ───────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $name   = trim($_POST['name']        ?? '');
    $code   = strtoupper(trim($_POST['code'] ?? ''));
    $desc   = trim($_POST['description'] ?? '');
    $editId = (int)($_POST['edit_id']    ?? 0);

    if ($name === '')  $errors[] = 'Subject name is required.';
    if ($code === '')  $errors[] = 'Subject code is required.';

    if (empty($errors)) {
        try {
            $conn = getConnection();

            if ($action === 'edit' && $editId > 0) {
                $sql  = 'UPDATE SUBJECT SET name = :name, code = :code, description = :desc
                         WHERE subject_id = :id';
                $stmt = oci_parse($conn, $sql);
                oci_bind_by_name($stmt, ':name', $name);
                oci_bind_by_name($stmt, ':code', $code);
                oci_bind_by_name($stmt, ':desc', $desc);
                oci_bind_by_name($stmt, ':id',   $editId);
                oci_execute($stmt);
                oci_commit($conn);
                oci_free_statement($stmt);
                $_SESSION['flash_success'] = 'Subject updated successfully.';
            } else {
                $sql  = 'INSERT INTO SUBJECT (name, code, description)
                         VALUES (:name, :code, :desc)';
                $stmt = oci_parse($conn, $sql);
                oci_bind_by_name($stmt, ':name', $name);
                oci_bind_by_name($stmt, ':code', $code);
                oci_bind_by_name($stmt, ':desc', $desc);
                oci_execute($stmt);
                oci_commit($conn);
                oci_free_statement($stmt);
                $_SESSION['flash_success'] = 'Subject added successfully.';
            }

            oci_close($conn);
            header('Location: /PTE-MANAGEMENT-SYSTEM/src/Subjects/index.php');
            exit;
        } catch (\RuntimeException $e) {
            $errors[] = 'Database error. The code may already be in use.';
        }
    }
}

// ── GET: load edit target ─────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    try {
        $conn    = getConnection();
        $sql     = 'SELECT subject_id, name, code, description FROM SUBJECT WHERE subject_id = :id';
        $stmt    = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':id', $editId);
        oci_execute($stmt);
        $editing = oci_fetch_assoc($stmt);
        oci_free_statement($stmt);
        oci_close($conn);
    } catch (\RuntimeException $e) {
        $editing = null;
    }
}

// ── DELETE ────────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $delId = (int)($_POST['delete_id'] ?? 0);
    if ($delId > 0) {
        try {
            $conn = getConnection();
            $sql  = 'DELETE FROM SUBJECT WHERE subject_id = :id';
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':id', $delId);
            oci_execute($stmt);
            oci_commit($conn);
            oci_free_statement($stmt);
            oci_close($conn);
            $_SESSION['flash_success'] = 'Subject deleted.';
        } catch (\RuntimeException $e) {
            $_SESSION['flash_error'] = 'Cannot delete — subject is in use by one or more classes.';
        }
    }
    header('Location: /PTE-MANAGEMENT-SYSTEM/src/Subjects/index.php');
    exit;
}

// ── Fetch all subjects ────────────────────────────────────────────────────────
try {
    $conn    = getConnection();
    $sql     = 'SELECT s.subject_id, s.name, s.code, s.description,
                       COUNT(c.class_id) AS class_count
                FROM   SUBJECT s
                LEFT   JOIN CLASS c ON c.subject_id = s.subject_id
                GROUP  BY s.subject_id, s.name, s.code, s.description
                ORDER  BY s.name';
    $stmt    = oci_parse($conn, $sql);
    oci_execute($stmt);
    $subjects = [];
    while ($r = oci_fetch_assoc($stmt)) $subjects[] = $r;
    oci_free_statement($stmt);
    oci_close($conn);
} catch (\RuntimeException $e) {
    $subjects = [];
}

$pageTitle = 'Subjects — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="ml-64 p-8 min-h-screen">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Subjects</h1>
            <p class="text-slate-500 text-sm mt-1">Manage subjects offered by the centre</p>
        </div>
        <button onclick="document.getElementById('subject-form-panel').classList.toggle('hidden')"
                class="bg-indigo-800 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 inline-flex items-center gap-2 text-sm">
            <i class="ti ti-plus"></i> Add Subject
        </button>
    </div>

    <?php require_once '../../views/partials/flash.php'; ?>

    <?php if (!empty($errors)): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-4">
        <?php foreach ($errors as $e): ?>
        <p class="flex items-center gap-2 text-sm"><i class="ti ti-alert-circle"></i> <?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Inline form panel -->
    <div id="subject-form-panel" class="<?= ($editing || !empty($errors)) ? '' : 'hidden' ?> bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-6">
        <h2 class="text-sm font-semibold text-slate-800 mb-4"><?= $editing ? 'Edit Subject' : 'New Subject' ?></h2>
        <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="hidden" name="action"  value="<?= $editing ? 'edit' : 'add' ?>">
            <input type="hidden" name="edit_id" value="<?= $editing ? (int)$editing['SUBJECT_ID'] : 0 ?>">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Subject Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" required
                       value="<?= htmlspecialchars($_POST['name'] ?? $editing['NAME'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="border border-slate-300 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Code <span class="text-red-500">*</span></label>
                <input type="text" name="code" required maxlength="20"
                       value="<?= htmlspecialchars($_POST['code'] ?? $editing['CODE'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="border border-slate-300 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm uppercase">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                <input type="text" name="description"
                       value="<?= htmlspecialchars($_POST['description'] ?? $editing['DESCRIPTION'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="border border-slate-300 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>
            <div class="md:col-span-3 flex gap-3">
                <button type="submit"
                        class="bg-indigo-800 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 inline-flex items-center gap-2 text-sm">
                    <i class="ti ti-device-floppy"></i> <?= $editing ? 'Update Subject' : 'Save Subject' ?>
                </button>
                <a href="/PTE-MANAGEMENT-SYSTEM/src/Subjects/index.php"
                   class="bg-slate-100 text-slate-600 px-4 py-2 rounded-lg hover:bg-slate-200 inline-flex items-center gap-2 text-sm">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Subject table -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Subject</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Code</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Description</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Classes</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($subjects)): ?>
                <tr>
                    <td colspan="5" class="text-center py-10 text-slate-400">
                        <i class="ti ti-book text-3xl block mb-2"></i>
                        No subjects yet.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($subjects as $s): ?>
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-800"><?= htmlspecialchars($s['NAME'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3">
                        <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2 py-0.5 rounded"><?= htmlspecialchars($s['CODE'], ENT_QUOTES, 'UTF-8') ?></span>
                    </td>
                    <td class="px-4 py-3 text-slate-500"><?= htmlspecialchars($s['DESCRIPTION'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-slate-600"><?= (int)$s['CLASS_COUNT'] ?></td>
                    <td class="px-4 py-3 text-right">
                        <a href="?edit=<?= (int)$s['SUBJECT_ID'] ?>"
                           class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-xs font-medium mr-3">
                            <i class="ti ti-pencil"></i> Edit
                        </a>
                        <?php if ((int)$s['CLASS_COUNT'] === 0): ?>
                        <button onclick="confirmDelete(<?= (int)$s['SUBJECT_ID'] ?>, '<?= htmlspecialchars($s['NAME'], ENT_QUOTES, 'UTF-8') ?>')"
                                class="inline-flex items-center gap-1 text-red-500 hover:text-red-700 text-xs font-medium">
                            <i class="ti ti-trash"></i> Delete
                        </button>
                        <?php else: ?>
                        <span class="text-xs text-slate-300 cursor-not-allowed" title="In use by classes">Delete</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- Delete modal -->
<div id="delete-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                <i class="ti ti-trash text-red-600 text-lg"></i>
            </div>
            <div>
                <h3 class="font-semibold text-slate-800">Delete Subject</h3>
                <p class="text-sm text-slate-500">This action cannot be undone.</p>
            </div>
        </div>
        <p class="text-sm text-slate-600 mb-5">Delete <strong id="delete-name"></strong>?</p>
        <form method="POST">
            <input type="hidden" name="action"    value="delete">
            <input type="hidden" name="delete_id" id="delete-id">
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
