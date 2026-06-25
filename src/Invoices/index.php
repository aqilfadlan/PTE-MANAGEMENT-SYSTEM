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

$search      = trim($_GET['search']  ?? '');
$statusFilter = $_GET['status']      ?? '';
$monthFilter = (int)($_GET['month']  ?? 0);
$yearFilter  = (int)($_GET['year']   ?? 0);
$page        = max(1, (int)($_GET['page'] ?? 1));
$limit       = 15;
$offset      = ($page - 1) * $limit;

$months = [
    1 => 'January', 2 => 'February', 3 => 'March',    4 => 'April',
    5 => 'May',     6 => 'June',     7 => 'July',      8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
];

try {
    $conn = getConnection();

    // Summary counts for quick view
    $sumSql  = "SELECT i.status, COUNT(*) AS cnt, SUM(i.total_amount) AS total_amt
                FROM   INVOICE i
                GROUP  BY i.status";
    $sumStmt = oci_parse($conn, $sumSql);
    oci_execute($sumStmt);
    $statusSummary = [];
    while ($r = oci_fetch_assoc($sumStmt)) $statusSummary[$r['STATUS']] = $r;
    oci_free_statement($sumStmt);

    // WHERE
    $where  = '1=1';
    $params = [];
    if ($search !== '') {
        $where .= ' AND LOWER(p.fullname) LIKE LOWER(:search)';
        $params[':search'] = '%' . $search . '%';
    }
    if (in_array($statusFilter, ['UNPAID', 'PARTIAL', 'PAID', 'OVERDUE'])) {
        $where .= ' AND i.status = :status';
        $params[':status'] = $statusFilter;
    }
    if ($monthFilter > 0) {
        $where .= ' AND i.billing_month = :month';
        $params[':month'] = $monthFilter;
    }
    if ($yearFilter > 0) {
        $where .= ' AND i.billing_year = :year';
        $params[':year'] = $yearFilter;
    }

    // Count
    $countSql  = "SELECT COUNT(*) AS total
                  FROM   INVOICE i
                  JOIN   PARENT  p ON p.parent_id = i.parent_id
                  WHERE  $where";
    $countStmt = oci_parse($conn, $countSql);
    foreach ($params as $k => &$v) oci_bind_by_name($countStmt, $k, $v);
    unset($v);
    oci_execute($countStmt);
    $total      = (int)oci_fetch_assoc($countStmt)['TOTAL'];
    $totalPages = (int)ceil($total / $limit);
    oci_free_statement($countStmt);

    // List
    $sql  = "SELECT i.invoice_id, i.billing_month, i.billing_year,
                    i.total_amount, i.status,
                    TO_CHAR(i.due_date,    'YYYY-MM-DD') AS due_date,
                    TO_CHAR(i.created_at,  'YYYY-MM-DD') AS created_at,
                    p.parent_id, p.fullname AS parent_name, p.phone AS parent_phone,
                    COALESCE(SUM(pay.amount_paid), 0) AS total_paid
             FROM   INVOICE i
             JOIN   PARENT  p   ON p.parent_id   = i.parent_id
             LEFT   JOIN PAYMENT pay ON pay.invoice_id = i.invoice_id
             WHERE  $where
             GROUP  BY i.invoice_id, i.billing_month, i.billing_year,
                       i.total_amount, i.status, i.due_date, i.created_at,
                       p.parent_id, p.fullname, p.phone
             ORDER  BY i.billing_year DESC, i.billing_month DESC, p.fullname
             OFFSET :offset ROWS FETCH NEXT :limit ROWS ONLY";
    $stmt = oci_parse($conn, $sql);
    foreach ($params as $k => &$v) oci_bind_by_name($stmt, $k, $v);
    unset($v);
    oci_bind_by_name($stmt, ':offset', $offset);
    oci_bind_by_name($stmt, ':limit',  $limit);
    oci_execute($stmt);
    $invoices = [];
    while ($r = oci_fetch_assoc($stmt)) $invoices[] = $r;
    oci_free_statement($stmt);
    oci_close($conn);
} catch (\RuntimeException $e) {
    $invoices = []; $total = 0; $totalPages = 1; $statusSummary = [];
}

$statusColors = [
    'UNPAID'  => 'bg-slate-100 text-slate-600',
    'PARTIAL' => 'bg-yellow-100 text-yellow-700',
    'PAID'    => 'bg-green-100 text-green-700',
    'OVERDUE' => 'bg-orange-100 text-orange-700',
];

$pageTitle = 'Invoices — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="ml-64 p-8 min-h-screen">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Invoices</h1>
            <p class="text-slate-500 text-sm mt-1">Billing and payment status</p>
        </div>
        <a href="/PTE-MANAGEMENT-SYSTEM/src/Invoices/generate.php"
           class="bg-indigo-800 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 inline-flex items-center gap-2 text-sm">
            <i class="ti ti-file-plus"></i> Generate Invoice
        </a>
    </div>

    <?php require_once '../../views/partials/flash.php'; ?>

    <!-- Status summary pills -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <?php
        $summaryDef = [
            'UNPAID'  => ['label' => 'Unpaid',  'color' => 'border-slate-300 text-slate-600'],
            'PARTIAL' => ['label' => 'Partial', 'color' => 'border-yellow-300 text-yellow-700'],
            'OVERDUE' => ['label' => 'Overdue', 'color' => 'border-orange-300 text-orange-700'],
            'PAID'    => ['label' => 'Paid',    'color' => 'border-green-300 text-green-700'],
        ];
        foreach ($summaryDef as $st => $def):
            $cnt = (int)($statusSummary[$st]['CNT'] ?? 0);
            $amt = (float)($statusSummary[$st]['TOTAL_AMT'] ?? 0);
        ?>
        <a href="?status=<?= $st ?>"
           class="bg-white rounded-lg shadow-sm border-2 <?= $def['color'] ?> p-4 hover:shadow-md transition <?= $statusFilter === $st ? 'ring-2 ring-indigo-400' : '' ?>">
            <p class="text-xs font-medium <?= $def['color'] ?> uppercase tracking-wide"><?= $def['label'] ?></p>
            <p class="text-2xl font-bold text-slate-800 mt-1"><?= $cnt ?></p>
            <p class="text-xs text-slate-400 mt-0.5">RM <?= number_format($amt, 2) ?></p>
        </a>
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
                <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
                <select name="status" class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All</option>
                    <option value="UNPAID"  <?= $statusFilter === 'UNPAID'  ? 'selected' : '' ?>>Unpaid</option>
                    <option value="PARTIAL" <?= $statusFilter === 'PARTIAL' ? 'selected' : '' ?>>Partial</option>
                    <option value="OVERDUE" <?= $statusFilter === 'OVERDUE' ? 'selected' : '' ?>>Overdue</option>
                    <option value="PAID"    <?= $statusFilter === 'PAID'    ? 'selected' : '' ?>>Paid</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Month</label>
                <select name="month" class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All</option>
                    <?php foreach ($months as $m => $label): ?>
                    <option value="<?= $m ?>" <?= $monthFilter === $m ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Year</label>
                <select name="year" class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All</option>
                    <?php foreach ([2024, 2025, 2026] as $y): ?>
                    <option value="<?= $y ?>" <?= $yearFilter === $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit"
                    class="bg-indigo-800 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 text-sm inline-flex items-center gap-2">
                <i class="ti ti-search"></i> Filter
            </button>
            <?php if ($search !== '' || $statusFilter !== '' || $monthFilter > 0 || $yearFilter > 0): ?>
            <a href="/PTE-MANAGEMENT-SYSTEM/src/Invoices/index.php"
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
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Invoice</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Parent</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Billing</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Due Date</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Amount</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Paid</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Balance</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Status</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-slate-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($invoices)): ?>
                <tr>
                    <td colspan="9" class="text-center py-10 text-slate-400">
                        <i class="ti ti-file-invoice text-3xl block mb-2"></i>
                        No invoices found.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($invoices as $inv):
                    $balance  = (float)$inv['TOTAL_AMOUNT'] - (float)$inv['TOTAL_PAID'];
                    $overdue  = $inv['STATUS'] !== 'PAID' && $inv['DUE_DATE'] < date('Y-m-d');
                ?>
                <tr class="border-b border-slate-100 hover:bg-slate-50">
                    <td class="px-4 py-3 font-mono text-xs text-slate-500">
                        <a href="/PTE-MANAGEMENT-SYSTEM/src/Invoices/show.php?id=<?= (int)$inv['INVOICE_ID'] ?>"
                           class="font-semibold text-indigo-600 hover:text-indigo-800">
                            #<?= str_pad($inv['INVOICE_ID'], 5, '0', STR_PAD_LEFT) ?>
                        </a>
                    </td>
                    <td class="px-4 py-3 font-medium text-slate-800">
                        <?= htmlspecialchars($inv['PARENT_NAME'], ENT_QUOTES, 'UTF-8') ?>
                        <span class="block text-xs text-slate-400"><?= htmlspecialchars($inv['PARENT_PHONE'], ENT_QUOTES, 'UTF-8') ?></span>
                    </td>
                    <td class="px-4 py-3 text-slate-600">
                        <?= $months[(int)$inv['BILLING_MONTH']] ?> <?= (int)$inv['BILLING_YEAR'] ?>
                    </td>
                    <td class="px-4 py-3 text-slate-600 <?= $overdue ? 'text-orange-600 font-medium' : '' ?>">
                        <?= date('d M Y', strtotime($inv['DUE_DATE'])) ?>
                        <?php if ($overdue): ?><span class="block text-xs text-orange-500">Overdue</span><?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-right text-slate-700">RM <?= number_format((float)$inv['TOTAL_AMOUNT'], 2) ?></td>
                    <td class="px-4 py-3 text-right text-green-600">RM <?= number_format((float)$inv['TOTAL_PAID'], 2) ?></td>
                    <td class="px-4 py-3 text-right <?= $balance > 0 ? 'text-red-600 font-medium' : 'text-slate-400' ?>">
                        RM <?= number_format($balance, 2) ?>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium <?= $statusColors[$inv['STATUS']] ?? 'bg-slate-100 text-slate-600' ?>">
                            <?= htmlspecialchars(ucfirst(strtolower($inv['STATUS'])), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <a href="/PTE-MANAGEMENT-SYSTEM/src/Invoices/show.php?id=<?= (int)$inv['INVOICE_ID'] ?>"
                           class="inline-flex items-center gap-1 text-slate-500 hover:text-slate-700 text-xs font-medium mr-2">
                            <i class="ti ti-eye"></i> View
                        </a>
                        <?php if ($inv['STATUS'] !== 'PAID'): ?>
                        <a href="/PTE-MANAGEMENT-SYSTEM/src/Payments/record.php?invoice_id=<?= (int)$inv['INVOICE_ID'] ?>"
                           class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-xs font-medium">
                            <i class="ti ti-cash"></i> Pay
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="flex items-center justify-between mt-4 text-sm text-slate-500">
        <span>Showing <?= count($invoices) ?> of <?= $total ?> invoices</span>
        <div class="flex gap-1">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>&month=<?= $monthFilter ?>&year=<?= $yearFilter ?>"
               class="px-3 py-1 rounded-lg <?= $i === $page ? 'bg-indigo-800 text-white' : 'bg-white border border-slate-200 hover:bg-slate-50' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>
</main>

<?php require_once '../../views/layout/footer.php'; ?>
