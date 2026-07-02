<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/login');
    exit;
}
if (!in_array($_SESSION['role'], ['OWNER', 'ADMIN'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/dashboard');
    exit;
}

$invoiceId = (int)($_GET['id'] ?? 0);
if ($invoiceId === 0) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/invoices');
    exit;
}

$months = [
    1 => 'January', 2 => 'February', 3 => 'March',    4 => 'April',
    5 => 'May',     6 => 'June',     7 => 'July',      8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
];

try {
    $conn = getConnection();

    // Invoice header
    $sql  = "SELECT i.invoice_id, i.billing_month, i.billing_year,
                    i.total_amount, i.status, i.notes,
                    TO_CHAR(i.due_date,   'YYYY-MM-DD') AS due_date,
                    TO_CHAR(i.created_at, 'YYYY-MM-DD') AS created_at,
                    p.parent_id, p.fullname AS parent_name,
                    p.phone AS parent_phone, p.email AS parent_email,
                    p.address AS parent_address
             FROM   INVOICE i
             JOIN   PARENT  p ON p.parent_id = i.parent_id
             WHERE  i.invoice_id = :id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':id', $invoiceId);
    oci_execute($stmt);
    $invoice = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);

    if (!$invoice) {
        oci_close($conn);
        $_SESSION['flash_error'] = 'Invoice not found.';
        header('Location: /PTE-MANAGEMENT-SYSTEM/invoices');
        exit;
    }

    // Line items — use a separate variable to avoid OCI8 bind-by-ref conflicts
    $itemId  = $invoiceId;
    $itemSql = 'SELECT ii.item_id, ii.description, ii.amount,
                       st.student_id, st.fullname AS student_name,
                       c.class_id, c.name AS class_name
                FROM   INVOICE_ITEM ii
                JOIN   STUDENT      st ON st.student_id = ii.student_id
                JOIN   CLASS        c  ON c.class_id    = ii.class_id
                WHERE  ii.invoice_id = :item_id
                ORDER  BY st.fullname, c.name';
    $itemStmt = oci_parse($conn, $itemSql);
    oci_bind_by_name($itemStmt, ':item_id', $itemId);
    if (!oci_execute($itemStmt)) {
        $e = oci_error($itemStmt);
        throw new \RuntimeException('Item query failed: ' . $e['message']);
    }
    $items = [];
    while ($r = oci_fetch_assoc($itemStmt)) $items[] = $r;
    oci_free_statement($itemStmt);

    // Payments
    $payId   = $invoiceId;
    $paySql  = "SELECT pay.payment_id, pay.amount_paid, pay.method,
                       pay.reference_no, pay.notes,
                       TO_CHAR(pay.payment_date, 'YYYY-MM-DD') AS payment_date,
                       TO_CHAR(pay.created_at,   'YYYY-MM-DD') AS created_at
                FROM   PAYMENT pay
                WHERE  pay.invoice_id = :pay_id
                ORDER  BY pay.payment_date DESC";
    $payStmt = oci_parse($conn, $paySql);
    oci_bind_by_name($payStmt, ':pay_id', $payId);
    oci_execute($payStmt);
    $payments = [];
    while ($r = oci_fetch_assoc($payStmt)) $payments[] = $r;
    oci_free_statement($payStmt);

    oci_close($conn);
} catch (\RuntimeException $e) {
    $_SESSION['flash_error'] = 'Database error: ' . $e->getMessage();
    header('Location: /PTE-MANAGEMENT-SYSTEM/invoices');
    exit;
}

$totalPaid = array_sum(array_column($payments, 'AMOUNT_PAID'));
$balance   = (float)$invoice['TOTAL_AMOUNT'] - (float)$totalPaid;

$statusColors = [
    'UNPAID'  => 'bg-slate-100 text-slate-600',
    'PARTIAL' => 'bg-yellow-100 text-yellow-700',
    'PAID'    => 'bg-green-100 text-green-700',
    'OVERDUE' => 'bg-orange-100 text-orange-700',
];

$methodLabels = [
    'CASH'          => 'Cash',
    'BANK_TRANSFER' => 'Bank Transfer',
    'ONLINE'        => 'Online',
    'CHEQUE'        => 'Cheque',
];

$pageTitle = 'Invoice #' . str_pad($invoiceId, 5, '0', STR_PAD_LEFT) . ' — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="pt-14 md:pt-0 md:ml-64 p-4 sm:p-8 min-h-screen">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="/PTE-MANAGEMENT-SYSTEM/invoices" class="text-slate-400 hover:text-slate-600">
                <i class="ti ti-arrow-left text-lg"></i>
            </a>
            <div>
                <h1 class="text-xl font-semibold text-slate-800">
                    Invoice #<?= str_pad($invoiceId, 5, '0', STR_PAD_LEFT) ?>
                </h1>
                <p class="text-slate-500 text-sm mt-1">
                    <?= $months[(int)$invoice['BILLING_MONTH']] ?> <?= (int)$invoice['BILLING_YEAR'] ?>
                    &middot; <?= htmlspecialchars($invoice['PARENT_NAME'], ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>
        </div>
        <?php if ($invoice['STATUS'] !== 'PAID'): ?>
        <a href="/PTE-MANAGEMENT-SYSTEM/payments/record?invoice_id=<?= $invoiceId ?>"
           class="bg-indigo-800 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 inline-flex items-center gap-2 text-sm">
            <i class="ti ti-cash"></i> Record Payment
        </a>
        <?php endif; ?>
    </div>

    <?php require_once '../../views/partials/flash.php'; ?>

    <!-- Summary bar -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Total</p>
            <p class="text-2xl font-bold text-slate-800">RM <?= number_format((float)$invoice['TOTAL_AMOUNT'], 2) ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Paid</p>
            <p class="text-2xl font-bold text-green-600">RM <?= number_format((float)$totalPaid, 2) ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Balance</p>
            <p class="text-2xl font-bold <?= $balance > 0 ? 'text-red-600' : 'text-slate-400' ?>">
                RM <?= number_format($balance, 2) ?>
            </p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Status</p>
            <div class="mt-2">
                <span class="px-2 py-1 rounded-full text-xs font-medium <?= $statusColors[$invoice['STATUS']] ?? 'bg-slate-100 text-slate-600' ?>">
                    <?= htmlspecialchars(ucfirst(strtolower($invoice['STATUS'])), ENT_QUOTES, 'UTF-8') ?>
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left: line items + payments -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Line items -->
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="text-sm font-semibold text-slate-800">Line Items</h2>
                </div>
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Description</th>
                            <th class="text-right px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr class="border-b border-slate-100">
                            <td class="px-4 py-3 text-slate-700"><?= htmlspecialchars($item['DESCRIPTION'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-right text-slate-700">RM <?= number_format((float)$item['AMOUNT'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-slate-50">
                        <tr>
                            <td class="px-4 py-3 font-semibold text-slate-800 text-right">Total</td>
                            <td class="px-4 py-3 text-right font-bold text-slate-800">RM <?= number_format((float)$invoice['TOTAL_AMOUNT'], 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Payment history -->
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-slate-800">Payment History</h2>
                    <?php if ($invoice['STATUS'] !== 'PAID'): ?>
                    <a href="/PTE-MANAGEMENT-SYSTEM/payments/record?invoice_id=<?= $invoiceId ?>"
                       class="text-xs text-indigo-600 hover:text-indigo-800 font-medium inline-flex items-center gap-1">
                        <i class="ti ti-plus"></i> Add Payment
                    </a>
                    <?php endif; ?>
                </div>
                <?php if (empty($payments)): ?>
                <div class="px-6 py-8 text-center text-slate-400 text-sm">No payments recorded yet.</div>
                <?php else: ?>
                <table class="w-full text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Date</th>
                            <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Method</th>
                            <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Reference</th>
                            <th class="text-right px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $pay): ?>
                        <tr class="border-b border-slate-100">
                            <td class="px-4 py-3 text-slate-700"><?= date('d M Y', strtotime($pay['PAYMENT_DATE'])) ?></td>
                            <td class="px-4 py-3 text-slate-600"><?= $methodLabels[$pay['METHOD']] ?? $pay['METHOD'] ?></td>
                            <td class="px-4 py-3 text-slate-500 font-mono text-xs"><?= htmlspecialchars($pay['REFERENCE_NO'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="px-4 py-3 text-right text-green-600 font-medium">RM <?= number_format((float)$pay['AMOUNT_PAID'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-slate-50">
                        <tr>
                            <td colspan="3" class="px-4 py-3 font-semibold text-slate-800 text-right">Total Paid</td>
                            <td class="px-4 py-3 text-right font-bold text-green-600">RM <?= number_format((float)$totalPaid, 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
                <?php endif; ?>
            </div>

        </div>

        <!-- Right: parent info + invoice meta -->
        <div class="space-y-4">
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                <h2 class="text-sm font-semibold text-slate-800 mb-4">Parent / Guardian</h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-slate-400">Name</dt>
                        <dd class="text-slate-800 font-medium"><?= htmlspecialchars($invoice['PARENT_NAME'], ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Phone</dt>
                        <dd class="text-slate-700"><?= htmlspecialchars($invoice['PARENT_PHONE'], ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <?php if (!empty($invoice['PARENT_EMAIL'])): ?>
                    <div>
                        <dt class="text-xs text-slate-400">Email</dt>
                        <dd class="text-slate-700"><?= htmlspecialchars($invoice['PARENT_EMAIL'], ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($invoice['PARENT_ADDRESS'])): ?>
                    <div>
                        <dt class="text-xs text-slate-400">Address</dt>
                        <dd class="text-slate-600 text-xs leading-relaxed"><?= htmlspecialchars($invoice['PARENT_ADDRESS'], ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <?php endif; ?>
                </dl>
                <a href="/PTE-MANAGEMENT-SYSTEM/parents/edit?id=<?= (int)$invoice['PARENT_ID'] ?>"
                   class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 mt-4">
                    <i class="ti ti-pencil"></i> Edit parent
                </a>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                <h2 class="text-sm font-semibold text-slate-800 mb-4">Invoice Details</h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-slate-400">Billing Period</dt>
                        <dd class="text-slate-800"><?= $months[(int)$invoice['BILLING_MONTH']] ?> <?= (int)$invoice['BILLING_YEAR'] ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Due Date</dt>
                        <dd class="text-slate-800 <?= ($invoice['STATUS'] !== 'PAID' && $invoice['DUE_DATE'] < date('Y-m-d')) ? 'text-orange-600 font-medium' : '' ?>">
                            <?= date('d M Y', strtotime($invoice['DUE_DATE'])) ?>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Created</dt>
                        <dd class="text-slate-600"><?= date('d M Y', strtotime($invoice['CREATED_AT'])) ?></dd>
                    </div>
                    <?php if (!empty($invoice['NOTES'])): ?>
                    <div>
                        <dt class="text-xs text-slate-400">Notes</dt>
                        <dd class="text-slate-600 text-xs"><?= htmlspecialchars($invoice['NOTES'], ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <?php endif; ?>
                </dl>
            </div>
        </div>

    </div>
</main>

<?php require_once '../../views/layout/footer.php'; ?>
