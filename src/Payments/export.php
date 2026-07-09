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

$search   = trim($_GET['search']    ?? '');
$method   = $_GET['method']         ?? '';
$dateFrom = trim($_GET['date_from'] ?? '');
$dateTo   = trim($_GET['date_to']   ?? '');

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

    $sql  = "SELECT TO_CHAR(pay.payment_date, 'YYYY-MM-DD') AS payment_date,
                    p.fullname AS parent_name, p.phone AS parent_phone,
                    i.invoice_id, i.billing_month, i.billing_year, i.status AS invoice_status,
                    pay.amount_paid, pay.method, pay.reference_no, pay.notes
             FROM   PAYMENT pay
             JOIN   INVOICE i ON i.invoice_id = pay.invoice_id
             JOIN   PARENT  p ON p.parent_id  = i.parent_id
             WHERE  $where
             ORDER  BY pay.payment_date DESC, pay.payment_id DESC";
    $stmt = oci_parse($conn, $sql);
    foreach ($params as $k => &$v) oci_bind_by_name($stmt, $k, $v);
    unset($v);
    oci_execute($stmt);

    $payments = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $row['INVOICE_NO'] = '#' . str_pad((string)$row['INVOICE_ID'], 5, '0', STR_PAD_LEFT);
        $row['BILLING']    = ($months[(int)$row['BILLING_MONTH']] ?? $row['BILLING_MONTH']) . ' ' . $row['BILLING_YEAR'];
        $row['METHOD_LABEL'] = $methodLabels[$row['METHOD']] ?? $row['METHOD'];
        $payments[] = $row;
    }
    oci_free_statement($stmt);
    oci_close($conn);
} catch (\RuntimeException $e) {
    $payments = [];
}

exportCsv('payments_' . date('Ymd_His') . '.csv', [
    'Date'           => 'PAYMENT_DATE',
    'Parent'         => 'PARENT_NAME',
    'Phone'          => 'PARENT_PHONE',
    'Invoice'        => 'INVOICE_NO',
    'Billing'        => 'BILLING',
    'Method'         => 'METHOD_LABEL',
    'Reference'      => 'REFERENCE_NO',
    'Amount'         => 'AMOUNT_PAID',
    'Invoice Status' => 'INVOICE_STATUS',
], $payments);
