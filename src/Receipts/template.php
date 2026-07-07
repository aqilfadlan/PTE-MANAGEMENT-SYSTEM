<?php

/**
 * Shared CSS for the receipt, used by both the browser view and the PDF
 * export so they always render identically.
 */
function receiptStyles(): string
{
    return '
        .receipt-body { font-family: "DejaVu Sans", sans-serif; color: #1e293b; font-size: 13px; line-height: 1.5; }
        .receipt-body .header { border-bottom: 3px solid #3730a3; padding-bottom: 16px; margin-bottom: 20px; }
        .receipt-body .brand { color: #3730a3; font-size: 20px; font-weight: bold; margin: 0 0 4px 0; }
        .receipt-body .muted { color: #64748b; font-size: 11px; margin: 0; }
        .receipt-body table { width: 100%; border-collapse: collapse; }
        .receipt-body .info-table td { vertical-align: top; padding-bottom: 20px; }
        .receipt-body .box { background: #f1f5f9; border-radius: 8px; padding: 18px 20px; margin: 0 0 24px 0; }
        .receipt-body .box td { vertical-align: top; }
        .receipt-body .label { color: #94a3b8; font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 6px 0; }
        .receipt-body .value { color: #1e293b; font-weight: 600; margin: 0 0 4px 0; }
        .receipt-body .items-section-label { margin: 0 0 10px 0; }
        .receipt-body .items-table th { text-align: left; color: #94a3b8; font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; padding: 0 0 8px 0; }
        .receipt-body .items-table th.amount-col, .receipt-body .items-table td.amount-col { text-align: right; }
        .receipt-body .items-table td { padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
        .receipt-body .total-row td { font-weight: bold; border-top: 2px solid #1e293b; border-bottom: none; padding-top: 12px; }
        .receipt-body .balance-table { margin-top: 20px; padding-top: 16px; border-top: 1px solid #e2e8f0; }
        .receipt-body .footer-note { margin-top: 32px; padding-top: 16px; border-top: 1px solid #e2e8f0; }
    ';
}

/**
 * Renders the receipt body markup shared by both the browser view
 * (Receipts/view.php) and the PDF export (Dompdf renders this same markup
 * to a PDF file).
 */
function renderReceiptHtml(array $payment, array $invoice, array $items): string
{
    $months = [
        1 => 'January', 2 => 'February', 3 => 'March',    4 => 'April',
        5 => 'May',     6 => 'June',     7 => 'July',      8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
    ];

    $methodLabels = [
        'CASH'          => 'Cash',
        'BANK_TRANSFER' => 'Bank Transfer',
        'ONLINE'        => 'Online',
        'CHEQUE'        => 'Cheque',
    ];

    $e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

    $receiptNo   = 'RCPT-' . str_pad((string)$payment['PAYMENT_ID'], 6, '0', STR_PAD_LEFT);
    $invoiceNo   = 'INV-' . str_pad((string)$invoice['INVOICE_ID'], 5, '0', STR_PAD_LEFT);
    $billingPeriod = ($months[(int)$invoice['BILLING_MONTH']] ?? '') . ' ' . (int)$invoice['BILLING_YEAR'];
    $paymentDate = date('d M Y', strtotime($payment['PAYMENT_DATE']));
    $methodLabel = $methodLabels[$payment['METHOD']] ?? $payment['METHOD'];

    $itemRows = '';
    foreach ($items as $item) {
        $itemRows .= '<tr>'
            . '<td style="color:#334155;">' . $e($item['DESCRIPTION']) . '</td>'
            . '<td class="amount-col" style="color:#334155;">RM ' . number_format((float)$item['AMOUNT'], 2) . '</td>'
            . '</tr>';
    }

    $balanceAfter   = (float)$invoice['TOTAL_AMOUNT'] - (float)$invoice['TOTAL_PAID_TO_DATE'];
    $balanceLabel   = $balanceAfter > 0 ? 'RM ' . number_format($balanceAfter, 2) . ' outstanding' : 'Fully paid';
    $balanceColor   = $balanceAfter > 0 ? '#c2410c' : '#15803d';

    // Added embedded <style> and explicit width="100%" on all tables for Dompdf stability
    return '
    <div class="receipt-body">
        <style>' . receiptStyles() . '</style>
        
        <div class="header">
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="50%" valign="top">
                        <p class="brand">Pusat Tuisyen Intelek Excellence</p>
                        <p class="muted">Payment Receipt</p>
                    </td>
                    <td width="50%" valign="top" style="text-align:right;">
                        <p class="value">' . $e($receiptNo) . '</p>
                        <p class="muted">' . $e($paymentDate) . '</p>
                    </td>
                </tr>
            </table>
        </div>

        <table class="info-table" width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td width="50%" valign="top">
                    <p class="label">Billed To</p>
                    <p class="value">' . $e($invoice['PARENT_NAME']) . '</p>
                    <p class="muted">' . $e($invoice['PARENT_PHONE']) . '</p>
                    ' . (!empty($invoice['PARENT_EMAIL']) ? '<p class="muted">' . $e($invoice['PARENT_EMAIL']) . '</p>' : '') . '
                </td>
                <td width="50%" valign="top" style="text-align:right;">
                    <p class="label">Invoice</p>
                    <p class="value">' . $e($invoiceNo) . '</p>
                    <p class="muted">' . $e($billingPeriod) . '</p>
                </td>
            </tr>
        </table>

        <div class="box">
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="50%" valign="top">
                        <p class="label">Amount Paid</p>
                        <p class="value" style="font-size:22px;color:#3730a3;margin:0;">RM ' . number_format((float)$payment['AMOUNT_PAID'], 2) . '</p>
                    </td>
                    <td width="50%" valign="top" style="text-align:right;">
                        <p class="label">Payment Method</p>
                        <p class="value" style="margin:0 0 4px 0;">' . $e($methodLabel) . '</p>
                        ' . (!empty($payment['REFERENCE_NO']) ? '<p class="muted" style="margin:0;">Ref: ' . $e($payment['REFERENCE_NO']) . '</p>' : '') . '
                    </td>
                </tr>
            </table>
        </div>

        <p class="label items-section-label">Invoice Line Items</p>
        <table class="items-table" width="100%" border="0" cellpadding="0" cellspacing="0">
            <thead>
                <tr><th>Description</th><th class="amount-col">Amount</th></tr>
            </thead>
            <tbody>
                ' . $itemRows . '
                <tr class="total-row">
                    <td>Invoice Total</td>
                    <td class="amount-col">RM ' . number_format((float)$invoice['TOTAL_AMOUNT'], 2) . '</td>
                </tr>
            </tbody>
        </table>

        <table class="balance-table" width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td class="muted">Balance after this payment</td>
                <td style="text-align:right;color:' . $balanceColor . ';font-weight:600;">' . $e($balanceLabel) . '</td>
            </tr>
        </table>

        <p class="muted footer-note">
            This is a computer-generated receipt and does not require a signature.
        </p>
    </div>';
}