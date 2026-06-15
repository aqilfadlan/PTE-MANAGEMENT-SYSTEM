<?php

session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/src/Auth/login.php');
    exit;
}

$pageTitle = 'Dashboard — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="ml-64 p-8 min-h-screen">
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-slate-800">Dashboard</h1>
        <p class="text-slate-500 text-sm mt-1">
            Welcome back, <?= htmlspecialchars($_SESSION['fullname'], ENT_QUOTES, 'UTF-8') ?>
        </p>
    </div>

    <?php require_once '../../views/partials/flash.php'; ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="ti ti-school text-indigo-800 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wide font-medium">Students</p>
                    <p class="text-2xl font-bold text-slate-800">—</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="ti ti-books text-indigo-800 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wide font-medium">Classes</p>
                    <p class="text-2xl font-bold text-slate-800">—</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="ti ti-file-invoice text-indigo-800 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wide font-medium">Unpaid Invoices</p>
                    <p class="text-2xl font-bold text-slate-800">—</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="ti ti-users text-indigo-800 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wide font-medium">Tutors</p>
                    <p class="text-2xl font-bold text-slate-800">—</p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../views/layout/footer.php'; ?>
