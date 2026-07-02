<?php

session_start();
require_once '../../config/database.php';
require_once '../../config/debug.php';

if (isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/dashboard');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Email and password are required.';
    } else {
        try {
            $conn = getConnection();

            $sql  = 'SELECT u.user_id, u.fullname, u.email, u.password_hash, u.role
                     FROM   USERS u
                     WHERE  u.email = :email
                     AND    u.is_active = 1';

            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':email', $email);
            oci_execute($stmt);

            $user = oci_fetch_assoc($stmt);

            oci_free_statement($stmt);
            oci_close($conn);

            if ($user && password_verify($password, $user['PASSWORD_HASH'])) {
                session_regenerate_id(true);
                $_SESSION['user_id']  = $user['USER_ID'];
                $_SESSION['fullname'] = $user['FULLNAME'];
                $_SESSION['email']    = $user['EMAIL'];
                $_SESSION['role']     = $user['ROLE'];

                header('Location: /PTE-MANAGEMENT-SYSTEM/dashboard');
                exit;
            } else {
                $error = 'Invalid email or password.';
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
    <title>Login — PTE Management System</title>
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
        <p class="text-slate-500 text-sm mt-1">Sign in to your account</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
        <?php if ($error !== ''): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-5 flex items-center gap-2 text-sm">
                <i class="ti ti-alert-circle text-base"></i>
                <span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="/PTE-MANAGEMENT-SYSTEM/login" novalidate>
            <div class="mb-4">
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

            <div class="mb-6">
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                    <a href="/PTE-MANAGEMENT-SYSTEM/forgot" class="text-xs text-indigo-600 hover:text-indigo-800">Forgot password?</a>
                </div>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="border border-slate-300 rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                    placeholder="••••••••"
                >
            </div>

            <button type="submit"
                    class="w-full bg-indigo-800 text-white py-2.5 rounded-lg hover:bg-indigo-700 font-medium text-sm transition">
                Sign In
            </button>
        </form>
    </div>
</div>

</body>
</html>
