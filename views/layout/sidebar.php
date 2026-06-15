<?php
$currentRole = $_SESSION['role'] ?? '';
$currentPath = $_SERVER['PHP_SELF'] ?? '';

function isActive(string $path): string {
    global $currentPath;
    return str_contains($currentPath, $path) ? 'bg-indigo-700' : '';
}
?>

<aside class="fixed top-0 left-0 h-screen w-64 bg-indigo-800 text-white flex flex-col z-10">
    <div class="px-6 py-5 border-b border-indigo-700">
        <span class="text-lg font-bold tracking-tight">PTE Management</span>
        <p class="text-xs text-indigo-300 mt-0.5"><?= htmlspecialchars($_SESSION['fullname'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
        <span class="inline-block mt-1 text-xs bg-indigo-600 text-indigo-100 px-2 py-0.5 rounded-full">
            <?= htmlspecialchars($currentRole, ENT_QUOTES, 'UTF-8') ?>
        </span>
    </div>

    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5 text-sm">
        <a href="/PTE-MANAGEMENT-SYSTEM/src/Dashboard/index.php"
           class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-indigo-700 transition <?= isActive('Dashboard') ?>">
            <i class="ti ti-layout-dashboard text-base"></i> Dashboard
        </a>

        <?php if (in_array($currentRole, ['OWNER', 'ADMIN'])): ?>
        <p class="px-3 pt-4 pb-1 text-xs text-indigo-400 uppercase tracking-wide font-medium">People</p>

        <?php if ($currentRole === 'OWNER'): ?>
        <a href="/PTE-MANAGEMENT-SYSTEM/src/Users/index.php"
           class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-indigo-700 transition <?= isActive('/Users/') ?>">
            <i class="ti ti-users text-base"></i> Users
        </a>
        <?php endif; ?>

        <a href="/PTE-MANAGEMENT-SYSTEM/src/Students/index.php"
           class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-indigo-700 transition <?= isActive('/Students/') ?>">
            <i class="ti ti-school text-base"></i> Students
        </a>

        <a href="/PTE-MANAGEMENT-SYSTEM/src/Parents/index.php"
           class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-indigo-700 transition <?= isActive('/Parents/') ?>">
            <i class="ti ti-user-heart text-base"></i> Parents
        </a>
        <?php endif; ?>

        <p class="px-3 pt-4 pb-1 text-xs text-indigo-400 uppercase tracking-wide font-medium">Academic</p>

        <?php if (in_array($currentRole, ['OWNER', 'ADMIN'])): ?>
        <a href="/PTE-MANAGEMENT-SYSTEM/src/Classes/index.php"
           class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-indigo-700 transition <?= isActive('/Classes/') ?>">
            <i class="ti ti-books text-base"></i> Classes
        </a>
        <a href="/PTE-MANAGEMENT-SYSTEM/src/Subjects/index.php"
           class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-indigo-700 transition <?= isActive('/Subjects/') ?>">
            <i class="ti ti-book text-base"></i> Subjects
        </a>
        <a href="/PTE-MANAGEMENT-SYSTEM/src/Grades/index.php"
           class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-indigo-700 transition <?= isActive('/Grades/') ?>">
            <i class="ti ti-award text-base"></i> Grades
        </a>
        <?php endif; ?>

        <a href="/PTE-MANAGEMENT-SYSTEM/src/Sessions/index.php"
           class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-indigo-700 transition <?= isActive('/Sessions/') ?>">
            <i class="ti ti-calendar-event text-base"></i> Sessions
        </a>

        <a href="/PTE-MANAGEMENT-SYSTEM/src/Attendance/<?= $currentRole === 'TUTOR' ? 'take' : 'report' ?>.php"
           class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-indigo-700 transition <?= isActive('/Attendance/') ?>">
            <i class="ti ti-clipboard-check text-base"></i> Attendance
        </a>

        <?php if (in_array($currentRole, ['OWNER', 'ADMIN'])): ?>
        <p class="px-3 pt-4 pb-1 text-xs text-indigo-400 uppercase tracking-wide font-medium">Finance</p>

        <a href="/PTE-MANAGEMENT-SYSTEM/src/Invoices/index.php"
           class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-indigo-700 transition <?= isActive('/Invoices/') ?>">
            <i class="ti ti-file-invoice text-base"></i> Invoices
        </a>

        <a href="/PTE-MANAGEMENT-SYSTEM/src/Payments/history.php"
           class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-indigo-700 transition <?= isActive('/Payments/') ?>">
            <i class="ti ti-cash text-base"></i> Payments
        </a>
        <?php endif; ?>
    </nav>

    <div class="px-3 py-4 border-t border-indigo-700">
        <a href="/PTE-MANAGEMENT-SYSTEM/src/Auth/logout.php"
           class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-indigo-700 transition text-sm">
            <i class="ti ti-logout text-base"></i> Logout
        </a>
    </div>
</aside>
