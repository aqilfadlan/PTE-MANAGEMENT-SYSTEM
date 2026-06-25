<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/src/Auth/login.php');
    exit;
}
if (!in_array($_SESSION['role'], ['OWNER', 'ADMIN'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/src/Dashboard/index.php');
    exit;
}

$search    = trim($_GET['search']    ?? '');
$method    = $_GET['method']         ?? '';
$dateFrom  = trim($_GET['date_from'] ?? '');
$dateTo    = trim($_GET['date_to']   ?? '');
$page      = max(1, (int)($_GET['page'] ?? 1));
$limit     = 20;
$offset    = ($page - 1) * $limit;

$months = [
    1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
    5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
    9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
];

$methodLabels = [
    'CASH'          => 'Cash',
    'BANK_TRANSFER' => 'Bank Transfer',
    'ONLINE'        => 'Online',
    'CHEQUE'        => 'Cheque',
];

try {
    $conn = getConnection();

    // Total received (all time, matching filters)
    $where  = '1=1';
    $params = [];

    if ($search !== '') {
        $where .= ' AND LOWER(p.fullname) LIKE LOWER(:search)';
        $params[':search'] = '%' . $search . '%';
    }
    if (in_array($method, ['CASH', 'BANK_TRANSFER', 'ONLINE', 'CHEQUE'])) {
        $where .= ' AND pay.method = :method';
        $params[':method'] = $method;
    }
    if ($dateFrom !== '') {
        $where .= " AND pay.payment_date >= TO_DATE(:date_from, 'YYYY-MM-DD')";
        $params[':date_from'] = $dateFrom;
    }
    if ($dateTo !== '') {
        $where .= " AND pay.payment_date <= TO_DATE(:date_to, 'YYYY-MM-DD')";
        $params[':date_to'] = $dateTo;
    }

    // Summary totals by method
    $sumSql  = "SELECT pay.method, COUNT(*) AS cnt, SUM(pay.amount_paid) AS total
                FROM   PAYMENT pay
                JOIN   INVOICE i ON i.invoice_id = pay.invoice_id
                JOIN   PARENT  p ON p.parent_id  = i.parent_id
                WHERE  $where
                GROUP  BY pay.method";
    $sumStmt = oci_parse($conn, $sumSql);
    foreach ($params as $k => &$v) oci_bind_by_name($sumStmt, $k, $v);
    unset($v);
    oci_execute($sumStmt);
    $methodSummary = [];
    $grandTotal    = 0.0;
    while ($r = oci_fetch_assoc($sumStmt)) {
        $methodSummary[$r['METHOD']] = $r;
        $grandTotal += (float)$r['TOTAL'];
    }
    oci_free_statement($sumStmt);

    // Count
    $countSql  = "SELECT COUNT(*) AS total
                  FROM   PAYMENT pay
                  JOIN   INVOICE i ON i.invoice_id = pay.invoice_id
                  JOIN   PARENT  p ON p.parent_id  = i.parent_id
                  WHERE  $where";
    $countStmt = oci_parse($conn, $countSql);
    foreach ($params as $k => &$v) oci_bind_by_name($countStmt, $k, $v);
    unset($v);
    oci_execute($countStmt);
    $total      = (int)oci_fetch_assoc($countStmt)['TOTAL'];
    $totalPages = (int)ceil($total / $limit);
    oci_free_statement($countStmt);

    // Records
    $sql  = "SELECT pay.payment_id, pay.amount_paid, pay.method,
                    pay.reference_no, pay.notes,
                    TO_CHAR(pay.payment_date, 'YYYY-MM-DD') AS payment_date,
                    i.invoice_id, i.billing_month, i.billing_year, i.status AS invoice_status,
                    p.parent_id, p.fullname AS parent_name, p.phone AS parent_phone
             FROM   PAYMENT pay
             JOIN   INVOICE i ON i.invoice_id = pay.invoice_id
             JOIN   PARENT  p ON p.parent_id  = i.parent_id
             WHERE  $where
             ORDER  BY pay.payment_date DESC, pay.payment_id DESC
             OFFSET :offset ROWS FETCH NEXT :limit ROWS ONLY";
    $stmt = oci_parse($conn, $sql);
    foreach ($params as $k => &$v) oci_bind_by_name($stmt, $k, $v);
    unset($v);
    oci_bind_by_name($stmt, ':offset', $offset);
    oci_bind_by_name($stmt, ':limit',  $limit);
    oci_execute($stmt);
    $payments = [];
    while ($r = oci_fetch_assoc($stmt)) $payments[] = $r;
    oci_free_statement($stmt);
    oci_close($conn);
} catch (\RuntimeException $e) {
    $payments = []; $total = 0; $totalPages = 1;
    $methodSummary = []; $grandTotal = 0.0;
}

$invoiceStatusColors = [
    'UNPAID'  => 'bg-slate-100 text-slate-600',
    'PARTIAL' => 'bg-yellow-100 text-yellow-700',
    'PAID'    => 'bg-green-100 text-green-700',
    'OVERDUE' => 'bg-orange-100 text-orange-700',
];

$pageTitle = 'Payment History — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="ml-64 p-8 min-h-screen">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Payment History</h1>
            <p class="text-slate-500 text-sm mt-1">All recorded payments</p>
        </div>
    </div>

    <?php require_once '../../views/partials/flash.php'; ?>

    <!-- Summary cards -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-6">
        <div class="sm:col-span-1 bg-white rounded-lg shadow-sm border border-slate-200 p-5">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Total Received</p>
            <p class="text-xl font-bold text-indigo-800">RM <?= number_format($grandTotal, 2) ?></p>
            <p class="text-xs text-slate-400 mt-1"><?= $total ?> transactions</p>
        </div>
        <?php foreach ($methodLabels as $mkey => $mlabel): ?>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1"><?= $mlabel ?></p>
            <p class="text-lg font-bold text-slate-800">
                RM <?= number_format((float)($methodSummary[$mkey]['TOTAL'] ?? 0), 2) ?>
            </p>
            <p class="text-xs text-slate-400 mt-1"><?= (int)($methodSummary[$mkey]['CNT'] ?? 0) ?> txn</p>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4 mb-4">
        <form method="GET" class="flex gap-3 items-end flex-wrap">
            <div class="flex-1 min-w-40">
                <label class="block text-xs font-medium text-slate-500 mb-1">Parent</label>
                <input type="text" name="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="Parent name…"
                       class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Method</label>
                <select name="method"
                        class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Methods</option>
                    <?php foreach ($methodLabels as $mval => $mlabel): ?>
                    <option value="<?= $mval ?>" <?= $method === $mval ? 'selected' : '' ?>><?= $mlabel ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">From</label>
                <input type="date" name="date_from" value="<?= htmlspecialchars($dateFrom, ENT_QUOTES, 'UTF-8') ?>"
                       class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">To</label>
                <input type="date" name="date_to" value="<?= htmlspecialchars($dateTo, ENT_QUOTES, 'UTF-8') ?>"
                       class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit"
                    class="bg-indigo-800 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 text-sm inline-flex items-center gap-2">
                <i class="ti ti-search"></i> Filter
            </button>
            <?php if ($search !== '' || $method !== '' || $dateFrom !== '' || $dateTo !== ''): ?>
            <a href="/PTE-MANAGEMENT-SYSTEM/src/Payments/history.php"
               class="bg-slate-100 text-slate-600 px-4 py-2 rounded-lg hover:bg-slate-200 text-sm inline-flex items-center gap-2">
                <i class="ti ti-x"></i> Clear
            </a>
            <?php endif; ?>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Date</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Parent</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Invoice</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Billing</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Method</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Reference</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Amount</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Invoice Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($payments)): ?>
                <tr>
                    <td colspan="8" class="text-center py-10 text-slate-400">
                        <i class="ti ti-cash text-3xl block mb-2"></i>
                        No payments found.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($payments as $pay): ?>
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="px-4 py-3 text-slate-800 whitespace-nowrap">
                        <?= date('d M Y', strtotime($pay['PAYMENT_DATE'])) ?>
                    </td>
                    <td class="px-4 py-3 font-medium text-slate-800">
                        <?= htmlspecialchars($pay['PARENT_NAME'], ENT_QUOTES, 'UTF-8') ?>
                        <span class="block text-xs text-slate-400"><?= htmlspecialchars($pay['PARENT_PHONE'], ENT_QUOTES, 'UTF-8') ?></span>
                    </td>
                    <td class="px-4 py-3">
                        <a href="/PTE-MANAGEMENT-SYSTEM/src/Invoices/show.php?id=<?= (int)$pay['INVOICE_ID'] ?>"
                           class="font-mono text-xs text-indigo-600 hover:text-indigo-800 font-semibold">
                            #<?= str_pad($pay['INVOICE_ID'], 5, '0', STR_PAD_LEFT) ?>
                        </a>
                    </td>
                    <td class="px-4 py-3 text-slate-600">
                        <?= $months[(int)$pay['BILLING_MONTH']] ?> <?= (int)$pay['BILLING_YEAR'] ?>
                    </td>
                    <td class="px-4 py-3 text-slate-600"><?= $methodLabels[$pay['METHOD']] ?? $pay['METHOD'] ?></td>
                    <td class="px-4 py-3 text-slate-500 font-mono text-xs">
                        <?= htmlspecialchars($pay['REFERENCE_NO'] ?? '—', ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-green-600">
                        RM <?= number_format((float)$pay['AMOUNT_PAID'], 2) ?>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium <?= $invoiceStatusColors[$pay['INVOICE_STATUS']] ?? 'bg-slate-100 text-slate-600' ?>">
                            <?= htmlspecialchars(ucfirst(strtolower($pay['INVOICE_STATUS'])), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="flex items-center justify-between mt-4 text-sm text-slate-500">
        <span>Showing <?= count($payments) ?> of <?= $total ?> payments</span>
        <div class="flex gap-1">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&method=<?= urlencode($method) ?>&date_from=<?= urlencode($dateFrom) ?>&date_to=<?= urlencode($dateTo) ?>"
               class="px-3 py-1 rounded-lg <?= $i === $page ? 'bg-indigo-800 text-white' : 'bg-white border border-slate-200 hover:bg-slate-50' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>
</main>

<?php require_once '../../views/layout/footer.php'; ?>
