<?php
$currentRole = $_SESSION['role'] ?? '';
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';

function isActive(string $path): string {
    global $currentPath;
    return str_contains($currentPath, $path) ? 'bg-indigo-700' : '';
}

$sidebarHasPhoto = false;
if (isset($_SESSION['user_id'])) {
    try {
        $sbConn = getConnection();
        $sbStmt = oci_parse($sbConn, 'SELECT CASE WHEN photo IS NOT NULL THEN 1 ELSE 0 END AS HAS_PHOTO FROM USERS WHERE user_id = :id');
        oci_bind_by_name($sbStmt, ':id', $_SESSION['user_id']);
        oci_execute($sbStmt);
        $sidebarHasPhoto = (bool)(oci_fetch_assoc($sbStmt)['HAS_PHOTO'] ?? 0);
        oci_free_statement($sbStmt);
        oci_close($sbConn);
    } catch (\RuntimeException $e) {
        $sidebarHasPhoto = false;
    }
}
?>

<!-- Mobile topbar -->
<header class="md:hidden fixed top-0 left-0 right-0 h-14 bg-indigo-800 text-white flex items-center justify-between px-4 z-30">
    <span class="text-base font-bold tracking-tight">PTE Management</span>
    <button type="button" id="sidebar-toggle"
            class="w-10 h-10 -mr-2 flex items-center justify-center rounded-lg hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
            aria-controls="sidebar" aria-expanded="false" aria-label="Open navigation menu">
        <i class="ti ti-menu-2 text-xl"></i>
    </button>
</header>

<!-- Mobile backdrop -->
<div id="sidebar-backdrop" class="md:hidden fixed inset-0 bg-black/40 z-20 opacity-0 pointer-events-none"></div>

<aside id="sidebar"
       class="fixed top-0 left-0 h-screen w-64 bg-indigo-800 text-white flex flex-col z-30 -translate-x-full md:translate-x-0">
    <div class="px-6 py-5 border-b border-indigo-700 flex items-start justify-between">
        <div class="min-w-0">
            <span class="text-lg font-bold tracking-tight">PTE Management</span>
            <p class="text-xs text-indigo-300 mt-0.5 truncate"><?= htmlspecialchars($_SESSION['fullname'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            <div class="mt-3 pt-3 border-t border-indigo-700/60 flex items-center gap-1.5 text-indigo-300">
                <i class="ti ti-clock text-sm shrink-0"></i>
                <span id="sidebar-clock" class="text-xs tabular-nums"></span>
            </div>
        </div>
        <button type="button" id="sidebar-close"
                class="md:hidden w-9 h-9 -mr-1 -mt-1 flex items-center justify-center rounded-lg hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                aria-label="Close navigation menu">
            <i class="ti ti-x text-lg"></i>
        </button>
    </div>

    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5 text-sm">
        <a href="/PTE-MANAGEMENT-SYSTEM/dashboard"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-inset transition <?= isActive('/dashboard') ?>">
            <i class="ti ti-layout-dashboard text-base"></i> Dashboard
        </a>

        <?php if (in_array($currentRole, ['OWNER', 'ADMIN'])): ?>
        <p class="px-3 pt-4 pb-1 text-xs text-indigo-400 uppercase tracking-wide font-medium">People</p>

        <?php if ($currentRole === 'OWNER'): ?>
        <a href="/PTE-MANAGEMENT-SYSTEM/users"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-inset transition <?= isActive('/users') ?>">
            <i class="ti ti-users text-base"></i> Users
        </a>
        <?php endif; ?>

        <a href="/PTE-MANAGEMENT-SYSTEM/students"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-inset transition <?= isActive('/students') ?>">
            <i class="ti ti-school text-base"></i> Students
        </a>

        <a href="/PTE-MANAGEMENT-SYSTEM/parents"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-inset transition <?= isActive('/parents') ?>">
            <i class="ti ti-user-heart text-base"></i> Parents
        </a>
        <?php endif; ?>

        <p class="px-3 pt-4 pb-1 text-xs text-indigo-400 uppercase tracking-wide font-medium">Academic</p>

        <?php if (in_array($currentRole, ['OWNER', 'ADMIN'])): ?>
        <a href="/PTE-MANAGEMENT-SYSTEM/classes"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-inset transition <?= isActive('/classes') ?>">
            <i class="ti ti-books text-base"></i> Classes
        </a>
        <a href="/PTE-MANAGEMENT-SYSTEM/subjects"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-inset transition <?= isActive('/subjects') ?>">
            <i class="ti ti-book text-base"></i> Subjects
        </a>
        <a href="/PTE-MANAGEMENT-SYSTEM/grades"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-inset transition <?= isActive('/grades') ?>">
            <i class="ti ti-award text-base"></i> Grades
        </a>
        <?php endif; ?>

        <a href="/PTE-MANAGEMENT-SYSTEM/sessions"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-inset transition <?= isActive('/sessions') ?>">
            <i class="ti ti-calendar-event text-base"></i> Sessions
        </a>

        <a href="/PTE-MANAGEMENT-SYSTEM/attendance<?= $currentRole === 'TUTOR' ? '' : '/report' ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-inset transition <?= isActive('/attendance') ?>">
            <i class="ti ti-clipboard-check text-base"></i> Attendance
        </a>

        <?php if (in_array($currentRole, ['OWNER', 'ADMIN'])): ?>
        <p class="px-3 pt-4 pb-1 text-xs text-indigo-400 uppercase tracking-wide font-medium">Finance</p>

        <a href="/PTE-MANAGEMENT-SYSTEM/invoices"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-inset transition <?= isActive('/invoices') ?>">
            <i class="ti ti-file-invoice text-base"></i> Invoices
        </a>

        <a href="/PTE-MANAGEMENT-SYSTEM/payments/history"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-inset transition <?= isActive('/payments') ?>">
            <i class="ti ti-cash text-base"></i> Payments
        </a>
        <?php endif; ?>

        <!-- <?php if ($currentRole === 'OWNER'): ?>
        <p class="px-3 pt-4 pb-1 text-xs text-indigo-400 uppercase tracking-wide font-medium">System</p>

        <a href="/PTE-MANAGEMENT-SYSTEM/system-test"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-inset transition <?= isActive('/system-test') ?>">
            <i class="ti ti-heartbeat text-base"></i> System Test
        </a>
        <?php endif; ?> -->
    </nav>

    <div class="relative px-3 py-3 border-t border-indigo-700">
        <button type="button" id="user-menu-toggle" aria-haspopup="true" aria-expanded="false" aria-controls="user-menu"
                class="w-full flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-white transition text-left">
            <?php if ($sidebarHasPhoto): ?>
            <img src="/PTE-MANAGEMENT-SYSTEM/users/avatar?id=<?= (int)$_SESSION['user_id'] ?>" alt=""
                 class="w-9 h-9 rounded-full object-cover shrink-0">
            <?php else: ?>
            <div class="w-9 h-9 rounded-full bg-indigo-600 text-white flex items-center justify-center text-sm font-semibold shrink-0">
                <?= htmlspecialchars(strtoupper(substr($_SESSION['fullname'] ?? '?', 0, 1)), ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php endif; ?>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-white truncate"><?= htmlspecialchars($_SESSION['fullname'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                <p class="text-xs text-indigo-300 truncate"><?= htmlspecialchars($_SESSION['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <i class="ti ti-chevron-right text-indigo-300 text-sm shrink-0 transition-transform" id="user-menu-chevron"></i>
        </button>

        <div id="user-menu" role="menu" aria-labelledby="user-menu-toggle"
             class="hidden absolute left-3 right-3 bottom-full mb-2 bg-white rounded-lg shadow-lg border border-slate-200 overflow-hidden py-1">
            <a href="/PTE-MANAGEMENT-SYSTEM/profile" role="menuitem"
               class="flex items-center gap-2.5 px-3 py-2.5 text-sm text-slate-700 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-inset">
                <i class="ti ti-user-circle text-base text-slate-400"></i> Profile
            </a>
            <button type="button" id="logout-menu-item" role="menuitem"
                    class="w-full flex items-center gap-2.5 px-3 py-2.5 text-sm text-red-600 hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-inset">
                <i class="ti ti-logout text-base"></i> Logout
            </button>
        </div>
    </div>
</aside>

<!-- Logout confirmation modal -->
<div id="logout-modal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50" role="dialog" aria-modal="true" aria-labelledby="logout-modal-title">
    <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                <i class="ti ti-logout text-red-600 text-lg"></i>
            </div>
            <div>
                <h3 id="logout-modal-title" class="font-semibold text-slate-800">Log Out</h3>
                <p class="text-sm text-slate-500">You'll need to sign in again to continue.</p>
            </div>
        </div>
        <p class="text-sm text-slate-600 mb-5">Are you sure you want to log out?</p>
        <div class="flex gap-3 justify-end">
            <button type="button" id="logout-cancel"
                    class="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 text-sm">Cancel</button>
            <a href="/PTE-MANAGEMENT-SYSTEM/logout"
               class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-2 text-sm">Log Out</a>
        </div>
    </div>
</div>

<script>
(function () {
    var sidebar  = document.getElementById('sidebar');
    var backdrop = document.getElementById('sidebar-backdrop');
    var toggle   = document.getElementById('sidebar-toggle');
    var close    = document.getElementById('sidebar-close');

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        backdrop.classList.remove('opacity-0', 'pointer-events-none');
        toggle.setAttribute('aria-expanded', 'true');
    }

    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.add('opacity-0', 'pointer-events-none');
        toggle.setAttribute('aria-expanded', 'false');
    }

    toggle.addEventListener('click', openSidebar);
    close.addEventListener('click', closeSidebar);
    backdrop.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeSidebar();
    });

    var clockEl = document.getElementById('sidebar-clock');
    if (clockEl) {
        var dateFmt = new Intl.DateTimeFormat(undefined, { weekday: 'long', day: 'numeric', month: 'long' });
        var timeFmt = new Intl.DateTimeFormat(undefined, { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        function tick() {
            var now = new Date();
            clockEl.textContent = dateFmt.format(now) + ' · ' + timeFmt.format(now);
        }
        tick();
        setInterval(tick, 1000);
    }

    // User menu popover
    var userMenuToggle  = document.getElementById('user-menu-toggle');
    var userMenu        = document.getElementById('user-menu');
    var userMenuChevron = document.getElementById('user-menu-chevron');

    function openUserMenu() {
        userMenu.classList.remove('hidden');
        userMenuToggle.setAttribute('aria-expanded', 'true');
        userMenuChevron.style.transform = 'rotate(90deg)';
    }
    function closeUserMenu() {
        userMenu.classList.add('hidden');
        userMenuToggle.setAttribute('aria-expanded', 'false');
        userMenuChevron.style.transform = '';
    }

    if (userMenuToggle) {
        userMenuToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            if (userMenu.classList.contains('hidden')) openUserMenu();
            else closeUserMenu();
        });
        document.addEventListener('click', function (e) {
            if (!userMenu.classList.contains('hidden') && !userMenu.contains(e.target) && e.target !== userMenuToggle) {
                closeUserMenu();
            }
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeUserMenu();
        });
    }

    // Logout confirmation
    var logoutMenuItem = document.getElementById('logout-menu-item');
    var logoutModal     = document.getElementById('logout-modal');
    var logoutCancel    = document.getElementById('logout-cancel');

    function openLogoutModal() {
        closeUserMenu();
        logoutModal.classList.remove('hidden');
    }
    function closeLogoutModal() {
        logoutModal.classList.add('hidden');
    }

    if (logoutMenuItem) {
        logoutMenuItem.addEventListener('click', function (e) {
            e.stopPropagation();
            openLogoutModal();
        });
        logoutCancel.addEventListener('click', closeLogoutModal);
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeLogoutModal();
        });
    }
})();
</script>
