<?php

session_start();
require_once '../../config/database.php';
require_once '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/dashboard');
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        $error = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            $conn = getConnection();

            $sql  = 'SELECT user_id, fullname FROM USERS WHERE email = :email';
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':email', $email);
            oci_execute($stmt);
            $user = oci_fetch_assoc($stmt);
            oci_free_statement($stmt);

            if ($user) {
                $userId   = $user['USER_ID'];
                $fullname = $user['FULLNAME'];
                $otp      = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

                // Invalidate any existing unused tokens for this user
                $invSql  = 'UPDATE PASSWORD_RESET_TOKEN SET used = 1 WHERE user_id = :user_id AND used = 0';
                $invStmt = oci_parse($conn, $invSql);
                oci_bind_by_name($invStmt, ':user_id', $userId);
                oci_execute($invStmt);
                oci_free_statement($invStmt);

                // Store OTP — expires in 15 minutes (use Oracle time to avoid PHP/DB timezone mismatch)
                $insSql  = "INSERT INTO PASSWORD_RESET_TOKEN (user_id, token, expires_at)
                            VALUES (:user_id, :token, SYSDATE + (15/1440))";
                $insStmt = oci_parse($conn, $insSql);
                oci_bind_by_name($insStmt, ':user_id', $userId);
                oci_bind_by_name($insStmt, ':token',   $otp,  6, SQLT_CHR);
                oci_execute($insStmt);
                oci_commit($conn);
                oci_free_statement($insStmt);

                // Send OTP via Mailtrap
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = 'sandbox.smtp.mailtrap.io';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'd5588ee6cae646';
                $mail->Password   = 'd01949952c3a34';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 2525;

                $mail->setFrom('no-reply@pte-management.local', 'PTE Management System');
                $mail->addAddress($email, $fullname);
                $mail->isHTML(true);
                $mail->Subject = 'Your Password Reset Code';
                $mail->Body    = '
                    <div style="font-family:sans-serif;max-width:480px;margin:0 auto;">
                        <h2 style="color:#3730a3;">PTE Management System</h2>
                        <p>Hi ' . htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8') . ',</p>
                        <p>Use the code below to reset your password. It expires in <strong>15 minutes</strong>.</p>
                        <div style="font-size:36px;font-weight:bold;letter-spacing:12px;text-align:center;
                                    background:#f1f5f9;padding:24px;border-radius:8px;margin:24px 0;color:#3730a3;">
                            ' . $otp . '
                        </div>
                        <p style="color:#64748b;font-size:13px;">If you did not request this, you can safely ignore this email.</p>
                    </div>';
                $mail->AltBody = "Your password reset code is: $otp\n\nIt expires in 15 minutes.";
                $mail->send();

                // Store email in session to pass to verify page
                $_SESSION['otp_email'] = $email;

                header('Location: /PTE-MANAGEMENT-SYSTEM/verify-otp');
                exit;
            }

            oci_close($conn);

            // Don't reveal whether the email exists — always show same message
            $success = 'If that email is registered, a reset code has been sent.';

        } catch (Exception $e) {
            $error = 'Could not send email. Please try again.';
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
    <title>Forgot Password — PTE Management System</title>
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
        <p class="text-slate-500 text-sm mt-1">Reset your password</p>
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

        <?php if ($success === ''): ?>
        <p class="text-sm text-slate-500 mb-6">Enter your account email and we'll send you a 6-digit reset code.</p>

        <form method="POST" action="/PTE-MANAGEMENT-SYSTEM/forgot" novalidate>
            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    required
                    autofocus
                    class="border border-slate-300 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="you@example.com"
                >
            </div>

            <button type="submit"
                    class="w-full bg-indigo-800 text-white py-2.5 rounded-lg hover:bg-indigo-700 font-medium text-sm transition">
                Send Reset Code
            </button>
        </form>
        <?php endif; ?>

        <div class="mt-6 text-center">
            <a href="/PTE-MANAGEMENT-SYSTEM/login" class="text-sm text-indigo-600 hover:text-indigo-800 inline-flex items-center gap-1">
                <i class="ti ti-arrow-left text-sm"></i> Back to login
            </a>
        </div>
    </div>
</div>

</body>
</html>
