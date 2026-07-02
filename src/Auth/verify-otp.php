<?php

session_start();
require_once '../../config/database.php';

if (isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/dashboard');
    exit;
}

// Must arrive here via forgot.php
if (empty($_SESSION['otp_email'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/forgot');
    exit;
}

$error = '';
$email = $_SESSION['otp_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');

    if ($otp === '' || !ctype_digit($otp) || strlen($otp) !== 6) {
        $error = 'Please enter the 6-digit code sent to your email.';
    } else {
        try {
            $conn = getConnection();

            $sql  = "SELECT t.token_id, t.user_id
                     FROM   PASSWORD_RESET_TOKEN t
                     JOIN   USERS u ON u.user_id = t.user_id
                     WHERE  u.email      = :email
                     AND    t.token      = :otp
                     AND    t.used       = 0
                     AND    t.expires_at > SYSTIMESTAMP";
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':email', $email);
            oci_bind_by_name($stmt, ':otp',   $otp);
            oci_execute($stmt);
            $row = oci_fetch_assoc($stmt);
            oci_free_statement($stmt);
            oci_close($conn);

            if ($row) {
                // OTP valid — pass user identity to reset page via session
                $_SESSION['reset_user_id'] = $row['USER_ID'];
                $_SESSION['reset_token']   = $otp;
                unset($_SESSION['otp_email']);

                header('Location: /PTE-MANAGEMENT-SYSTEM/reset-password');
                exit;
            } else {
                $error = 'Invalid or expired code. Please try again.';
            }

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
    <title>Enter Reset Code — PTE Management System</title>
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
        <p class="text-slate-500 text-sm mt-1">Enter your reset code</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">

        <?php if ($error !== ''): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-5 flex items-center gap-2 text-sm">
                <i class="ti ti-alert-circle text-base"></i>
                <span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endif; ?>

        <p class="text-sm text-slate-500 mb-6">
            We sent a 6-digit code to <span class="font-medium text-slate-700"><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></span>. Enter it below. The code expires in 15 minutes.
        </p>

        <form method="POST" action="/PTE-MANAGEMENT-SYSTEM/verify-otp" novalidate>
            <div class="mb-6">
                <label for="otp" class="block text-sm font-medium text-slate-700 mb-1">6-digit code</label>
                <input
                    type="text"
                    id="otp"
                    name="otp"
                    maxlength="6"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    required
                    autofocus
                    class="border border-slate-300 rounded-lg px-3 py-2 w-full text-center text-2xl font-bold tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="——————"
                >
            </div>

            <button type="submit"
                    class="w-full bg-indigo-800 text-white py-2.5 rounded-lg hover:bg-indigo-700 font-medium text-sm transition">
                Verify Code
            </button>
        </form>

        <div class="mt-5 text-center">
            <a href="/PTE-MANAGEMENT-SYSTEM/forgot" class="text-sm text-indigo-600 hover:text-indigo-800 inline-flex items-center gap-1">
                <i class="ti ti-refresh text-sm"></i> Resend code
            </a>
        </div>

        <div class="mt-3 text-center">
            <a href="/PTE-MANAGEMENT-SYSTEM/login" class="text-sm text-slate-400 hover:text-slate-600 inline-flex items-center gap-1">
                <i class="ti ti-arrow-left text-sm"></i> Back to login
            </a>
        </div>
    </div>
</div>

</body>
</html>
