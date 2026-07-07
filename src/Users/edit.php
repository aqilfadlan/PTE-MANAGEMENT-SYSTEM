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

$id = (int)($_GET['id'] ?? 0);
if ($id === 0) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/users');
    exit;
}

$errors = [];
$input  = [];

try {
    $conn = getConnection();

    $fetchSql  = 'SELECT user_id, fullname, email, phone, role, is_active FROM USERS WHERE user_id = :id';
    $fetchStmt = oci_parse($conn, $fetchSql);
    oci_bind_by_name($fetchStmt, ':id', $id);
    oci_execute($fetchStmt);
    $user = oci_fetch_assoc($fetchStmt);
    oci_free_statement($fetchStmt);

    if (!$user) {
        oci_close($conn);
        $_SESSION['flash_error'] = 'User not found.';
        header('Location: /PTE-MANAGEMENT-SYSTEM/users');
        exit;
    }

    // Fetch profile fields — check both profile tables directly, since a
    // user can hold an ADMIN_PROFILE and a TUTOR_PROFILE row at once,
    // independent of their single-valued USERS.ROLE.
    $department = '';
    $qualification = '';
    $specialisation = '';

    $adminProfStmt = oci_parse($conn, 'SELECT department FROM ADMIN_PROFILE WHERE user_id = :id');
    oci_bind_by_name($adminProfStmt, ':id', $id);
    oci_execute($adminProfStmt);
    $adminProf = oci_fetch_assoc($adminProfStmt);
    oci_free_statement($adminProfStmt);
    $existingIsAdmin = (bool)$adminProf;
    $department = $adminProf['DEPARTMENT'] ?? '';

    $tutorProfStmt = oci_parse($conn, 'SELECT qualification, specialisation FROM TUTOR_PROFILE WHERE user_id = :id');
    oci_bind_by_name($tutorProfStmt, ':id', $id);
    oci_execute($tutorProfStmt);
    $tutorProf = oci_fetch_assoc($tutorProfStmt);
    oci_free_statement($tutorProfStmt);
    $existingIsTutor = (bool)$tutorProf;
    $qualification  = $tutorProf['QUALIFICATION']  ?? '';
    $specialisation = $tutorProf['SPECIALISATION'] ?? '';

    $input = [
        'fullname'       => $user['FULLNAME'],
        'email'          => $user['EMAIL'],
        'phone'          => $user['PHONE'] ?? '',
        'is_admin'       => $existingIsAdmin,
        'is_tutor'       => $existingIsTutor,
        'is_active'      => $user['IS_ACTIVE'],
        'password'       => '',
        'department'     => $department,
        'qualification'  => $qualification,
        'specialisation' => $specialisation,
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input['fullname']       = trim($_POST['fullname'] ?? '');
        $input['email']          = trim($_POST['email'] ?? '');
        $input['phone']          = trim($_POST['phone'] ?? '');
        $input['is_admin']       = isset($_POST['is_admin']);
        $input['is_tutor']       = isset($_POST['is_tutor']);
        $input['is_active']      = isset($_POST['is_active']) ? 1 : 0;
        $input['password']       = $_POST['password'] ?? '';
        $input['department']     = trim($_POST['department'] ?? '');
        $input['qualification']  = trim($_POST['qualification'] ?? '');
        $input['specialisation'] = trim($_POST['specialisation'] ?? '');
        $confirm                 = $_POST['password_confirm'] ?? '';

        if ($input['fullname'] === '') $errors['fullname'] = 'Full name is required.';
        if ($input['email'] === '')    $errors['email'] = 'Email is required.';
        elseif (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email address.';
        if (!$input['is_admin'] && !$input['is_tutor']) $errors['role'] = 'Select at least one role: Admin or Tutor.';
        if ($input['password'] !== '' && strlen($input['password']) < 8) $errors['password'] = 'Password must be at least 8 characters.';
        if ($input['password'] !== '' && $input['password'] !== $confirm) $errors['password_confirm'] = 'Passwords do not match.';

        if (empty($errors)) {
            $chkSql  = 'SELECT COUNT(*) AS cnt FROM USERS WHERE email = :email AND user_id != :id';
            $chkStmt = oci_parse($conn, $chkSql);
            oci_bind_by_name($chkStmt, ':email', $input['email']);
            oci_bind_by_name($chkStmt, ':id',    $id);
            oci_execute($chkStmt);
            $cnt = (int)oci_fetch_assoc($chkStmt)['CNT'];
            oci_free_statement($chkStmt);

            if ($cnt > 0) {
                $errors['email'] = 'That email address is already in use.';
            } else {
                // ROLE is single-valued and drives route-access guards;
                // Admin takes priority as the more privileged role when
                // both boxes are checked.
                $primaryRole = $input['is_admin'] ? 'ADMIN' : 'TUTOR';

                if ($input['password'] !== '') {
                    $hash    = password_hash($input['password'], PASSWORD_BCRYPT);
                    $updSql  = 'UPDATE USERS SET fullname = :fullname, email = :email, phone = :phone,
                                role = :role, is_active = :active, password_hash = :hash,
                                updated_at = SYSTIMESTAMP WHERE user_id = :id';
                    $updStmt = oci_parse($conn, $updSql);
                    oci_bind_by_name($updStmt, ':hash', $hash);
                } else {
                    $updSql  = 'UPDATE USERS SET fullname = :fullname, email = :email, phone = :phone,
                                role = :role, is_active = :active,
                                updated_at = SYSTIMESTAMP WHERE user_id = :id';
                    $updStmt = oci_parse($conn, $updSql);
                }
                oci_bind_by_name($updStmt, ':fullname', $input['fullname']);
                oci_bind_by_name($updStmt, ':email',    $input['email']);
                oci_bind_by_name($updStmt, ':phone',    $input['phone']);
                oci_bind_by_name($updStmt, ':role',     $primaryRole);
                oci_bind_by_name($updStmt, ':active',   $input['is_active']);
                oci_bind_by_name($updStmt, ':id',       $id);
                oci_execute($updStmt);
                oci_free_statement($updStmt);

                // Reconcile ADMIN_PROFILE: insert/update if checked, remove if unchecked
                if ($input['is_admin']) {
                    $dept = $input['department'] !== '' ? $input['department'] : null;
                    if ($existingIsAdmin) {
                        $pStmt = oci_parse($conn, 'UPDATE ADMIN_PROFILE SET department = :dept WHERE user_id = :id');
                    } else {
                        $pStmt = oci_parse($conn, 'INSERT INTO ADMIN_PROFILE (user_id, department) VALUES (:id, :dept)');
                    }
                    oci_bind_by_name($pStmt, ':dept', $dept);
                    oci_bind_by_name($pStmt, ':id',   $id);
                    oci_execute($pStmt);
                    oci_free_statement($pStmt);
                } elseif ($existingIsAdmin) {
                    $pStmt = oci_parse($conn, 'DELETE FROM ADMIN_PROFILE WHERE user_id = :id');
                    oci_bind_by_name($pStmt, ':id', $id);
                    oci_execute($pStmt);
                    oci_free_statement($pStmt);
                }

                // Reconcile TUTOR_PROFILE: insert/update if checked, remove if unchecked
                if ($input['is_tutor']) {
                    $qual = $input['qualification']  !== '' ? $input['qualification']  : null;
                    $spec = $input['specialisation'] !== '' ? $input['specialisation'] : null;
                    if ($existingIsTutor) {
                        $pStmt = oci_parse($conn, 'UPDATE TUTOR_PROFILE SET qualification = :qual, specialisation = :spec WHERE user_id = :id');
                    } else {
                        $pStmt = oci_parse($conn, 'INSERT INTO TUTOR_PROFILE (user_id, qualification, specialisation) VALUES (:id, :qual, :spec)');
                    }
                    oci_bind_by_name($pStmt, ':qual', $qual);
                    oci_bind_by_name($pStmt, ':spec', $spec);
                    oci_bind_by_name($pStmt, ':id',   $id);
                    oci_execute($pStmt);
                    oci_free_statement($pStmt);
                } elseif ($existingIsTutor) {
                    $pStmt = oci_parse($conn, 'DELETE FROM TUTOR_PROFILE WHERE user_id = :id');
                    oci_bind_by_name($pStmt, ':id', $id);
                    oci_execute($pStmt);
                    oci_free_statement($pStmt);
                }

                oci_commit($conn);
                oci_close($conn);

                $_SESSION['flash_success'] = 'User updated successfully.';
                header('Location: /PTE-MANAGEMENT-SYSTEM/users');
                exit;
            }
        }
    }

    oci_close($conn);
} catch (\RuntimeException $e) {
    $errors['_general'] = 'Database error. Please try again.';
}

$pageTitle = 'Edit User — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="pt-16 md:pt-10 md:ml-64 px-4 sm:px-8 pb-4 sm:pb-8 min-h-screen">
    <div class="mb-6 flex items-center gap-3">
        <a href="/PTE-MANAGEMENT-SYSTEM/users" class="text-slate-400 hover:text-slate-600">
            <i class="ti ti-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Edit User</h1>
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
        <form method="POST" action="/PTE-MANAGEMENT-SYSTEM/users/edit?id=<?= $id ?>" novalidate
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
                <label class="block text-sm font-medium text-slate-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="<?= htmlspecialchars($input['email'], ENT_QUOTES, 'UTF-8') ?>"
                       class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'email') ?>"
                       aria-invalid="<?= isset($errors['email']) ? 'true' : 'false' ?>">
                <?php if (isset($errors['email'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($input['phone'], ENT_QUOTES, 'UTF-8') ?>"
                       class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'phone') ?>">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Role <span class="text-red-500">*</span></label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_admin" id="is-admin-check" value="1" <?= $input['is_admin'] ? 'checked' : '' ?>
                               class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500"
                               onchange="toggleRoleFields()">
                        <span class="text-sm text-slate-700">Admin</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_tutor" id="is-tutor-check" value="1" <?= $input['is_tutor'] ? 'checked' : '' ?>
                               class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500"
                               onchange="toggleRoleFields()">
                        <span class="text-sm text-slate-700">Tutor</span>
                    </label>
                </div>
                <p class="text-xs text-slate-400 mt-1">Select one or both — a user can be both an admin and a tutor.</p>
                <?php if (isset($errors['role'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['role'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
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
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" <?= $input['is_active'] ? 'checked' : '' ?>
                           class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                    <span class="text-sm font-medium text-slate-700">Active account</span>
                </label>
            </div>

            <hr class="border-slate-100 my-5">
            <p class="text-xs text-slate-400 mb-4">Leave password fields blank to keep the current password.</p>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">New Password</label>
                <input type="password" name="password"
                       class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'password') ?>"
                       aria-invalid="<?= isset($errors['password']) ? 'true' : 'false' ?>"
                       placeholder="At least 8 characters">
                <?php if (isset($errors['password'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Confirm New Password</label>
                <input type="password" name="password_confirm"
                       class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'password_confirm') ?>"
                       aria-invalid="<?= isset($errors['password_confirm']) ? 'true' : 'false' ?>"
                       placeholder="Repeat new password">
                <?php if (isset($errors['password_confirm'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['password_confirm'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="bg-indigo-800 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed text-sm font-medium inline-flex items-center gap-2">
                    Save Changes
                </button>
                <a href="/PTE-MANAGEMENT-SYSTEM/users"
                   class="bg-slate-100 text-slate-600 px-5 py-2 rounded-lg hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 text-sm font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</main>

<script>
function toggleRoleFields() {
    var isAdmin = document.getElementById('is-admin-check').checked;
    var isTutor = document.getElementById('is-tutor-check').checked;
    document.getElementById('admin-fields').classList.toggle('hidden', !isAdmin);
    document.getElementById('tutor-fields').classList.toggle('hidden', !isTutor);
}
toggleRoleFields();
</script>

<?php require_once '../../views/layout/footer.php'; ?>
