<?php

$routes = [
    '/'                     => 'src/Auth/login.php',
    '/login'                => 'src/Auth/login.php',
    '/logout'               => 'src/Auth/logout.php',
    '/forgot'                => 'src/Auth/forgot.php',
    '/verify-otp'            => 'src/Auth/verify-otp.php',
    '/reset-password'        => 'src/Auth/reset-password.php',

    '/dashboard'             => 'src/Dashboard/index.php',

    '/users'                 => 'src/Users/index.php',
    '/users/create'          => 'src/Users/create.php',
    '/users/edit'            => 'src/Users/edit.php',
    '/users/delete'          => 'src/Users/delete.php',
    '/users/avatar'          => 'src/Users/avatar.php',

    '/profile'               => 'src/Users/profile.php',

    '/students'              => 'src/Students/index.php',
    '/students/create'       => 'src/Students/create.php',
    '/students/edit'         => 'src/Students/edit.php',
    '/students/delete'       => 'src/Students/delete.php',
    '/students/show'         => 'src/Students/show.php',
    '/students/enrol'        => 'src/Students/enrol.php',

    '/parents'               => 'src/Parents/index.php',
    '/parents/create'        => 'src/Parents/create.php',
    '/parents/edit'          => 'src/Parents/edit.php',
    '/parents/delete'        => 'src/Parents/delete.php',

    '/classes'               => 'src/Classes/index.php',
    '/classes/create'        => 'src/Classes/create.php',
    '/classes/edit'          => 'src/Classes/edit.php',
    '/classes/show'          => 'src/Classes/show.php',

    '/schedule/generate'     => 'src/Schedule/generate.php',

    '/sessions'              => 'src/Sessions/index.php',
    '/sessions/create'       => 'src/Sessions/create.php',
    '/sessions/show'         => 'src/Sessions/show.php',

    '/attendance'            => 'src/Attendance/index.php',
    '/attendance/take'       => 'src/Attendance/take.php',
    '/attendance/report'     => 'src/Attendance/report.php',

    '/subjects'              => 'src/Subjects/index.php',
    '/grades'                => 'src/Grades/index.php',

    '/invoices'              => 'src/Invoices/index.php',
    '/invoices/generate'     => 'src/Invoices/generate.php',
    '/invoices/show'         => 'src/Invoices/show.php',

    '/payments/record'       => 'src/Payments/record.php',
    '/payments/history'      => 'src/Payments/history.php',

    '/receipts/view'         => 'src/Receipts/view.php',
];

$basePath = '/PTE-MANAGEMENT-SYSTEM';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (str_starts_with($path, $basePath)) {
    $path = substr($path, strlen($basePath));
}
$path = rtrim($path, '/');
if ($path === '') {
    $path = '/';
}

if (!isset($routes[$path])) {
    http_response_code(404);
    echo '404 Not Found';
    exit;
}

$target    = $routes[$path];
$targetDir = __DIR__ . '/' . dirname($target);
chdir($targetDir);
require __DIR__ . '/' . $target;
