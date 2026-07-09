<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/csv_export.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/login');
    exit;
}
if (!in_array($_SESSION['role'], ['OWNER', 'ADMIN'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/dashboard');
    exit;
}

$search       = trim($_GET['search']  ?? '');
$statusFilter = $_GET['status']       ?? '';
$monthFilter  = (int)($_GET['month']  ?? 0);
$yearFilter   = (int)($_GET['year']   ?? 0);

$months = [
    1 => 'January', 2 => 'February', 3 => 'March',    4 => 'April',
    5 => 'May',     6 => 'June',     7 => 'July',      8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
];

try {
    $conn = getConnection();

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

    $sql  = "SELECT i.invoice_id, i.billing_month, i.billing_year,
                    i.total_amount, i.status,
                    TO_CHAR(i.due_date, 'YYYY-MM-DD') AS due_date,
                    p.fullname AS parent_name, p.phone AS parent_phone,
                    COALESCE(SUM(pay.amount_paid), 0) AS total_paid
             FROM   INVOICE i
             JOIN   PARENT  p   ON p.parent_id   = i.parent_id
             LEFT   JOIN PAYMENT pay ON pay.invoice_id = i.invoice_id
             WHERE  $where
             GROUP  BY i.invoice_id, i.billing_month, i.billing_year,
                       i.total_amount, i.status, i.due_date,
                       p.fullname, p.phone
             ORDER  BY i.billing_year DESC, i.billing_month DESC, p.fullname";
    $stmt = oci_parse($conn, $sql);
    foreach ($params as $k => &$v) oci_bind_by_name($stmt, $k, $v);
    unset($v);
    oci_execute($stmt);

    $invoices = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $row['INVOICE_NO']   = '#' . str_pad((string)$row['INVOICE_ID'], 5, '0', STR_PAD_LEFT);
        $row['BILLING']      = ($months[(int)$row['BILLING_MONTH']] ?? $row['BILLING_MONTH']) . ' ' . $row['BILLING_YEAR'];
        $row['BALANCE']      = (float)$row['TOTAL_AMOUNT'] - (float)$row['TOTAL_PAID'];
        $invoices[] = $row;
    }
    oci_free_statement($stmt);
    oci_close($conn);
} catch (\RuntimeException $e) {
    $invoices = [];
}

exportCsv('invoices_' . date('Ymd_His') . '.csv', [
    'Invoice'  => 'INVOICE_NO',
    'Parent'   => 'PARENT_NAME',
    'Phone'    => 'PARENT_PHONE',
    'Billing'  => 'BILLING',
    'Due Date' => 'DUE_DATE',
    'Amount'   => 'TOTAL_AMOUNT',
    'Paid'     => 'TOTAL_PAID',
    'Balance'  => 'BALANCE',
    'Status'   => 'STATUS',
], $invoices);
