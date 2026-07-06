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

$invoiceId = (int)($_GET['invoice_id'] ?? 0);
if ($invoiceId === 0) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/invoices');
    exit;
}

$months = [
    1 => 'January', 2 => 'February', 3 => 'March',    4 => 'April',
    5 => 'May',     6 => 'June',     7 => 'July',      8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
];

$errors = [];

// ── Fetch invoice ─────────────────────────────────────────────────────────────
try {
    $conn = getConnection();

    $sql  = "SELECT i.invoice_id, i.billing_month, i.billing_year,
                    i.total_amount, i.status,
                    TO_CHAR(i.due_date, 'YYYY-MM-DD') AS due_date,
                    p.parent_id, p.fullname AS parent_name, p.phone AS parent_phone,
                    COALESCE(SUM(pay.amount_paid), 0) AS total_paid
             FROM   INVOICE i
             JOIN   PARENT  p   ON p.parent_id   = i.parent_id
             LEFT   JOIN PAYMENT pay ON pay.invoice_id = i.invoice_id
             WHERE  i.invoice_id = :id
             GROUP  BY i.invoice_id, i.billing_month, i.billing_year,
                       i.total_amount, i.status, i.due_date,
                       p.parent_id, p.fullname, p.phone";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':id', $invoiceId);
    oci_execute($stmt);
    $invoice = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);
    oci_close($conn);
} catch (\RuntimeException $e) {
    $_SESSION['flash_error'] = 'Database error.';
    header('Location: /PTE-MANAGEMENT-SYSTEM/invoices');
    exit;
}

if (!$invoice) {
    $_SESSION['flash_error'] = 'Invoice not found.';
    header('Location: /PTE-MANAGEMENT-SYSTEM/invoices');
    exit;
}

if ($invoice['STATUS'] === 'PAID') {
    $_SESSION['flash_error'] = 'This invoice is already fully paid.';
    header('Location: /PTE-MANAGEMENT-SYSTEM/invoices/show?id=' . $invoiceId);
    exit;
}

$balance = (float)$invoice['TOTAL_AMOUNT'] - (float)$invoice['TOTAL_PAID'];

// ── Handle POST ───────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amountPaid  = trim($_POST['amount_paid']  ?? '');
    $method      = $_POST['method']            ?? '';
    $paymentDate = trim($_POST['payment_date'] ?? '');
    $referenceNo = trim($_POST['reference_no'] ?? '');
    $notes       = trim($_POST['notes']        ?? '');

    if (!is_numeric($amountPaid) || (float)$amountPaid <= 0) {
        $errors['amount_paid'] = 'Amount must be a positive number.';
    } elseif ((float)$amountPaid > $balance) {
        $errors['amount_paid'] = 'Amount cannot exceed the outstanding balance of RM ' . number_format($balance, 2) . '.';
    }
    if (!in_array($method, ['CASH', 'BANK_TRANSFER', 'ONLINE', 'CHEQUE'])) {
        $errors['method'] = 'Select a valid payment method.';
    }
    if ($paymentDate === '') {
        $errors['payment_date'] = 'Payment date is required.';
    }

    if (empty($errors)) {
        try {
            $conn       = getConnection();
            $paid       = (float)$amountPaid;
            $newTotalPaid = (float)$invoice['TOTAL_PAID'] + $paid;
            $newBalance   = (float)$invoice['TOTAL_AMOUNT'] - $newTotalPaid;

            // Determine new invoice status
            if ($newBalance <= 0) {
                $newStatus = 'PAID';
            } elseif ($newTotalPaid > 0) {
                $newStatus = 'PARTIAL';
            } else {
                $newStatus = $invoice['STATUS'];
            }

            // Insert payment
            $paySql  = "INSERT INTO PAYMENT
                            (invoice_id, amount_paid, method, payment_date, reference_no, notes)
                        VALUES
                            (:invoice_id, :amount_paid, :method,
                             TO_DATE(:payment_date, 'YYYY-MM-DD'), :reference_no, :notes)";
            $payStmt = oci_parse($conn, $paySql);
            oci_bind_by_name($payStmt, ':invoice_id',  $invoiceId);
            oci_bind_by_name($payStmt, ':amount_paid', $paid);
            oci_bind_by_name($payStmt, ':method',      $method);
            oci_bind_by_name($payStmt, ':payment_date',$paymentDate);
            oci_bind_by_name($payStmt, ':reference_no',$referenceNo);
            oci_bind_by_name($payStmt, ':notes',       $notes);
            oci_execute($payStmt);
            oci_free_statement($payStmt);

            // Update invoice status
            $updSql  = 'UPDATE INVOICE SET status = :status, updated_at = SYSTIMESTAMP WHERE invoice_id = :id';
            $updStmt = oci_parse($conn, $updSql);
            oci_bind_by_name($updStmt, ':status', $newStatus);
            oci_bind_by_name($updStmt, ':id',     $invoiceId);
            oci_execute($updStmt);
            oci_free_statement($updStmt);

            oci_commit($conn);
            oci_close($conn);

            $_SESSION['flash_success'] = 'Payment of RM ' . number_format($paid, 2) . ' recorded. Invoice is now ' . strtolower($newStatus) . '.';
            header('Location: /PTE-MANAGEMENT-SYSTEM/invoices/show?id=' . $invoiceId);
            exit;
        } catch (\RuntimeException $e) {
            $errors['_general'] = 'Database error. Please try again.';
        }
    }
}

$pageTitle = 'Record Payment — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="pt-16 md:pt-10 md:ml-64 px-4 sm:px-8 pb-4 sm:pb-8 min-h-screen">
    <div class="mb-6 flex items-center gap-3">
        <a href="/PTE-MANAGEMENT-SYSTEM/invoices/show?id=<?= $invoiceId ?>"
           class="text-slate-400 hover:text-slate-600">
            <i class="ti ti-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Record Payment</h1>
            <p class="text-slate-500 text-sm mt-1">
                Invoice #<?= str_pad($invoiceId, 5, '0', STR_PAD_LEFT) ?>
                &middot; <?= htmlspecialchars($invoice['PARENT_NAME'], ENT_QUOTES, 'UTF-8') ?>
                &middot; <?= $months[(int)$invoice['BILLING_MONTH']] ?> <?= (int)$invoice['BILLING_YEAR'] ?>
            </p>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-6">
        <?php foreach ($errors as $e): ?>
        <p class="flex items-center gap-2 text-sm"><i class="ti ti-alert-circle"></i> <?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?php
        function fieldRing(array $errors, string $key): string {
            return isset($errors[$key])
                ? 'border-red-400 focus:ring-2 focus:ring-red-500 focus:border-red-500'
                : 'border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500';
        }
    ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Payment form -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <h2 class="text-sm font-semibold text-slate-800 mb-5">Payment Details</h2>
            <form method="POST" class="space-y-5"
                  onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').innerHTML = '<i class=\'ti ti-loader-2 animate-spin\'></i> Saving…';">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Amount (RM) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount_paid" required step="0.01" min="0.01"
                               max="<?= number_format($balance, 2, '.', '') ?>"
                               value="<?= htmlspecialchars($_POST['amount_paid'] ?? number_format($balance, 2, '.', ''), ENT_QUOTES, 'UTF-8') ?>"
                               aria-invalid="<?= isset($errors['amount_paid']) ? 'true' : 'false' ?>"
                               class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'amount_paid') ?>">
                        <?php if (isset($errors['amount_paid'])): ?>
                        <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['amount_paid'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php else: ?>
                        <p class="text-xs text-slate-400 mt-1">Outstanding: RM <?= number_format($balance, 2) ?></p>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Payment Date <span class="text-red-500">*</span></label>
                        <input type="date" name="payment_date" required
                               value="<?= htmlspecialchars($_POST['payment_date'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8') ?>"
                               aria-invalid="<?= isset($errors['payment_date']) ? 'true' : 'false' ?>"
                               class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'payment_date') ?>">
                        <?php if (isset($errors['payment_date'])): ?>
                        <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['payment_date'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Payment Method <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-4 gap-2">
                        <?php
                        $methods = ['CASH' => 'Cash', 'BANK_TRANSFER' => 'Bank Transfer', 'ONLINE' => 'Online', 'CHEQUE' => 'Cheque'];
                        foreach ($methods as $val => $label):
                            $selected = ($_POST['method'] ?? 'CASH') === $val;
                        ?>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="method" value="<?= $val ?>" class="sr-only peer" <?= $selected ? 'checked' : '' ?>>
                            <div class="border-2 <?= isset($errors['method']) ? 'border-red-300' : 'border-slate-200' ?> rounded-lg p-3 text-center text-xs font-medium text-slate-600
                                        peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-800
                                        peer-focus-visible:ring-2 peer-focus-visible:ring-indigo-500
                                        hover:border-slate-300 transition">
                                <?= $label ?>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <?php if (isset($errors['method'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['method'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Reference No. <span class="text-slate-400 font-normal">(optional)</span></label>
                    <input type="text" name="reference_no"
                           value="<?= htmlspecialchars($_POST['reference_no'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="Bank ref, cheque no., receipt no…"
                           class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'reference_no') ?>">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Notes <span class="text-slate-400 font-normal">(optional)</span></label>
                    <textarea name="notes" rows="2"
                              class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'notes') ?>"
                              placeholder="Any additional notes…"><?= htmlspecialchars($_POST['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="submit"
                            class="bg-indigo-800 text-white px-6 py-2.5 rounded-lg hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center gap-2 text-sm font-medium">
                        <i class="ti ti-device-floppy"></i> Save Payment
                    </button>
                    <a href="/PTE-MANAGEMENT-SYSTEM/invoices/show?id=<?= $invoiceId ?>"
                       class="bg-slate-100 text-slate-600 px-5 py-2.5 rounded-lg hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 inline-flex items-center gap-2 text-sm">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Invoice summary -->
        <div class="space-y-4">
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                <h2 class="text-sm font-semibold text-slate-800 mb-4">Invoice Summary</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Invoice Total</dt>
                        <dd class="font-medium text-slate-800">RM <?= number_format((float)$invoice['TOTAL_AMOUNT'], 2) ?></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Total Paid</dt>
                        <dd class="font-medium text-green-600">RM <?= number_format((float)$invoice['TOTAL_PAID'], 2) ?></dd>
                    </div>
                    <div class="flex justify-between border-t border-slate-100 pt-2">
                        <dt class="font-semibold text-slate-700">Balance Due</dt>
                        <dd class="font-bold text-red-600">RM <?= number_format($balance, 2) ?></dd>
                    </div>
                </dl>
            </div>
            <div class="bg-slate-50 rounded-lg border border-slate-200 p-4 text-xs text-slate-500 space-y-1">
                <p><span class="font-medium text-slate-700">Due:</span> <?= date('d M Y', strtotime($invoice['DUE_DATE'])) ?></p>
                <p><span class="font-medium text-slate-700">Parent:</span> <?= htmlspecialchars($invoice['PARENT_NAME'], ENT_QUOTES, 'UTF-8') ?></p>
                <p><span class="font-medium text-slate-700">Phone:</span> <?= htmlspecialchars($invoice['PARENT_PHONE'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>

    </div>
</main>

<?php require_once '../../views/layout/footer.php'; ?>
