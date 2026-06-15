<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/src/Auth/login.php');
    exit;
}
if ($_SESSION['role'] !== 'OWNER') {
    header('Location: /PTE-MANAGEMENT-SYSTEM/src/Dashboard/index.php');
    exit;
}

$errors = [];
$input  = [
    'fullname'       => '',
    'email'          => '',
    'phone'          => '',
    'role'           => '',
    'password'       => '',
    'department'     => '',
    'qualification'  => '',
    'specialisation' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input['fullname']       = trim($_POST['fullname'] ?? '');
    $input['email']          = trim($_POST['email'] ?? '');
    $input['phone']          = trim($_POST['phone'] ?? '');
    $input['role']           = $_POST['role'] ?? '';
    $input['password']       = $_POST['password'] ?? '';
    $input['department']     = trim($_POST['department'] ?? '');
    $input['qualification']  = trim($_POST['qualification'] ?? '');
    $input['specialisation'] = trim($_POST['specialisation'] ?? '');
    $confirm                 = $_POST['password_confirm'] ?? '';

    if ($input['fullname'] === '') $errors[] = 'Full name is required.';
    if ($input['email'] === '')    $errors[] = 'Email is required.';
    elseif (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';
    if (!in_array($input['role'], ['ADMIN', 'TUTOR'])) $errors[] = 'Role must be Admin or Tutor.';
    if (strlen($input['password']) < 8) $errors[] = 'Password must be at least 8 characters.';
    elseif ($input['password'] !== $confirm) $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        try {
            $conn = getConnection();

            $chkSql  = 'SELECT COUNT(*) AS cnt FROM USERS WHERE email = :email';
            $chkStmt = oci_parse($conn, $chkSql);
            oci_bind_by_name($chkStmt, ':email', $input['email']);
            oci_execute($chkStmt);
            $cnt = (int)oci_fetch_assoc($chkStmt)['CNT'];
            oci_free_statement($chkStmt);

            if ($cnt > 0) {
                $errors[] = 'That email address is already in use.';
            } else {
                $hash = password_hash($input['password'], PASSWORD_BCRYPT);

                $sql  = 'INSERT INTO USERS (fullname, email, phone, password_hash, role)
                         VALUES (:fullname, :email, :phone, :hash, :role)';
                $stmt = oci_parse($conn, $sql);
                oci_bind_by_name($stmt, ':fullname', $input['fullname']);
                oci_bind_by_name($stmt, ':email',    $input['email']);
                oci_bind_by_name($stmt, ':phone',    $input['phone']);
                oci_bind_by_name($stmt, ':hash',     $hash);
                oci_bind_by_name($stmt, ':role',     $input['role']);
                oci_execute($stmt);
                oci_free_statement($stmt);

                $newIdStmt = oci_parse($conn, 'SELECT SEQ_USER.CURRVAL AS new_id FROM DUAL');
                oci_execute($newIdStmt);
                $newId = (int)oci_fetch_assoc($newIdStmt)['NEW_ID'];
                oci_free_statement($newIdStmt);

                if ($input['role'] === 'ADMIN') {
                    $dept     = $input['department'] !== '' ? $input['department'] : null;
                    $profSql  = 'INSERT INTO ADMIN_PROFILE (user_id, department) VALUES (:uid, :dept)';
                    $profStmt = oci_parse($conn, $profSql);
                    oci_bind_by_name($profStmt, ':uid',  $newId);
                    oci_bind_by_name($profStmt, ':dept', $dept);
                } else {
                    $qual     = $input['qualification']  !== '' ? $input['qualification']  : null;
                    $spec     = $input['specialisation'] !== '' ? $input['specialisation'] : null;
                    $profSql  = 'INSERT INTO TUTOR_PROFILE (user_id, qualification, specialisation) VALUES (:uid, :qual, :spec)';
                    $profStmt = oci_parse($conn, $profSql);
                    oci_bind_by_name($profStmt, ':uid',  $newId);
                    oci_bind_by_name($profStmt, ':qual', $qual);
                    oci_bind_by_name($profStmt, ':spec', $spec);
                }
                oci_execute($profStmt);
                oci_free_statement($profStmt);

                oci_commit($conn);
                oci_close($conn);

                $_SESSION['flash_success'] = 'User "' . $input['fullname'] . '" created successfully.';
                header('Location: /PTE-MANAGEMENT-SYSTEM/src/Users/index.php');
                exit;
            }
            oci_close($conn);
        } catch (\RuntimeException $e) {
            $errors[] = 'Database error. Please try again.';
        }
    }
}

$pageTitle = 'Add User — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="ml-64 p-8 min-h-screen">
    <div class="mb-6 flex items-center gap-3">
        <a href="/PTE-MANAGEMENT-SYSTEM/src/Users/index.php" class="text-slate-400 hover:text-slate-600">
            <i class="ti ti-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Add User</h1>
            <p class="text-slate-500 text-sm mt-1">Create a new admin or tutor account</p>
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

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 max-w-xl">
        <form method="POST" action="/PTE-MANAGEMENT-SYSTEM/src/Users/create.php" novalidate>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                <input type="text" name="fullname" value="<?= htmlspecialchars($input['fullname'], ENT_QUOTES, 'UTF-8') ?>"
                       class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="e.g. Ahmad bin Ali">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="<?= htmlspecialchars($input['email'], ENT_QUOTES, 'UTF-8') ?>"
                       class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="e.g. ahmad@pte.edu.my">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($input['phone'], ENT_QUOTES, 'UTF-8') ?>"
                       class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="e.g. 0123456789">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Role <span class="text-red-500">*</span></label>
                <select name="role" id="role-select"
                        class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        onchange="toggleRoleFields(this.value)">
                    <option value="">Select role…</option>
                    <option value="ADMIN" <?= $input['role'] === 'ADMIN' ? 'selected' : '' ?>>Admin</option>
                    <option value="TUTOR" <?= $input['role'] === 'TUTOR' ? 'selected' : '' ?>>Tutor</option>
                </select>
            </div>

            <!-- Admin-only fields -->
            <div id="admin-fields" class="hidden mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Department</label>
                <input type="text" name="department" value="<?= htmlspecialchars($input['department'], ENT_QUOTES, 'UTF-8') ?>"
                       class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="e.g. Operations">
            </div>

            <!-- Tutor-only fields -->
            <div id="tutor-fields" class="hidden">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Qualification</label>
                    <input type="text" name="qualification" value="<?= htmlspecialchars($input['qualification'], ENT_QUOTES, 'UTF-8') ?>"
                           class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="e.g. B.Ed Mathematics, UPM">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Specialisation</label>
                    <input type="text" name="specialisation" value="<?= htmlspecialchars($input['specialisation'], ENT_QUOTES, 'UTF-8') ?>"
                           class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="e.g. Mathematics, Science">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Password <span class="text-red-500">*</span></label>
                <input type="password" name="password"
                       class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="At least 8 characters">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                <input type="password" name="password_confirm"
                       class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="Repeat password">
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="bg-indigo-800 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 text-sm font-medium">
                    Create User
                </button>
                <a href="/PTE-MANAGEMENT-SYSTEM/src/Users/index.php"
                   class="bg-slate-100 text-slate-600 px-5 py-2 rounded-lg hover:bg-slate-200 text-sm font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</main>

<script>
function toggleRoleFields(role) {
    document.getElementById('admin-fields').classList.toggle('hidden', role !== 'ADMIN');
    document.getElementById('tutor-fields').classList.toggle('hidden', role !== 'TUTOR');
}
// Restore state on page reload after validation error
toggleRoleFields('<?= htmlspecialchars($input['role'], ENT_QUOTES, 'UTF-8') ?>');
</script>

<?php require_once '../../views/layout/footer.php'; ?>
