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
$input   = ['fullname' => '', 'ic_number' => '', 'phone' => '', 'status' => 'ACTIVE', 'grade_id' => '', 'parent_id' => ''];
$grades  = [];
$parents = [];

try {
    $conn = getConnection();

    $gStmt = oci_parse($conn, 'SELECT grade_id, name FROM GRADE ORDER BY grade_level');
    oci_execute($gStmt);
    while ($r = oci_fetch_assoc($gStmt)) $grades[] = $r;
    oci_free_statement($gStmt);

    $pStmt = oci_parse($conn, 'SELECT parent_id, fullname, phone FROM PARENT ORDER BY fullname');
    oci_execute($pStmt);
    while ($r = oci_fetch_assoc($pStmt)) $parents[] = $r;
    oci_free_statement($pStmt);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input['fullname']  = trim($_POST['fullname'] ?? '');
        $input['ic_number'] = trim($_POST['ic_number'] ?? '');
        $input['phone']     = trim($_POST['phone'] ?? '');
        $input['status']    = $_POST['status'] ?? 'ACTIVE';
        $input['grade_id']  = (int)($_POST['grade_id'] ?? 0);
        $input['parent_id'] = (int)($_POST['parent_id'] ?? 0);

        if ($input['fullname'] === '')  $errors[] = 'Full name is required.';
        if ($input['grade_id'] === 0)   $errors[] = 'Grade is required.';
        if ($input['parent_id'] === 0)  $errors[] = 'Parent is required.';
        if (!in_array($input['status'], ['ACTIVE', 'INACTIVE'])) $errors[] = 'Invalid status.';

        if (empty($errors) && $input['ic_number'] !== '') {
            $chkStmt = oci_parse($conn, 'SELECT COUNT(*) AS cnt FROM STUDENT WHERE ic_number = :ic');
            oci_bind_by_name($chkStmt, ':ic', $input['ic_number']);
            oci_execute($chkStmt);
            if ((int)oci_fetch_assoc($chkStmt)['CNT'] > 0) $errors[] = 'IC number already registered.';
            oci_free_statement($chkStmt);
        }

        if (empty($errors)) {
            $ic   = $input['ic_number'] !== '' ? $input['ic_number'] : null;
            $ph   = $input['phone']     !== '' ? $input['phone']     : null;

            $sql  = 'INSERT INTO STUDENT (fullname, ic_number, phone, status, grade_id, parent_id)
                     VALUES (:fullname, :ic, :phone, :status, :grade_id, :parent_id)';
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':fullname',  $input['fullname']);
            oci_bind_by_name($stmt, ':ic',        $ic);
            oci_bind_by_name($stmt, ':phone',     $ph);
            oci_bind_by_name($stmt, ':status',    $input['status']);
            oci_bind_by_name($stmt, ':grade_id',  $input['grade_id']);
            oci_bind_by_name($stmt, ':parent_id', $input['parent_id']);
            oci_execute($stmt);
            oci_commit($conn);
            oci_free_statement($stmt);
            oci_close($conn);

            $_SESSION['flash_success'] = 'Student "' . $input['fullname'] . '" added successfully.';
            header('Location: /PTE-MANAGEMENT-SYSTEM/src/Students/index.php');
            exit;
        }
    }

    oci_close($conn);
} catch (\RuntimeException $e) {
    $errors[] = 'Database error. Please try again.';
}

$pageTitle = 'Add Student — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="ml-64 p-8 min-h-screen">
    <div class="mb-6 flex items-center gap-3">
        <a href="/PTE-MANAGEMENT-SYSTEM/src/Students/index.php" class="text-slate-400 hover:text-slate-600">
            <i class="ti ti-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Add Student</h1>
            <p class="text-slate-500 text-sm mt-1">Register a new student</p>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-5 text-sm">
        <div class="flex items-center gap-2 font-medium mb-1"><i class="ti ti-alert-circle"></i> Please fix the following:</div>
        <ul class="list-disc list-inside space-y-0.5">
            <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if (empty($grades)): ?>
    <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-lg px-4 py-3 mb-5 text-sm flex items-center gap-2">
        <i class="ti ti-alert-circle"></i>
        No grades found. <a href="/PTE-MANAGEMENT-SYSTEM/src/Grades/index.php" class="underline font-medium ml-1">Add grades first.</a>
    </div>
    <?php endif; ?>

    <?php if (empty($parents)): ?>
    <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-lg px-4 py-3 mb-5 text-sm flex items-center gap-2">
        <i class="ti ti-alert-circle"></i>
        No parents found. <a href="/PTE-MANAGEMENT-SYSTEM/src/Parents/create.php" class="underline font-medium ml-1">Add a parent first.</a>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 max-w-xl">
        <form method="POST" action="/PTE-MANAGEMENT-SYSTEM/src/Students/create.php" novalidate>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                <input type="text" name="fullname" value="<?= htmlspecialchars($input['fullname'], ENT_QUOTES, 'UTF-8') ?>"
                       class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="e.g. Aina binti Ahmad">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">IC Number</label>
                <input type="text" name="ic_number" value="<?= htmlspecialchars($input['ic_number'], ENT_QUOTES, 'UTF-8') ?>"
                       class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="e.g. 100101-01-1234">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($input['phone'], ENT_QUOTES, 'UTF-8') ?>"
                       class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="e.g. 0123456789">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Grade <span class="text-red-500">*</span></label>
                <select name="grade_id" class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select grade…</option>
                    <?php foreach ($grades as $g): ?>
                    <option value="<?= $g['GRADE_ID'] ?>" <?= (int)$input['grade_id'] === (int)$g['GRADE_ID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['NAME'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Parent / Guardian <span class="text-red-500">*</span></label>
                <select name="parent_id" class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select parent…</option>
                    <?php foreach ($parents as $p): ?>
                    <option value="<?= $p['PARENT_ID'] ?>" <?= (int)$input['parent_id'] === (int)$p['PARENT_ID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['FULLNAME'] . ' (' . $p['PHONE'] . ')', ENT_QUOTES, 'UTF-8') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                <select name="status" class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="ACTIVE"   <?= $input['status'] === 'ACTIVE'   ? 'selected' : '' ?>>Active</option>
                    <option value="INACTIVE" <?= $input['status'] === 'INACTIVE' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-indigo-800 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 text-sm font-medium">
                    Add Student
                </button>
                <a href="/PTE-MANAGEMENT-SYSTEM/src/Students/index.php"
                   class="bg-slate-100 text-slate-600 px-5 py-2 rounded-lg hover:bg-slate-200 text-sm font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</main>

<?php require_once '../../views/layout/footer.php'; ?>
