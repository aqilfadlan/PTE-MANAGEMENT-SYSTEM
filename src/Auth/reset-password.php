<?php

session_start();
require_once '../../config/database.php';

if (isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/src/Dashboard/index.php');
    exit;
}

$error   = '';
$success = '';
$token   = trim($_GET['token'] ?? '');
$valid   = false;
$userId  = null;

if ($token === '') {
    header('Location: /PTE-MANAGEMENT-SYSTEM/src/Auth/forgot.php');
    exit;
}

// Validate the token on every page load
try {
    $conn = getConnection();

    $sql  = "SELECT token_id, user_id
             FROM   PASSWORD_RESET_TOKEN
             WHERE  token = :token
             AND    used = 0
             AND    expires_at > SYSTIMESTAMP";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':token', $token);
    oci_execute($stmt);
    $row = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);

    if ($row) {
        $valid  = true;
        $userId = $row['USER_ID'];
    } else {
        $error = 'This reset link is invalid or has expired.';
    }

    oci_close($conn);
} catch (\RuntimeException $e) {
    $error = 'Database error. Please try again.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid) {
    $password        = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    if (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $passwordConfirm) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $conn = getConnection();
            $hash = password_hash($password, PASSWORD_BCRYPT);

            $updSql  = 'UPDATE USERS SET password_hash = :hash WHERE user_id = :user_id';
            $updStmt = oci_parse($conn, $updSql);
            oci_bind_by_name($updStmt, ':hash',    $hash);
            oci_bind_by_name($updStmt, ':user_id', $userId);
            oci_execute($updStmt);
            oci_free_statement($updStmt);

            $markSql  = 'UPDATE PASSWORD_RESET_TOKEN SET used = 1 WHERE token = :token';
            $markStmt = oci_parse($conn, $markSql);
            oci_bind_by_name($markStmt, ':token', $token);
            oci_execute($markStmt);
            oci_free_statement($markStmt);

            oci_commit($conn);
            oci_close($conn);

            $success = 'Your password has been reset. You can now log in.';
            $valid   = false;
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
        <?php endif; ?>

        <?php if ($valid): ?>
        <form method="POST" action="/PTE-MANAGEMENT-SYSTEM/src/Auth/reset-password.php?token=<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>" novalidate>
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

        <div class="mt-6 text-center">
            <a href="/PTE-MANAGEMENT-SYSTEM/src/Auth/login.php" class="text-sm text-indigo-600 hover:text-indigo-800 inline-flex items-center gap-1">
                <i class="ti ti-arrow-left text-sm"></i> Back to login
            </a>
        </div>
    </div>
</div>

</body>
</html>
