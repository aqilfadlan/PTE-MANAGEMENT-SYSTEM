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
    header('Location: /PTE-MANAGEMENT-SYSTEM/src/Classes/index.php');
    exit;
}

$errors  = [];
$class   = null;
$subjects = [];
$grades   = [];
$tutors   = [];

try {
    $conn = getConnection();

    // Load current class
    $sql  = 'SELECT class_id, name, subject_id, grade_id, user_id, fee, max_students, status
             FROM   CLASS
             WHERE  class_id = :id';
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':id', $id);
    oci_execute($stmt);
    $class = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);

    if (!$class) {
        oci_close($conn);
        header('Location: /PTE-MANAGEMENT-SYSTEM/src/Classes/index.php');
        exit;
    }

    // Dropdowns
    $subjStmt = oci_parse($conn, 'SELECT subject_id, name, code FROM SUBJECT ORDER BY name');
    oci_execute($subjStmt);
    while ($r = oci_fetch_assoc($subjStmt)) $subjects[] = $r;
    oci_free_statement($subjStmt);

    $gradeStmt = oci_parse($conn, 'SELECT grade_id, name FROM GRADE ORDER BY grade_level');
    oci_execute($gradeStmt);
    while ($r = oci_fetch_assoc($gradeStmt)) $grades[] = $r;
    oci_free_statement($gradeStmt);

    $tutorStmt = oci_parse($conn, "SELECT user_id, fullname FROM USERS WHERE role IN ('TUTOR','ADMIN') AND is_active = 1 ORDER BY fullname");
    oci_execute($tutorStmt);
    while ($r = oci_fetch_assoc($tutorStmt)) $tutors[] = $r;
    oci_free_statement($tutorStmt);

    oci_close($conn);
} catch (\RuntimeException $e) {
    $_SESSION['flash_error'] = 'Database error.';
    header('Location: /PTE-MANAGEMENT-SYSTEM/src/Classes/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name']          ?? '');
    $subjectId   = (int)($_POST['subject_id']   ?? 0);
    $gradeId     = (int)($_POST['grade_id']     ?? 0);
    $userId      = (int)($_POST['user_id']      ?? 0);
    $fee         = trim($_POST['fee']           ?? '');
    $maxStudents = (int)($_POST['max_students'] ?? 30);
    $status      = $_POST['status'] ?? 'ACTIVE';

    if ($name === '')    $errors[] = 'Class name is required.';
    if ($subjectId <= 0) $errors[] = 'Please select a subject.';
    if ($gradeId   <= 0) $errors[] = 'Please select a grade.';
    if ($userId    <= 0) $errors[] = 'Please select a tutor.';
    if (!is_numeric($fee) || (float)$fee < 0) $errors[] = 'Fee must be a valid amount.';
    if (!in_array($status, ['ACTIVE', 'INACTIVE'])) $status = 'ACTIVE';

    if (empty($errors)) {
        try {
            $conn   = getConnection();
            $feeVal = (float)$fee;
            $sql    = 'UPDATE CLASS
                       SET name = :name, subject_id = :subject_id, grade_id = :grade_id,
                           user_id = :user_id, fee = :fee, max_students = :max_students,
                           status = :status, updated_at = SYSTIMESTAMP
                       WHERE class_id = :id';
            $stmt   = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':name',         $name);
            oci_bind_by_name($stmt, ':subject_id',   $subjectId);
            oci_bind_by_name($stmt, ':grade_id',     $gradeId);
            oci_bind_by_name($stmt, ':user_id',      $userId);
            oci_bind_by_name($stmt, ':fee',          $feeVal);
            oci_bind_by_name($stmt, ':max_students', $maxStudents);
            oci_bind_by_name($stmt, ':status',       $status);
            oci_bind_by_name($stmt, ':id',           $id);
            oci_execute($stmt);
            oci_commit($conn);
            oci_free_statement($stmt);
            oci_close($conn);
            $_SESSION['flash_success'] = 'Class updated successfully.';
            header('Location: /PTE-MANAGEMENT-SYSTEM/src/Classes/show.php?id=' . $id);
            exit;
        } catch (\RuntimeException $e) {
            $errors[] = 'Database error. Please try again.';
        }
    }

    // Overwrite $class with POST values for re-render
    $class['NAME']         = $_POST['name']          ?? $class['NAME'];
    $class['SUBJECT_ID']   = $_POST['subject_id']    ?? $class['SUBJECT_ID'];
    $class['GRADE_ID']     = $_POST['grade_id']      ?? $class['GRADE_ID'];
    $class['USER_ID']      = $_POST['user_id']       ?? $class['USER_ID'];
    $class['FEE']          = $_POST['fee']           ?? $class['FEE'];
    $class['MAX_STUDENTS'] = $_POST['max_students']  ?? $class['MAX_STUDENTS'];
    $class['STATUS']       = $_POST['status']        ?? $class['STATUS'];
}

$pageTitle = 'Edit Class — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="ml-64 p-8 min-h-screen">
    <div class="mb-6 flex items-center gap-3">
        <a href="/PTE-MANAGEMENT-SYSTEM/src/Classes/show.php?id=<?= $id ?>" class="text-slate-400 hover:text-slate-600">
            <i class="ti ti-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Edit Class</h1>
            <p class="text-slate-500 text-sm mt-1"><?= htmlspecialchars($class['NAME'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-6">
        <?php foreach ($errors as $e): ?>
        <p class="flex items-center gap-2 text-sm"><i class="ti ti-alert-circle"></i> <?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 max-w-2xl">
        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Class Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" required
                       value="<?= htmlspecialchars($class['NAME'], ENT_QUOTES, 'UTF-8') ?>"
                       class="border border-slate-300 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Subject <span class="text-red-500">*</span></label>
                    <select name="subject_id" required
                            class="border border-slate-300 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">Select subject…</option>
                        <?php foreach ($subjects as $s): ?>
                        <option value="<?= (int)$s['SUBJECT_ID'] ?>"
                                <?= (int)$class['SUBJECT_ID'] === (int)$s['SUBJECT_ID'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['NAME'], ENT_QUOTES, 'UTF-8') ?>
                            (<?= htmlspecialchars($s['CODE'], ENT_QUOTES, 'UTF-8') ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Grade <span class="text-red-500">*</span></label>
                    <select name="grade_id" required
                            class="border border-slate-300 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="">Select grade…</option>
                        <?php foreach ($grades as $g): ?>
                        <option value="<?= (int)$g['GRADE_ID'] ?>"
                                <?= (int)$class['GRADE_ID'] === (int)$g['GRADE_ID'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($g['NAME'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tutor <span class="text-red-500">*</span></label>
                <select name="user_id" required
                        class="border border-slate-300 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    <option value="">Select tutor…</option>
                    <?php foreach ($tutors as $t): ?>
                    <option value="<?= (int)$t['USER_ID'] ?>"
                            <?= (int)$class['USER_ID'] === (int)$t['USER_ID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($t['FULLNAME'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Monthly Fee (RM) <span class="text-red-500">*</span></label>
                    <input type="number" name="fee" required min="0" step="0.01"
                           value="<?= htmlspecialchars((string)$class['FEE'], ENT_QUOTES, 'UTF-8') ?>"
                           class="border border-slate-300 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Max Students</label>
                    <input type="number" name="max_students" min="1" max="100"
                           value="<?= htmlspecialchars((string)$class['MAX_STUDENTS'], ENT_QUOTES, 'UTF-8') ?>"
                           class="border border-slate-300 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                    <select name="status"
                            class="border border-slate-300 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        <option value="ACTIVE"   <?= $class['STATUS'] === 'ACTIVE'   ? 'selected' : '' ?>>Active</option>
                        <option value="INACTIVE" <?= $class['STATUS'] === 'INACTIVE' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="bg-indigo-800 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 inline-flex items-center gap-2 text-sm">
                    <i class="ti ti-device-floppy"></i> Update Class
                </button>
                <a href="/PTE-MANAGEMENT-SYSTEM/src/Classes/show.php?id=<?= $id ?>"
                   class="bg-slate-100 text-slate-600 px-5 py-2 rounded-lg hover:bg-slate-200 inline-flex items-center gap-2 text-sm">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</main>

<?php require_once '../../views/layout/footer.php'; ?>
