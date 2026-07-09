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

$id = (int)($_GET['id'] ?? 0);
if ($id === 0) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/students');
    exit;
}

$errors  = [];
$input   = [];
$grades  = [];
$parents = [];

try {
    $conn = getConnection();

    $fetchStmt = oci_parse($conn, 'SELECT * FROM STUDENT WHERE student_id = :id');
    oci_bind_by_name($fetchStmt, ':id', $id);
    oci_execute($fetchStmt);
    $student = oci_fetch_assoc($fetchStmt);
    oci_free_statement($fetchStmt);

    if (!$student) {
        oci_close($conn);
        $_SESSION['flash_error'] = 'Student not found.';
        header('Location: /PTE-MANAGEMENT-SYSTEM/students');
        exit;
    }

    $input = [
        'fullname'  => $student['FULLNAME'],
        'ic_number' => $student['IC_NUMBER'] ?? '',
        'phone'     => $student['PHONE']     ?? '',
        'status'    => $student['STATUS'],
        'grade_id'  => $student['GRADE_ID'],
        'parent_id' => $student['PARENT_ID'],
    ];

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

        if ($input['fullname'] === '') $errors['fullname']  = 'Full name is required.';
        if ($input['grade_id'] === 0)  $errors['grade_id']  = 'Grade is required.';
        if ($input['parent_id'] === 0) $errors['parent_id'] = 'Parent is required.';

        if (empty($errors['ic_number']) && $input['ic_number'] !== '') {
            $chkStmt = oci_parse($conn, 'SELECT COUNT(*) AS cnt FROM STUDENT WHERE ic_number = :ic AND student_id != :id');
            oci_bind_by_name($chkStmt, ':ic', $input['ic_number']);
            oci_bind_by_name($chkStmt, ':id', $id);
            oci_execute($chkStmt);
            if ((int)oci_fetch_assoc($chkStmt)['CNT'] > 0) $errors['ic_number'] = 'IC number already registered to another student.';
            oci_free_statement($chkStmt);
        }

        if (empty($errors)) {
            $ic = $input['ic_number'] !== '' ? $input['ic_number'] : null;
            $ph = $input['phone']     !== '' ? $input['phone']     : null;

            $updSql  = 'UPDATE STUDENT SET fullname = :fullname, ic_number = :ic, phone = :phone,
                        status = :status, grade_id = :grade_id, parent_id = :parent_id,
                        updated_at = SYSTIMESTAMP WHERE student_id = :id';
            $updStmt = oci_parse($conn, $updSql);
            oci_bind_by_name($updStmt, ':fullname',  $input['fullname']);
            oci_bind_by_name($updStmt, ':ic',        $ic);
            oci_bind_by_name($updStmt, ':phone',     $ph);
            oci_bind_by_name($updStmt, ':status',    $input['status']);
            oci_bind_by_name($updStmt, ':grade_id',  $input['grade_id']);
            oci_bind_by_name($updStmt, ':parent_id', $input['parent_id']);
            oci_bind_by_name($updStmt, ':id',        $id);
            oci_execute($updStmt);
            oci_commit($conn);
            oci_free_statement($updStmt);
            oci_close($conn);

            $_SESSION['flash_success'] = 'Student updated successfully.';
            header('Location: /PTE-MANAGEMENT-SYSTEM/students');
            exit;
        }
    }

    oci_close($conn);
} catch (\RuntimeException $e) {
    $errors['_general'] = 'Database error. Please try again.';
}

$pageTitle = 'Edit Student — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="pt-16 md:pt-10 md:ml-64 px-4 sm:px-8 pb-4 sm:pb-8 min-h-screen">
    <div class="mb-6 flex items-center gap-3">
        <a href="/PTE-MANAGEMENT-SYSTEM/students" class="text-slate-400 hover:text-slate-600">
            <i class="ti ti-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Edit Student</h1>
            <p class="text-slate-500 text-sm mt-1"><?= htmlspecialchars($input['fullname'], ENT_QUOTES, 'UTF-8') ?></p>
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
    <?php
        function fieldRing(array $errors, string $key): string {
            return isset($errors[$key])
                ? 'border-red-400 focus:ring-2 focus:ring-red-500 focus:border-red-500'
                : 'border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500';
        }
    ?>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 max-w-xl">
        <form method="POST" action="/PTE-MANAGEMENT-SYSTEM/students/edit?id=<?= $id ?>" novalidate
              onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').innerHTML = '<i class=\'ti ti-loader-2 animate-spin\'></i> Saving…';">
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                <input type="text" name="fullname" value="<?= htmlspecialchars($input['fullname'], ENT_QUOTES, 'UTF-8') ?>"
                       class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'fullname') ?>"
                       aria-invalid="<?= isset($errors['fullname']) ? 'true' : 'false' ?>">
                <?php if (isset($errors['fullname'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['fullname'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">IC Number</label>
                <input type="text" name="ic_number" value="<?= htmlspecialchars($input['ic_number'], ENT_QUOTES, 'UTF-8') ?>"
                       class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'ic_number') ?>"
                       aria-invalid="<?= isset($errors['ic_number']) ? 'true' : 'false' ?>"
                       data-format="ic" inputmode="numeric" maxlength="14">
                <?php if (isset($errors['ic_number'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['ic_number'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($input['phone'], ENT_QUOTES, 'UTF-8') ?>"
                       class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'phone') ?>">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Grade <span class="text-red-500">*</span></label>
                <select name="grade_id" class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'grade_id') ?>"
                        aria-invalid="<?= isset($errors['grade_id']) ? 'true' : 'false' ?>">
                    <?php foreach ($grades as $g): ?>
                    <option value="<?= $g['GRADE_ID'] ?>" <?= (int)$input['grade_id'] === (int)$g['GRADE_ID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['NAME'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['grade_id'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['grade_id'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Parent / Guardian <span class="text-red-500">*</span></label>
                <select name="parent_id" data-searchable data-placeholder="Search parent by name or phone…"
                        class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'parent_id') ?>"
                        aria-invalid="<?= isset($errors['parent_id']) ? 'true' : 'false' ?>">
                    <?php foreach ($parents as $p): ?>
                    <option value="<?= $p['PARENT_ID'] ?>" <?= (int)$input['parent_id'] === (int)$p['PARENT_ID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['FULLNAME'] . ' (' . $p['PHONE'] . ')', ENT_QUOTES, 'UTF-8') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['parent_id'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['parent_id'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                <select name="status" class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="ACTIVE"   <?= $input['status'] === 'ACTIVE'   ? 'selected' : '' ?>>Active</option>
                    <option value="INACTIVE" <?= $input['status'] === 'INACTIVE' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-indigo-800 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed text-sm font-medium inline-flex items-center gap-2">
                    Save Changes
                </button>
                <a href="/PTE-MANAGEMENT-SYSTEM/students"
                   class="bg-slate-100 text-slate-600 px-5 py-2 rounded-lg hover:bg-slate-200 text-sm font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</main>

<?php require_once '../../views/layout/footer.php'; ?>
