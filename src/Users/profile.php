<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/login');
    exit;
}

$id = (int)$_SESSION['user_id'];

$errors = [];
$input  = [];

try {
    $conn = getConnection();

    $fetchSql  = 'SELECT user_id, fullname, email, phone, role
                  FROM   USERS
                  WHERE  user_id = :id';
    $fetchStmt = oci_parse($conn, $fetchSql);
    oci_bind_by_name($fetchStmt, ':id', $id);
    oci_execute($fetchStmt);
    $user = oci_fetch_assoc($fetchStmt);
    oci_free_statement($fetchStmt);

    if (!$user) {
        oci_close($conn);
        session_destroy();
        header('Location: /PTE-MANAGEMENT-SYSTEM/login');
        exit;
    }

    $input = [
        'fullname' => $user['FULLNAME'],
        'phone'    => $user['PHONE'] ?? '',
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input['fullname']       = trim($_POST['fullname'] ?? '');
        $input['phone']          = trim($_POST['phone'] ?? '');
        $currentPassword         = $_POST['current_password'] ?? '';
        $newPassword             = $_POST['new_password'] ?? '';
        $confirmPassword         = $_POST['new_password_confirm'] ?? '';
        $wantsPasswordChange     = $newPassword !== '' || $confirmPassword !== '';
        $photoFile                = $_FILES['photo'] ?? null;
        $hasPhotoUpload           = $photoFile && $photoFile['error'] === UPLOAD_ERR_OK;

        if ($input['fullname'] === '') $errors['fullname'] = 'Full name is required.';

        if ($wantsPasswordChange) {
            if ($currentPassword === '') {
                $errors['current_password'] = 'Enter your current password to change it.';
            }
            if (strlen($newPassword) < 8) {
                $errors['new_password'] = 'New password must be at least 8 characters.';
            } elseif ($newPassword !== $confirmPassword) {
                $errors['new_password_confirm'] = 'New passwords do not match.';
            }
        }

        if ($photoFile && !$hasPhotoUpload && $photoFile['error'] !== UPLOAD_ERR_NO_FILE) {
            $errors['photo'] = 'Photo upload failed. Please try a smaller image.';
        }
        if ($hasPhotoUpload) {
            $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
            $mime = mime_content_type($photoFile['tmp_name']);
            if (!in_array($mime, $allowedMime, true)) {
                $errors['photo'] = 'Photo must be a JPEG, PNG, or WebP image.';
            } elseif ($photoFile['size'] > 2 * 1024 * 1024) {
                $errors['photo'] = 'Photo must be under 2 MB.';
            }
        }

        if (empty($errors) && $wantsPasswordChange) {
            $pwStmt = oci_parse($conn, 'SELECT password_hash FROM USERS WHERE user_id = :id');
            oci_bind_by_name($pwStmt, ':id', $id);
            oci_execute($pwStmt);
            $currentHash = oci_fetch_assoc($pwStmt)['PASSWORD_HASH'];
            oci_free_statement($pwStmt);

            if (!password_verify($currentPassword, $currentHash)) {
                $errors['current_password'] = 'Current password is incorrect.';
            }
        }

        if (empty($errors)) {
            $sets   = ['fullname = :fullname', 'phone = :phone', 'updated_at = SYSTIMESTAMP'];
            $updSql = 'UPDATE USERS SET ' . implode(', ', $sets);

            if ($wantsPasswordChange) {
                $updSql .= ', password_hash = :hash';
            }
            if ($hasPhotoUpload) {
                $updSql .= ', photo = EMPTY_BLOB()';
            }
            $updSql .= ' WHERE user_id = :id';
            if ($hasPhotoUpload) {
                $updSql .= ' RETURNING photo INTO :photo_lob';
            }

            $updStmt = oci_parse($conn, $updSql);
            oci_bind_by_name($updStmt, ':fullname', $input['fullname']);
            oci_bind_by_name($updStmt, ':phone', $input['phone']);
            if ($wantsPasswordChange) {
                $hash = password_hash($newPassword, PASSWORD_BCRYPT);
                oci_bind_by_name($updStmt, ':hash', $hash);
            }
            oci_bind_by_name($updStmt, ':id', $id);

            if ($hasPhotoUpload) {
                $photoLob = oci_new_descriptor($conn, OCI_D_LOB);
                oci_bind_by_name($updStmt, ':photo_lob', $photoLob, -1, OCI_B_BLOB);
            }

            oci_execute($updStmt, OCI_DEFAULT);

            if ($hasPhotoUpload) {
                $photoLob->save(file_get_contents($photoFile['tmp_name']));
                $mimeStmt = oci_parse($conn, 'UPDATE USERS SET photo_mime = :mime WHERE user_id = :id');
                oci_bind_by_name($mimeStmt, ':mime', $mime);
                oci_bind_by_name($mimeStmt, ':id', $id);
                oci_execute($mimeStmt);
                oci_free_statement($mimeStmt);
                $photoLob->free();
            }

            oci_commit($conn);
            oci_free_statement($updStmt);
            oci_close($conn);

            $_SESSION['fullname'] = $input['fullname'];

            $_SESSION['flash_success'] = 'Profile updated successfully.';
            header('Location: /PTE-MANAGEMENT-SYSTEM/profile');
            exit;
        }
    }

    // Check whether a photo exists, for the avatar preview
    $photoChkStmt = oci_parse($conn, 'SELECT CASE WHEN photo IS NOT NULL THEN 1 ELSE 0 END AS HAS_PHOTO FROM USERS WHERE user_id = :id');
    oci_bind_by_name($photoChkStmt, ':id', $id);
    oci_execute($photoChkStmt);
    $hasPhoto = (bool)oci_fetch_assoc($photoChkStmt)['HAS_PHOTO'];
    oci_free_statement($photoChkStmt);

    oci_close($conn);
} catch (\RuntimeException $e) {
    $errors['_general'] = 'Database error. Please try again.';
    $hasPhoto = false;
} catch (\Throwable $e) {
    $errors['_general'] = 'Something went wrong saving your profile.';
    $hasPhoto = false;
}

$pageTitle = 'My Profile — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="pt-14 md:pt-0 md:ml-64 p-4 sm:p-8 min-h-screen">
    <div class="mb-6 flex items-center gap-3">
        <a href="/PTE-MANAGEMENT-SYSTEM/dashboard" class="text-slate-400 hover:text-slate-600">
            <i class="ti ti-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-slate-800">My Profile</h1>
            <p class="text-slate-500 text-sm mt-1">Manage your account information</p>
        </div>
    </div>

    <?php require_once '../../views/partials/flash.php'; ?>

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
        <form method="POST" enctype="multipart/form-data" novalidate
              onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').innerHTML = '<i class=\'ti ti-loader-2 animate-spin\'></i> Saving…';">

            <div class="flex items-center gap-4 mb-6">
                <?php if ($hasPhoto): ?>
                <img src="/PTE-MANAGEMENT-SYSTEM/users/avatar?id=<?= $id ?>&t=<?= time() ?>" alt=""
                     class="w-16 h-16 rounded-full object-cover border border-slate-200">
                <?php else: ?>
                <div class="w-16 h-16 rounded-full bg-indigo-100 text-indigo-800 flex items-center justify-center text-xl font-semibold border border-slate-200">
                    <?= htmlspecialchars(strtoupper(substr($input['fullname'], 0, 1)), ENT_QUOTES, 'UTF-8') ?>
                </div>
                <?php endif; ?>
                <div>
                    <label class="inline-flex items-center gap-2 bg-indigo-100 text-indigo-800 px-3 py-1.5 rounded-lg hover:bg-indigo-200 focus-within:ring-2 focus-within:ring-indigo-500 text-sm font-medium cursor-pointer">
                        <i class="ti ti-camera"></i> Change Photo
                        <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" class="sr-only">
                    </label>
                    <p class="text-xs text-slate-400 mt-1">JPEG, PNG, or WebP. Max 2 MB.</p>
                    <?php if (isset($errors['photo'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['photo'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>
            </div>

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
                <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                <input type="email" value="<?= htmlspecialchars($_SESSION['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" disabled
                       class="border border-slate-200 bg-slate-50 text-slate-500 rounded-lg px-3 py-2 w-full text-sm cursor-not-allowed">
                <p class="text-xs text-slate-400 mt-1">Contact an owner to change your email address.</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($input['phone'], ENT_QUOTES, 'UTF-8') ?>"
                       class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'phone') ?>"
                       placeholder="e.g. 0123456789">
            </div>

            <hr class="border-slate-100 my-5">
            <p class="text-xs text-slate-400 mb-4">Leave password fields blank to keep your current password.</p>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Current Password</label>
                <input type="password" name="current_password"
                       class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'current_password') ?>"
                       aria-invalid="<?= isset($errors['current_password']) ? 'true' : 'false' ?>">
                <?php if (isset($errors['current_password'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['current_password'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">New Password</label>
                <input type="password" name="new_password"
                       class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'new_password') ?>"
                       aria-invalid="<?= isset($errors['new_password']) ? 'true' : 'false' ?>"
                       placeholder="At least 8 characters">
                <?php if (isset($errors['new_password'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['new_password'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Confirm New Password</label>
                <input type="password" name="new_password_confirm"
                       class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'new_password_confirm') ?>"
                       aria-invalid="<?= isset($errors['new_password_confirm']) ? 'true' : 'false' ?>"
                       placeholder="Repeat new password">
                <?php if (isset($errors['new_password_confirm'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['new_password_confirm'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="bg-indigo-800 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed text-sm font-medium inline-flex items-center gap-2">
                    Save Changes
                </button>
                <a href="/PTE-MANAGEMENT-SYSTEM/dashboard"
                   class="bg-slate-100 text-slate-600 px-5 py-2 rounded-lg hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 text-sm font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</main>

<?php require_once '../../views/layout/footer.php'; ?>
