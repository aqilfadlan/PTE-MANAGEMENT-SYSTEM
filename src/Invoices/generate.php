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

$errors  = [];
$preview = [];

$months = [
    1 => 'January', 2 => 'February', 3 => 'March',    4 => 'April',
    5 => 'May',     6 => 'June',     7 => 'July',      8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $billingMonth = (int)($_POST['billing_month'] ?? 0);
    $billingYear  = (int)($_POST['billing_year']  ?? 0);
    $dueDay       = (int)($_POST['due_day']       ?? 15);
    $parentId     = (int)($_POST['parent_id']     ?? 0);  // 0 = all parents
    $doGenerate   = isset($_POST['confirm']);

    if ($billingMonth < 1 || $billingMonth > 12) $errors[] = 'Select a valid billing month.';
    if ($billingYear  < 2024 || $billingYear > 2030) $errors[] = 'Select a valid billing year.';
    if ($dueDay < 1 || $dueDay > 28) $dueDay = 15;

    if (empty($errors)) {
        try {
            $conn = getConnection();

            // Build due date string
            $dueDate = sprintf('%04d-%02d-%02d', $billingYear, $billingMonth, $dueDay);

            // Find all parents who have active enrolled students in active classes
            // Exclude parents who already have an invoice for this month/year
            $parentWhere = $parentId > 0 ? 'AND p.parent_id = :parent_id' : '';

            $pSql  = "SELECT p.parent_id, p.fullname
                      FROM   PARENT p
                      WHERE  p.parent_id IN (
                                 SELECT s.parent_id
                                 FROM   STUDENT      s
                                 JOIN   CLASS_STUDENT cs ON cs.student_id = s.student_id
                                 JOIN   CLASS         c  ON c.class_id    = cs.class_id
                                 WHERE  s.status    = 'ACTIVE'
                                 AND    c.status    = 'ACTIVE'
                                 AND    s.parent_id IS NOT NULL
                             )
                      AND    p.parent_id NOT IN (
                                 SELECT i.parent_id
                                 FROM   INVOICE i
                                 WHERE  i.billing_month = :billing_month
                                 AND    i.billing_year  = :billing_year
                                 AND    i.parent_id IS NOT NULL
                             )
                      $parentWhere
                      ORDER  BY p.fullname";
            $pStmt = oci_parse($conn, $pSql);
            oci_bind_by_name($pStmt, ':billing_month', $billingMonth);
            oci_bind_by_name($pStmt, ':billing_year',  $billingYear);
            if ($parentId > 0) oci_bind_by_name($pStmt, ':parent_id', $parentId);
            oci_execute($pStmt);
            $eligibleParents = [];
            while ($r = oci_fetch_assoc($pStmt)) $eligibleParents[] = $r;
            oci_free_statement($pStmt);

            if (empty($eligibleParents)) {
                $errors[] = 'No eligible parents found. All parents may already have invoices for this month, or no active enrolments exist.';
            } else {
                // For each parent, gather line items (one per student per class)
                foreach ($eligibleParents as &$par) {
                    $pid = (int)$par['PARENT_ID'];

                    $itemSql  = "SELECT s.student_id, s.fullname AS student_name,
                                        c.class_id, c.name AS class_name, c.fee,
                                        sub.name AS subject_name
                                 FROM   STUDENT      s
                                 JOIN   CLASS_STUDENT cs  ON cs.student_id = s.student_id
                                 JOIN   CLASS         c   ON c.class_id    = cs.class_id
                                 JOIN   SUBJECT       sub ON sub.subject_id = c.subject_id
                                 WHERE  s.parent_id = :pid
                                 AND    s.status    = 'ACTIVE'
                                 AND    c.status    = 'ACTIVE'
                                 ORDER  BY s.fullname, c.name";
                    $itemStmt = oci_parse($conn, $itemSql);
                    oci_bind_by_name($itemStmt, ':pid', $pid);
                    oci_execute($itemStmt);
                    $items = [];
                    $total = 0.0;
                    while ($row = oci_fetch_assoc($itemStmt)) {
                        $items[] = $row;
                        $total  += (float)$row['FEE'];
                    }
                    oci_free_statement($itemStmt);
                    $par['items'] = $items;
                    $par['total'] = $total;
                }
                unset($par);

                if (!$doGenerate) {
                    $preview = $eligibleParents;
                } else {
                    // Generate invoices
                    $created = 0;
                    foreach ($eligibleParents as $par) {
                        if (empty($par['items'])) continue;

                        $pid   = (int)$par['PARENT_ID'];
                        $total = $par['total'];

                        // Insert invoice
                        $invSql  = "INSERT INTO INVOICE
                                        (parent_id, billing_month, billing_year, total_amount, status, due_date)
                                    VALUES
                                        (:parent_id, :billing_month, :billing_year, :total_amount,
                                         'UNPAID', TO_DATE(:due_date, 'YYYY-MM-DD'))";
                        $invStmt = oci_parse($conn, $invSql);
                        oci_bind_by_name($invStmt, ':parent_id',     $pid);
                        oci_bind_by_name($invStmt, ':billing_month', $billingMonth);
                        oci_bind_by_name($invStmt, ':billing_year',  $billingYear);
                        oci_bind_by_name($invStmt, ':total_amount',  $total);
                        oci_bind_by_name($invStmt, ':due_date',      $dueDate);
                        oci_execute($invStmt);
                        oci_free_statement($invStmt);

                        // Get the new invoice_id via unique key (parent + billing month/year)
                        $idSql  = 'SELECT invoice_id FROM INVOICE
                                   WHERE parent_id     = :pid
                                   AND   billing_month = :billing_month
                                   AND   billing_year  = :billing_year';
                        $idStmt = oci_parse($conn, $idSql);
                        oci_bind_by_name($idStmt, ':pid',           $pid);
                        oci_bind_by_name($idStmt, ':billing_month', $billingMonth);
                        oci_bind_by_name($idStmt, ':billing_year',  $billingYear);
                        oci_execute($idStmt);
                        $invoiceId = (int)oci_fetch_assoc($idStmt)['INVOICE_ID'];
                        oci_free_statement($idStmt);

                        // Insert line items
                        foreach ($par['items'] as $item) {
                            $desc    = htmlspecialchars(
                                $item['STUDENT_NAME'] . ' — ' . $item['CLASS_NAME'] . ' (' . $months[$billingMonth] . ' ' . $billingYear . ')',
                                ENT_QUOTES, 'UTF-8'
                            );
                            $fee     = (float)$item['FEE'];
                            $stuId   = (int)$item['STUDENT_ID'];
                            $classId = (int)$item['CLASS_ID'];

                            $itmSql  = 'INSERT INTO INVOICE_ITEM
                                            (invoice_id, student_id, class_id, description, amount)
                                        VALUES
                                            (:invoice_id, :student_id, :class_id, :description, :amount)';
                            $itmStmt = oci_parse($conn, $itmSql);
                            oci_bind_by_name($itmStmt, ':invoice_id',  $invoiceId);
                            oci_bind_by_name($itmStmt, ':student_id',  $stuId);
                            oci_bind_by_name($itmStmt, ':class_id',    $classId);
                            oci_bind_by_name($itmStmt, ':description', $desc);
                            oci_bind_by_name($itmStmt, ':amount',      $fee);
                            oci_execute($itmStmt);
                            oci_free_statement($itmStmt);
                        }

                        $created++;
                    }

                    oci_commit($conn);
                    oci_close($conn);
                    $_SESSION['flash_success'] = "$created invoice(s) generated for {$months[$billingMonth]} $billingYear.";
                    header('Location: /PTE-MANAGEMENT-SYSTEM/src/Invoices/index.php?month=' . $billingMonth . '&year=' . $billingYear);
                    exit;
                }
            }

            oci_close($conn);
        } catch (\RuntimeException $e) {
            $errors[] = 'Database error. Please try again.';
        }
    }
}

// Parents dropdown
try {
    $conn      = getConnection();
    $pListStmt = oci_parse($conn, 'SELECT parent_id, fullname FROM PARENT ORDER BY fullname');
    oci_execute($pListStmt);
    $parentList = [];
    while ($r = oci_fetch_assoc($pListStmt)) $parentList[] = $r;
    oci_free_statement($pListStmt);
    oci_close($conn);
} catch (\RuntimeException $e) {
    $parentList = [];
}

$pageTitle = 'Generate Invoice — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="ml-64 p-8 min-h-screen">
    <div class="mb-6 flex items-center gap-3">
        <a href="/PTE-MANAGEMENT-SYSTEM/src/Invoices/index.php" class="text-slate-400 hover:text-slate-600">
            <i class="ti ti-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Generate Invoice</h1>
            <p class="text-slate-500 text-sm mt-1">Auto-create monthly invoices from active enrolments</p>
        </div>
    </div>

    <?php require_once '../../views/partials/flash.php'; ?>

    <?php if (!empty($errors)): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-6">
        <?php foreach ($errors as $e): ?>
        <p class="flex items-center gap-2 text-sm"><i class="ti ti-alert-circle"></i> <?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 max-w-xl mb-6">
        <form method="POST" class="space-y-5">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Billing Month <span class="text-red-500">*</span></label>
                    <select name="billing_month" required
                            class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select…</option>
                        <?php foreach ($months as $m => $label): ?>
                        <option value="<?= $m ?>" <?= (int)($_POST['billing_month'] ?? 0) === $m ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Billing Year <span class="text-red-500">*</span></label>
                    <select name="billing_year" required
                            class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select…</option>
                        <?php foreach ([2024, 2025, 2026] as $y): ?>
                        <option value="<?= $y ?>" <?= (int)($_POST['billing_year'] ?? 0) === $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Due Day of Month</label>
                    <input type="number" name="due_day" min="1" max="28"
                           value="<?= (int)($_POST['due_day'] ?? 15) ?>"
                           class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-slate-400 mt-1">e.g. 15 = due on the 15th of the billing month</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Parent <span class="text-slate-400 font-normal">(optional)</span></label>
                    <select name="parent_id"
                            class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="0">All eligible parents</option>
                        <?php foreach ($parentList as $p): ?>
                        <option value="<?= (int)$p['PARENT_ID'] ?>"
                                <?= (int)($_POST['parent_id'] ?? 0) === (int)$p['PARENT_ID'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['FULLNAME'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                        class="bg-indigo-100 text-indigo-800 px-5 py-2 rounded-lg hover:bg-indigo-200 inline-flex items-center gap-2 text-sm font-medium">
                    <i class="ti ti-eye"></i> Preview
                </button>
            </div>
        </form>
    </div>

    <?php if (!empty($preview)): ?>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-800">
                Preview — <?= count($preview) ?> invoice(s) to generate
            </h2>
            <form method="POST">
                <input type="hidden" name="billing_month" value="<?= (int)($_POST['billing_month'] ?? 0) ?>">
                <input type="hidden" name="billing_year"  value="<?= (int)($_POST['billing_year']  ?? 0) ?>">
                <input type="hidden" name="due_day"       value="<?= (int)($_POST['due_day']       ?? 15) ?>">
                <input type="hidden" name="parent_id"     value="<?= (int)($_POST['parent_id']     ?? 0) ?>">
                <input type="hidden" name="confirm"       value="1">
                <button type="submit"
                        class="bg-indigo-800 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 inline-flex items-center gap-2 text-sm font-medium">
                    <i class="ti ti-file-plus"></i> Confirm & Generate
                </button>
            </form>
        </div>

        <?php foreach ($preview as $par): ?>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <span class="font-medium text-slate-800"><?= htmlspecialchars($par['FULLNAME'], ENT_QUOTES, 'UTF-8') ?></span>
                <span class="text-sm font-semibold text-indigo-800">RM <?= number_format($par['total'], 2) ?></span>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-slate-400 uppercase">
                        <th class="text-left px-5 py-2">Student</th>
                        <th class="text-left px-5 py-2">Class</th>
                        <th class="text-left px-5 py-2">Subject</th>
                        <th class="text-right px-5 py-2">Fee</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($par['items'] as $item): ?>
                    <tr class="border-t border-slate-100">
                        <td class="px-5 py-2 text-slate-700"><?= htmlspecialchars($item['STUDENT_NAME'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-5 py-2 text-slate-600"><?= htmlspecialchars($item['CLASS_NAME'],   ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-5 py-2 text-slate-500"><?= htmlspecialchars($item['SUBJECT_NAME'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-5 py-2 text-right text-slate-700">RM <?= number_format((float)$item['FEE'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</main>

<?php require_once '../../views/layout/footer.php'; ?>
