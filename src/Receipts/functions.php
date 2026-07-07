<?php

require_once __DIR__ . '/template.php';

/**
 * Fetches everything needed to render a receipt for one payment:
 * the payment row, its parent invoice (with total paid to date), and
 * the invoice's line items.
 */
function fetchReceiptData($conn, int $paymentId): ?array
{
    $paySql  = "SELECT pay.payment_id, pay.invoice_id, pay.amount_paid, pay.method,
                       pay.reference_no, pay.notes, pay.receipt_token,
                       TO_CHAR(pay.payment_date, 'YYYY-MM-DD') AS payment_date
                FROM   PAYMENT pay
                WHERE  pay.payment_id = :payment_id";
    $payStmt = oci_parse($conn, $paySql);
    oci_bind_by_name($payStmt, ':payment_id', $paymentId);
    oci_execute($payStmt);
    $payment = oci_fetch_assoc($payStmt);
    oci_free_statement($payStmt);

    if (!$payment) return null;

    $invoiceId = (int)$payment['INVOICE_ID'];

    $invSql  = "SELECT i.invoice_id, i.billing_month, i.billing_year, i.total_amount,
                       p.parent_id, p.fullname AS parent_name, p.phone AS parent_phone, p.email AS parent_email,
                       NVL((SELECT SUM(pay2.amount_paid) FROM PAYMENT pay2 WHERE pay2.invoice_id = i.invoice_id), 0) AS total_paid_to_date
                FROM   INVOICE i
                JOIN   PARENT  p ON p.parent_id = i.parent_id
                WHERE  i.invoice_id = :invoice_id";
    $invStmt = oci_parse($conn, $invSql);
    oci_bind_by_name($invStmt, ':invoice_id', $invoiceId);
    oci_execute($invStmt);
    $invoice = oci_fetch_assoc($invStmt);
    oci_free_statement($invStmt);

    if (!$invoice) return null;

    $itemSql = 'SELECT ii.description, ii.amount
                FROM   INVOICE_ITEM ii
                WHERE  ii.invoice_id = :invoice_id2
                ORDER  BY ii.item_id';
    $itemStmt = oci_parse($conn, $itemSql);
    oci_bind_by_name($itemStmt, ':invoice_id2', $invoiceId);
    oci_execute($itemStmt);
    $items = [];
    while ($r = oci_fetch_assoc($itemStmt)) $items[] = $r;
    oci_free_statement($itemStmt);

    return ['payment' => $payment, 'invoice' => $invoice, 'items' => $items];
}

/**
 * Renders a receipt to PDF bytes via Dompdf and saves it under storage/receipts/.
 * Returns the absolute file path written.
 */
function generateReceiptPdf(array $payment, array $invoice, array $items): string
{
    $html = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>'
          . receiptStyles()
          . '</style></head><body>'
          . renderReceiptHtml($payment, $invoice, $items)
          . '</body></html>';

    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $dir = dirname(__DIR__, 2) . '/storage/receipts';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $path = $dir . '/receipt_' . (int)$payment['PAYMENT_ID'] . '.pdf';
    file_put_contents($path, $dompdf->output());

    return $path;
}
