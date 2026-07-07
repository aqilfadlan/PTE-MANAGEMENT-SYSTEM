<?php
session_start();
require_once '../../config/database.php';
require_once '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

if (!isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/login');
    exit;
}
if ($_SESSION['role'] !== 'OWNER') {
    header('Location: /PTE-MANAGEMENT-SYSTEM/dashboard');
    exit;
}

/**
 * Every check is read-only or writes only to memory / temp files — nothing
 * here touches real application data (no inserts, updates, or emails sent).
 */
$results = [];

function check(string $group, string $label, callable $fn, bool $optional = false): array
{
    $start = microtime(true);
    try {
        $detail = $fn();
        $status = 'pass';
        $message = is_string($detail) ? $detail : 'OK';
    } catch (\Throwable $e) {
        $status = $optional ? 'warn' : 'fail';
        $message = $e->getMessage();
    }
    $ms = round((microtime(true) - $start) * 1000);
    return compact('group', 'label', 'status', 'message', 'ms');
}

// ── 1. Database connectivity ──────────────────────────────────────────────────
$results[] = check('Database', 'Connect to Oracle', function () {
    $conn = getConnection();
    oci_close($conn);
    return 'Connection established';
});

$results[] = check('Database', 'Round-trip query (SELECT 1 FROM DUAL)', function () {
    $conn = getConnection();
    $stmt = oci_parse($conn, 'SELECT 1 AS ok FROM DUAL');
    if (!oci_execute($stmt)) {
        $e = oci_error($stmt);
        throw new \RuntimeException($e['message']);
    }
    $row = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);
    oci_close($conn);
    if ((int)$row['OK'] !== 1) throw new \RuntimeException('Unexpected result');
    return 'OK';
});

// ── 2. Core table reads ────────────────────────────────────────────────────────
$tables = [
    'USERS', 'OWNER_PROFILE', 'ADMIN_PROFILE', 'TUTOR_PROFILE',
    'PASSWORD_RESET_TOKEN', 'SUBJECT', 'GRADE', 'PARENT', 'STUDENT',
    'CLASS', 'CLASS_SCHEDULE', 'CLASS_SESSION', 'CLASS_STUDENT',
    'STUDENT_ATTENDANCE', 'INVOICE', 'INVOICE_ITEM', 'PAYMENT',
];
foreach ($tables as $table) {
    $results[] = check('Tables', "$table is queryable", function () use ($table) {
        $conn = getConnection();
        $stmt = oci_parse($conn, "SELECT COUNT(*) AS cnt FROM $table");
        if (!oci_execute($stmt)) {
            $e = oci_error($stmt);
            oci_close($conn);
            throw new \RuntimeException($e['message']);
        }
        $row = oci_fetch_assoc($stmt);
        oci_free_statement($stmt);
        oci_close($conn);
        return (int)$row['CNT'] . ' row(s)';
    });
}

// ── 3. Representative queries per module (real bind-variable names used in
//    each page, to catch Oracle reserved-word collisions like :uid/:from/:to) ──
$moduleQueries = [
    'Students'   => ["SELECT student_id FROM STUDENT WHERE grade_id = :grade_id AND status = :status", [':grade_id' => 1, ':status' => 'ACTIVE']],
    'Parents'    => ["SELECT parent_id FROM PARENT WHERE parent_id = :id", [':id' => 1]],
    'Classes'    => ["SELECT class_id FROM CLASS WHERE user_id = :tutor_id AND status = :status", [':tutor_id' => 1, ':status' => 'ACTIVE']],
    'Schedule'   => ["SELECT schedule_id FROM CLASS_SCHEDULE WHERE class_id = :class_id AND effective_from <= TO_DATE(:eff_from, 'YYYY-MM-DD')", [':class_id' => 1, ':eff_from' => '2026-01-01']],
    'Sessions'   => ["SELECT session_id FROM CLASS_SESSION WHERE user_id = :tutor_id", [':tutor_id' => 1]],
    'Attendance' => ["SELECT attendance_id FROM STUDENT_ATTENDANCE WHERE student_id = :student_id", [':student_id' => 1]],
    'Invoices'   => ["SELECT invoice_id FROM INVOICE WHERE parent_id = :parent_id", [':parent_id' => 1]],
    'Payments'   => ["SELECT payment_id FROM PAYMENT WHERE invoice_id = :invoice_id", [':invoice_id' => 1]],
];
foreach ($moduleQueries as $module => [$sql, $binds]) {
    $results[] = check('Module Queries', "$module — representative query binds correctly", function () use ($sql, $binds) {
        $conn = getConnection();
        $stmt = oci_parse($conn, $sql);
        foreach ($binds as $name => &$value) {
            oci_bind_by_name($stmt, $name, $value);
        }
        unset($value);
        if (!oci_execute($stmt)) {
            $e = oci_error($stmt);
            oci_close($conn);
            throw new \RuntimeException($e['message']);
        }
        oci_free_statement($stmt);
        oci_close($conn);
        return 'Bind variables accepted';
    });
}

// ── 4. Route table integrity — every routed file must exist on disk ──────────
$results[] = check('Routing', 'All routed files exist on disk', function () {
    $routesFile = dirname(__DIR__, 2) . '/index.php';
    $contents   = file_get_contents($routesFile);
    preg_match_all("/=>\s*'([^']+\.php)'/", $contents, $matches);
    $missing = [];
    foreach ($matches[1] as $relPath) {
        $fullPath = dirname(__DIR__, 2) . '/' . $relPath;
        if (!file_exists($fullPath)) $missing[] = $relPath;
    }
    if (!empty($missing)) {
        throw new \RuntimeException('Missing: ' . implode(', ', $missing));
    }
    return count($matches[1]) . ' route(s) resolved';
});

// ── 5. PHPMailer / SMTP reachability (handshake only — never sends) ──────────
$results[] = check('Email', 'SMTP handshake (Mailtrap sandbox)', function () {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'sandbox.smtp.mailtrap.io';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'd5588ee6cae646';
    $mail->Password   = 'd01949952c3a34';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 2525;
    $mail->Timeout    = 8;

    if (!$mail->smtpConnect()) {
        throw new \RuntimeException('Could not connect to SMTP host');
    }
    $mail->smtpClose();
    return 'SMTP handshake succeeded (no email sent)';
});

// ── 6. PDF generation (Dompdf) — rendered in memory only, never saved ────────
$results[] = check('PDF Generation', 'Dompdf renders a test document', function () {
    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml('<html><body><p>System test document</p></body></html>');
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $output = $dompdf->output();
    if (strlen($output) < 100 || !str_starts_with($output, '%PDF')) {
        throw new \RuntimeException('Output does not look like a valid PDF');
    }
    return number_format(strlen($output)) . ' bytes generated';
});

// ── 7. Filesystem writability ──────────────────────────────────────────────────
$results[] = check('Filesystem', 'storage/receipts/ is writable', function () {
    $dir = dirname(__DIR__, 2) . '/storage/receipts';
    if (!is_dir($dir)) throw new \RuntimeException('Directory does not exist');
    if (!is_writable($dir)) throw new \RuntimeException('Directory is not writable');
    $testFile = $dir . '/.system_test_' . uniqid() . '.tmp';
    if (@file_put_contents($testFile, 'test') === false) {
        throw new \RuntimeException('Could not write test file');
    }
    unlink($testFile);
    return 'Write + delete succeeded';
});

// ── 8. Platform / extensions ───────────────────────────────────────────────────
$results[] = check('Platform', 'PHP version (as served by Apache)', function () {
    return PHP_VERSION . ' (' . PHP_OS . ')';
});
$requiredExtensions = ['oci8', 'mbstring', 'dom'];
$optionalExtensions  = ['gd']; // only needed by Dompdf for embedding raster images — receipts are text/tables only

foreach ($requiredExtensions as $ext) {
    $results[] = check('Platform', "Extension loaded: $ext", function () use ($ext) {
        if (!extension_loaded($ext)) throw new \RuntimeException('Not loaded');
        return 'Loaded';
    });
}
foreach ($optionalExtensions as $ext) {
    $results[] = check('Platform', "Extension loaded: $ext (optional)", function () use ($ext) {
        if (!extension_loaded($ext)) throw new \RuntimeException('Not loaded — only needed for embedding images in PDFs');
        return 'Loaded';
    }, optional: true);
}

// ── Summarize ───────────────────────────────────────────────────────────────
$totalCount  = count($results);
$passCount   = count(array_filter($results, fn($r) => $r['status'] === 'pass'));
$warnCount   = count(array_filter($results, fn($r) => $r['status'] === 'warn'));
$failCount   = count(array_filter($results, fn($r) => $r['status'] === 'fail'));
$groups      = [];
foreach ($results as $r) {
    $groups[$r['group']][] = $r;
}

$pageTitle = 'System Test — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="pt-16 md:pt-10 md:ml-64 px-4 sm:px-8 pb-4 sm:pb-8 min-h-screen">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold text-slate-800">System Test</h1>
            <p class="text-slate-500 text-sm mt-1">Live health check across database, integrations, and platform — read-only, no data is modified</p>
        </div>
        <a href="/PTE-MANAGEMENT-SYSTEM/system-test"
           class="bg-indigo-800 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 inline-flex items-center gap-2 text-sm">
            <i class="ti ti-refresh"></i> Re-run
        </a>
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Total Checks</p>
            <p class="text-2xl font-bold text-slate-800"><?= $totalCount ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Passed</p>
            <p class="text-2xl font-bold text-green-600"><?= $passCount ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Warnings</p>
            <p class="text-2xl font-bold <?= $warnCount > 0 ? 'text-yellow-600' : 'text-slate-300' ?>"><?= $warnCount ?></p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Failed</p>
            <p class="text-2xl font-bold <?= $failCount > 0 ? 'text-red-600' : 'text-slate-300' ?>"><?= $failCount ?></p>
        </div>
    </div>

    <?php if ($failCount > 0): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-6 text-sm flex items-center gap-2">
        <i class="ti ti-alert-circle"></i>
        <?= $failCount ?> check<?= $failCount === 1 ? '' : 's' ?> failed — see details below.
    </div>
    <?php elseif ($warnCount > 0): ?>
    <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-lg px-4 py-3 mb-6 text-sm flex items-center gap-2">
        <i class="ti ti-alert-triangle"></i>
        All critical checks passed. <?= $warnCount ?> optional check<?= $warnCount === 1 ? '' : 's' ?> flagged — not required for normal operation.
    </div>
    <?php else: ?>
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 mb-6 text-sm flex items-center gap-2">
        <i class="ti ti-circle-check"></i>
        All checks passed. System is in good status.
    </div>
    <?php endif; ?>

    <!-- Grouped results -->
    <div class="space-y-6">
        <?php foreach ($groups as $groupName => $groupResults): ?>
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-800"><?= htmlspecialchars($groupName, ENT_QUOTES, 'UTF-8') ?></h2>
            </div>
            <table class="w-full text-sm">
                <tbody>
                    <?php foreach ($groupResults as $r): ?>
                    <?php
                        $iconClass = match ($r['status']) {
                            'pass' => 'ti-circle-check text-green-600',
                            'warn' => 'ti-alert-triangle text-yellow-500',
                            default => 'ti-circle-x text-red-600',
                        };
                        $messageClass = match ($r['status']) {
                            'fail' => 'text-red-600',
                            'warn' => 'text-yellow-600',
                            default => '',
                        };
                    ?>
                    <tr class="border-b border-slate-100 last:border-b-0">
                        <td class="px-4 py-3 w-8">
                            <i class="ti <?= $iconClass ?> text-base"></i>
                        </td>
                        <td class="px-4 py-3 text-slate-700 font-medium"><?= htmlspecialchars($r['label'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="px-4 py-3 text-slate-500 <?= $messageClass ?>">
                            <?= htmlspecialchars($r['message'], ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="px-4 py-3 text-right text-slate-400 text-xs whitespace-nowrap"><?= $r['ms'] ?> ms</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endforeach; ?>
    </div>
</main>

<?php require_once '../../views/layout/footer.php'; ?>
