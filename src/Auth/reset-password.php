<?php

session_start();
require_once '../../config/database.php';

if (isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/dashboard');
    exit;
}

// Must arrive here via verify-otp.php
if (empty($_SESSION['reset_user_id']) || empty($_SESSION['reset_token'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/forgot');
    exit;
}

$error   = '';
$success = '';
$userId  = $_SESSION['reset_user_id'];
$otp     = $_SESSION['reset_token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password        = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    if (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $passwordConfirm) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $conn = getConnection();

            // Re-validate the token is still valid before committing the change
            $chkSql  = "SELECT token_id FROM PASSWORD_RESET_TOKEN
                        WHERE user_id = :user_id AND token = :otp
                        AND used = 0 AND expires_at > SYSTIMESTAMP";
            $chkStmt = oci_parse($conn, $chkSql);
            oci_bind_by_name($chkStmt, ':user_id', $userId);
            oci_bind_by_name($chkStmt, ':otp',     $otp);
            oci_execute($chkStmt);
            $valid = oci_fetch_assoc($chkStmt);
            oci_free_statement($chkStmt);

            if (!$valid) {
                oci_close($conn);
                // Token expired between verify and reset pages
                unset($_SESSION['reset_user_id'], $_SESSION['reset_token']);
                header('Location: /PTE-MANAGEMENT-SYSTEM/forgot');
                exit;
            }

            $hash    = password_hash($password, PASSWORD_BCRYPT);

            $updSql  = 'UPDATE USERS SET password_hash = :hash WHERE user_id = :user_id';
            $updStmt = oci_parse($conn, $updSql);
            oci_bind_by_name($updStmt, ':hash',    $hash);
            oci_bind_by_name($updStmt, ':user_id', $userId);
            oci_execute($updStmt);
            oci_free_statement($updStmt);

            $markSql  = 'UPDATE PASSWORD_RESET_TOKEN SET used = 1
                         WHERE user_id = :user_id AND token = :otp';
            $markStmt = oci_parse($conn, $markSql);
            oci_bind_by_name($markStmt, ':user_id', $userId);
            oci_bind_by_name($markStmt, ':otp',     $otp);
            oci_execute($markStmt);
            oci_free_statement($markStmt);

            oci_commit($conn);
            oci_close($conn);

            unset($_SESSION['reset_user_id'], $_SESSION['reset_token']);
            $success = 'Your password has been reset. You can now log in.';

        } catch (\RuntimeException $e) {
            $error = 'Database error. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — PTE Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont/dist/tabler-icons.min.css">
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center">

<div class="w-full max-w-md">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-indigo-800 rounded-2xl mb-4">
            <i class="ti ti-books text-white text-2xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-slate-800">PTE Management System</h1>
        <p class="text-slate-500 text-sm mt-1">Set a new password</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">

        <?php if ($error !== ''): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-5 flex items-center gap-2 text-sm">
                <i class="ti ti-alert-circle text-base"></i>
                <span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success !== ''): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 mb-5 flex items-center gap-2 text-sm">
                <i class="ti ti-circle-check text-base"></i>
                <span><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="text-center mt-2">
                <a href="/PTE-MANAGEMENT-SYSTEM/login"
                   class="bg-indigo-800 text-white px-6 py-2.5 rounded-lg hover:bg-indigo-700 font-medium text-sm transition inline-block">
                    Go to Login
                </a>
            </div>
        <?php else: ?>
        <p class="text-sm text-slate-500 mb-6">Choose a new password for your account.</p>

        <form method="POST" action="/PTE-MANAGEMENT-SYSTEM/reset-password" novalidate>
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">New password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autofocus
                    minlength="8"
                    class="border border-slate-300 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="At least 8 characters"
                >
            </div>

            <div class="mb-6">
                <label for="password_confirm" class="block text-sm font-medium text-slate-700 mb-1">Confirm new password</label>
                <input
                    type="password"
                    id="password_confirm"
                    name="password_confirm"
                    required
                    minlength="8"
                    class="border border-slate-300 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="Repeat your new password"
                >
            </div>

            <button type="submit"
                    class="w-full bg-indigo-800 text-white py-2.5 rounded-lg hover:bg-indigo-700 font-medium text-sm transition">
                Reset Password
            </button>
        </form>
        <?php endif; ?>

        <?php if ($success === ''): ?>
        <div class="mt-6 text-center">
            <a href="/PTE-MANAGEMENT-SYSTEM/login" class="text-sm text-indigo-600 hover:text-indigo-800 inline-flex items-center gap-1">
                <i class="ti ti-arrow-left text-sm"></i> Back to login
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
