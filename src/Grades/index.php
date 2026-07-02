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

$errors  = [];
$editing = null;

// ── POST: add or update ───────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $name   = trim($_POST['name']        ?? '');
    $level  = (int)($_POST['grade_level'] ?? 0);
    $desc   = trim($_POST['description'] ?? '');
    $editId = (int)($_POST['edit_id']    ?? 0);

    if ($name === '')  $errors['name'] = 'Grade name is required.';
    if ($level <= 0)   $errors['grade_level'] = 'Grade level must be a positive number.';

    if (empty($errors)) {
        try {
            $conn = getConnection();

            if ($action === 'edit' && $editId > 0) {
                $sql  = 'UPDATE GRADE SET name = :name, grade_level = :level, description = :desc
                         WHERE grade_id = :id';
                $stmt = oci_parse($conn, $sql);
                oci_bind_by_name($stmt, ':name',  $name);
                oci_bind_by_name($stmt, ':level', $level);
                oci_bind_by_name($stmt, ':desc',  $desc);
                oci_bind_by_name($stmt, ':id',    $editId);
                oci_execute($stmt);
                oci_commit($conn);
                oci_free_statement($stmt);
                $_SESSION['flash_success'] = 'Grade updated successfully.';
            } else {
                $sql  = 'INSERT INTO GRADE (name, grade_level, description)
                         VALUES (:name, :level, :desc)';
                $stmt = oci_parse($conn, $sql);
                oci_bind_by_name($stmt, ':name',  $name);
                oci_bind_by_name($stmt, ':level', $level);
                oci_bind_by_name($stmt, ':desc',  $desc);
                oci_execute($stmt);
                oci_commit($conn);
                oci_free_statement($stmt);
                $_SESSION['flash_success'] = 'Grade added successfully.';
            }

            oci_close($conn);
            header('Location: /PTE-MANAGEMENT-SYSTEM/grades');
            exit;
        } catch (\RuntimeException $e) {
            $errors['_general'] = 'Database error. Please try again.';
        }
    }
}

// ── DELETE ────────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $delId = (int)($_POST['delete_id'] ?? 0);
    if ($delId > 0) {
        try {
            $conn = getConnection();
            $sql  = 'DELETE FROM GRADE WHERE grade_id = :id';
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':id', $delId);
            oci_execute($stmt);
            oci_commit($conn);
            oci_free_statement($stmt);
            oci_close($conn);
            $_SESSION['flash_success'] = 'Grade deleted.';
        } catch (\RuntimeException $e) {
            $_SESSION['flash_error'] = 'Cannot delete — grade is assigned to students or classes.';
        }
    }
    header('Location: /PTE-MANAGEMENT-SYSTEM/grades');
    exit;
}

// ── GET: load edit target ─────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    try {
        $conn    = getConnection();
        $sql     = 'SELECT grade_id, name, grade_level, description FROM GRADE WHERE grade_id = :id';
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

// ── Fetch all grades ──────────────────────────────────────────────────────────
try {
    $conn   = getConnection();
    $sql    = 'SELECT g.grade_id, g.name, g.grade_level, g.description,
                      (SELECT COUNT(*) FROM STUDENT s WHERE s.grade_id = g.grade_id) AS student_count,
                      (SELECT COUNT(*) FROM CLASS   c WHERE c.grade_id = g.grade_id) AS class_count
               FROM   GRADE g
               ORDER  BY g.grade_level';
    $stmt   = oci_parse($conn, $sql);
    oci_execute($stmt);
    $grades = [];
    while ($r = oci_fetch_assoc($stmt)) $grades[] = $r;
    oci_free_statement($stmt);
    oci_close($conn);
} catch (\RuntimeException $e) {
    $grades = [];
}

$pageTitle = 'Grades — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="pt-14 md:pt-0 md:ml-64 p-4 sm:p-8 min-h-screen">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Grades</h1>
            <p class="text-slate-500 text-sm mt-1">Manage grade levels offered by the centre</p>
        </div>
        <button onclick="document.getElementById('grade-form-panel').classList.toggle('hidden')"
                class="bg-indigo-800 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 inline-flex items-center gap-2 text-sm">
            <i class="ti ti-plus"></i> Add Grade
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
    <?php
        function fieldRing(array $errors, string $key): string {
            return isset($errors[$key])
                ? 'border-red-400 focus:ring-2 focus:ring-red-500 focus:border-red-500'
                : 'border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500';
        }
    ?>

    <!-- Inline form panel -->
    <div id="grade-form-panel" class="<?= ($editing || !empty($errors)) ? '' : 'hidden' ?> bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-6">
        <h2 class="text-sm font-semibold text-slate-800 mb-4"><?= $editing ? 'Edit Grade' : 'New Grade' ?></h2>
        <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4"
              onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').innerHTML = '<i class=\'ti ti-loader-2 animate-spin\'></i> Saving…';">
            <input type="hidden" name="action"  value="<?= $editing ? 'edit' : 'add' ?>">
            <input type="hidden" name="edit_id" value="<?= $editing ? (int)$editing['GRADE_ID'] : 0 ?>">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Grade Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" required
                       value="<?= htmlspecialchars($_POST['name'] ?? $editing['NAME'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="e.g. Darjah 1"
                       aria-invalid="<?= isset($errors['name']) ? 'true' : 'false' ?>"
                       class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'name') ?>">
                <?php if (isset($errors['name'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Grade Level <span class="text-red-500">*</span></label>
                <input type="number" name="grade_level" required min="1" max="20"
                       value="<?= htmlspecialchars((string)($_POST['grade_level'] ?? $editing['GRADE_LEVEL'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="1"
                       aria-invalid="<?= isset($errors['grade_level']) ? 'true' : 'false' ?>"
                       class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'grade_level') ?>">
                <?php if (isset($errors['grade_level'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['grade_level'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                <input type="text" name="description"
                       value="<?= htmlspecialchars($_POST['description'] ?? $editing['DESCRIPTION'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'description') ?>">
            </div>
            <div class="md:col-span-3 flex gap-3">
                <button type="submit"
                        class="bg-indigo-800 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center gap-2 text-sm">
                    <i class="ti ti-device-floppy"></i> <?= $editing ? 'Update Grade' : 'Save Grade' ?>
                </button>
                <a href="/PTE-MANAGEMENT-SYSTEM/grades"
                   class="bg-slate-100 text-slate-600 px-4 py-2 rounded-lg hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 inline-flex items-center gap-2 text-sm">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Grades table -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Level</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Grade Name</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Description</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Students</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Classes</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($grades)): ?>
                <tr>
                    <td colspan="6" class="text-center py-10 text-slate-400">
                        <i class="ti ti-award text-3xl block mb-2"></i>
                        No grades yet.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($grades as $g): ?>
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="px-4 py-3">
                        <span class="bg-indigo-100 text-indigo-800 text-xs font-bold px-2 py-0.5 rounded"><?= (int)$g['GRADE_LEVEL'] ?></span>
                    </td>
                    <td class="px-4 py-3 font-medium text-slate-800"><?= htmlspecialchars($g['NAME'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-slate-500"><?= htmlspecialchars($g['DESCRIPTION'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="px-4 py-3 text-slate-600"><?= (int)$g['STUDENT_COUNT'] ?></td>
                    <td class="px-4 py-3 text-slate-600"><?= (int)$g['CLASS_COUNT'] ?></td>
                    <td class="px-4 py-3 text-right">
                        <a href="?edit=<?= (int)$g['GRADE_ID'] ?>"
                           class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 rounded px-1.5 py-1 -mx-1.5 text-xs font-medium mr-2">
                            <i class="ti ti-pencil"></i> Edit
                        </a>
                        <?php if ((int)$g['STUDENT_COUNT'] === 0 && (int)$g['CLASS_COUNT'] === 0): ?>
                        <button onclick="confirmDelete(<?= (int)$g['GRADE_ID'] ?>, '<?= htmlspecialchars($g['NAME'], ENT_QUOTES, 'UTF-8') ?>')"
                                class="inline-flex items-center gap-1 text-red-500 hover:text-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 rounded px-1.5 py-1 -mx-1.5 text-xs font-medium">
                            <i class="ti ti-trash"></i> Delete
                        </button>
                        <?php else: ?>
                        <span class="text-xs text-slate-300 cursor-not-allowed" title="In use">Delete</span>
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
<div id="delete-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50" role="dialog" aria-modal="true" aria-labelledby="delete-modal-title">
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                <i class="ti ti-trash text-red-600 text-lg"></i>
            </div>
            <div>
                <h3 id="delete-modal-title" class="font-semibold text-slate-800">Delete Grade</h3>
                <p class="text-sm text-slate-500">This action cannot be undone.</p>
            </div>
        </div>
        <p class="text-sm text-slate-600 mb-5">Delete <strong id="delete-name"></strong>?</p>
        <form method="POST">
            <input type="hidden" name="action"    value="delete">
            <input type="hidden" name="delete_id" id="delete-id">
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="document.getElementById('delete-modal').classList.add('hidden')"
                        class="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 text-sm">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2 text-sm">Delete</button>
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
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.getElementById('delete-modal').classList.add('hidden');
    }
});
</script>

<?php require_once '../../views/layout/footer.php'; ?>
