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

$errors  = [];
$preview = [];
$generated = 0;

// Map day name to PHP date number (0=Sun … 6=Sat)
$dayMap = [
    'SUN' => 0, 'MON' => 1, 'TUE' => 2, 'WED' => 3,
    'THU' => 4, 'FRI' => 5, 'SAT' => 6,
];

try {
    $conn = getConnection();

    // Active classes with at least one schedule
    $clsSql  = "SELECT DISTINCT c.class_id, c.name, s.name AS subject_name, g.name AS grade_name
                FROM   CLASS c
                JOIN   SUBJECT s ON s.subject_id = c.subject_id
                JOIN   GRADE   g ON g.grade_id   = c.grade_id
                WHERE  c.status = 'ACTIVE'
                AND    c.class_id IN (SELECT sch.class_id FROM CLASS_SCHEDULE sch WHERE sch.class_id IS NOT NULL)
                ORDER  BY c.name";
    $clsStmt = oci_parse($conn, $clsSql);
    oci_execute($clsStmt);
    $classes = [];
    while ($r = oci_fetch_assoc($clsStmt)) $classes[] = $r;
    oci_free_statement($clsStmt);

    oci_close($conn);
} catch (\RuntimeException $e) {
    $classes = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $classId   = (int)($_POST['class_id']  ?? 0);
    $dateFrom  = trim($_POST['date_from']  ?? '');
    $dateTo    = trim($_POST['date_to']    ?? '');
    $doGenerate = isset($_POST['confirm']);

    if ($classId   <= 0)          $errors['class_id'] = 'Please select a class.';
    if ($dateFrom  === '')         $errors['date_from'] = 'Start date is required.';
    if ($dateTo    === '')         $errors['date_to'] = 'End date is required.';
    if ($dateFrom  > $dateTo)      $errors['date_to'] = 'Start date must be before end date.';

    $fromTs = strtotime($dateFrom);
    $toTs   = strtotime($dateTo);
    if (empty($errors) && ($toTs - $fromTs) > 366 * 86400) {
        $errors['date_to'] = 'Date range cannot exceed 1 year.';
    }

    if (empty($errors)) {
        try {
            $conn = getConnection();

            // Fetch schedules for this class within effective range
            $schSql  = "SELECT schedule_id, daysofweek, start_time, end_time,
                               effective_from, effective_to
                        FROM   CLASS_SCHEDULE
                        WHERE  class_id = :class_id
                        AND    effective_from <= TO_DATE(:date_to, 'YYYY-MM-DD')
                        AND    (effective_to IS NULL OR effective_to >= TO_DATE(:date_from, 'YYYY-MM-DD'))";
            $schStmt = oci_parse($conn, $schSql);
            oci_bind_by_name($schStmt, ':class_id',  $classId);
            oci_bind_by_name($schStmt, ':date_from', $dateFrom);
            oci_bind_by_name($schStmt, ':date_to',   $dateTo);
            oci_execute($schStmt);
            $schedules = [];
            while ($r = oci_fetch_assoc($schStmt)) $schedules[] = $r;
            oci_free_statement($schStmt);

            // Get class tutor (user_id)
            $tutSql  = 'SELECT user_id FROM CLASS WHERE class_id = :id';
            $tutStmt = oci_parse($conn, $tutSql);
            oci_bind_by_name($tutStmt, ':id', $classId);
            oci_execute($tutStmt);
            $tutRow  = oci_fetch_assoc($tutStmt);
            $tutorId = (int)$tutRow['USER_ID'];
            oci_free_statement($tutStmt);

            if (empty($schedules)) {
                $errors['_general'] = 'No active schedules found for this class in the selected date range.';
            } else {
                // Build list of dates to generate
                $toGenerate = [];
                $cur = $fromTs;
                while ($cur <= $toTs) {
                    $dow = (int)date('w', $cur); // 0=Sun
                    $curDateStr = date('Y-m-d', $cur);
                    foreach ($schedules as $sch) {
                        if ($dayMap[$sch['DAYSOFWEEK']] !== $dow) continue;

                        // Check schedule effective range
                        $effFrom = strtotime($sch['EFFECTIVE_FROM']);
                        $effTo   = !empty($sch['EFFECTIVE_TO']) ? strtotime($sch['EFFECTIVE_TO']) : PHP_INT_MAX;
                        if ($cur < $effFrom || $cur > $effTo) continue;

                        $toGenerate[] = [
                            'date'        => $curDateStr,
                            'start_time'  => $sch['START_TIME'],
                            'end_time'    => $sch['END_TIME'],
                            'schedule_id' => (int)$sch['SCHEDULE_ID'],
                        ];
                    }
                    $cur += 86400;
                }

                if (empty($toGenerate)) {
                    $errors['_general'] = 'No session dates found matching the schedule days in the selected range.';
                } elseif (!$doGenerate) {
                    // Preview mode
                    $preview = $toGenerate;
                } else {
                    // Generate — skip dates that already have a session for this class
                    $inserted = 0;
                    foreach ($toGenerate as $row) {
                        // Check duplicate
                        $chkSql  = "SELECT COUNT(*) AS cnt FROM CLASS_SESSION
                                    WHERE class_id = :cid
                                    AND   session_date = TO_DATE(:d, 'YYYY-MM-DD')
                                    AND   start_time = :st";
                        $chkStmt = oci_parse($conn, $chkSql);
                        oci_bind_by_name($chkStmt, ':cid', $classId);
                        oci_bind_by_name($chkStmt, ':d',   $row['date']);
                        oci_bind_by_name($chkStmt, ':st',  $row['start_time']);
                        oci_execute($chkStmt);
                        $cnt = (int)oci_fetch_assoc($chkStmt)['CNT'];
                        oci_free_statement($chkStmt);
                        if ($cnt > 0) continue;

                        $insSql  = "INSERT INTO CLASS_SESSION
                                        (class_id, schedule_id, user_id, session_date, start_time, end_time, status)
                                    VALUES
                                        (:class_id, :schedule_id, :user_id,
                                         TO_DATE(:session_date, 'YYYY-MM-DD'),
                                         :start_time, :end_time, 'SCHEDULED')";
                        $insStmt = oci_parse($conn, $insSql);
                        $schId   = $row['schedule_id'];
                        $dt      = $row['date'];
                        $st      = $row['start_time'];
                        $et      = $row['end_time'];
                        oci_bind_by_name($insStmt, ':class_id',    $classId);
                        oci_bind_by_name($insStmt, ':schedule_id', $schId);
                        oci_bind_by_name($insStmt, ':user_id',     $tutorId);
                        oci_bind_by_name($insStmt, ':session_date',$dt);
                        oci_bind_by_name($insStmt, ':start_time',  $st);
                        oci_bind_by_name($insStmt, ':end_time',    $et);
                        oci_execute($insStmt);
                        oci_free_statement($insStmt);
                        $inserted++;
                    }
                    oci_commit($conn);
                    oci_close($conn);
                    $generated = $inserted;
                    $skipped   = count($toGenerate) - $inserted;
                    $_SESSION['flash_success'] = "$inserted session(s) generated" . ($skipped > 0 ? ", $skipped skipped (already exist)." : '.');
                    header('Location: /PTE-MANAGEMENT-SYSTEM/sessions?class_id=' . $classId);
                    exit;
                }
            }

            if (isset($conn) && $conn) oci_close($conn);
        } catch (\RuntimeException $e) {
            $errors['_general'] = 'Database error. Please try again.';
        }
    }
}

$pageTitle = 'Generate Sessions — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="pt-16 md:pt-10 md:ml-64 px-4 sm:px-8 pb-4 sm:pb-8 min-h-screen">
    <div class="mb-6 flex items-center gap-3">
        <a href="/PTE-MANAGEMENT-SYSTEM/sessions" class="text-slate-400 hover:text-slate-600">
            <i class="ti ti-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Generate Sessions</h1>
            <p class="text-slate-500 text-sm mt-1">Bulk-create class sessions from recurring schedules</p>
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
    <?php
        function fieldRing(array $errors, string $key): string {
            return isset($errors[$key])
                ? 'border-red-400 focus:ring-2 focus:ring-red-500 focus:border-red-500'
                : 'border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500';
        }
    ?>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 max-w-xl mb-6">
        <form method="POST" class="space-y-5"
              onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').innerHTML = '<i class=\'ti ti-loader-2 animate-spin\'></i> Loading…';">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Class <span class="text-red-500">*</span></label>
                <select name="class_id" required
                        aria-invalid="<?= isset($errors['class_id']) ? 'true' : 'false' ?>"
                        class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'class_id') ?>">
                    <option value="">Select class…</option>
                    <?php foreach ($classes as $c): ?>
                    <option value="<?= (int)$c['CLASS_ID'] ?>"
                            <?= (int)($_POST['class_id'] ?? 0) === (int)$c['CLASS_ID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['NAME'], ENT_QUOTES, 'UTF-8') ?>
                        — <?= htmlspecialchars($c['SUBJECT_NAME'], ENT_QUOTES, 'UTF-8') ?>
                        (<?= htmlspecialchars($c['GRADE_NAME'], ENT_QUOTES, 'UTF-8') ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['class_id'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['class_id'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php elseif (empty($classes)): ?>
                <p class="text-xs text-slate-400 mt-1">No active classes with schedules found. Add a schedule first via the class detail page.</p>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">From <span class="text-red-500">*</span></label>
                    <input type="date" name="date_from" required
                           value="<?= htmlspecialchars($_POST['date_from'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           aria-invalid="<?= isset($errors['date_from']) ? 'true' : 'false' ?>"
                           class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'date_from') ?>">
                    <?php if (isset($errors['date_from'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['date_from'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">To <span class="text-red-500">*</span></label>
                    <input type="date" name="date_to" required
                           value="<?= htmlspecialchars($_POST['date_to'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           aria-invalid="<?= isset($errors['date_to']) ? 'true' : 'false' ?>"
                           class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'date_to') ?>">
                    <?php if (isset($errors['date_to'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['date_to'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="submit"
                        class="bg-indigo-100 text-indigo-800 px-5 py-2 rounded-lg hover:bg-indigo-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center gap-2 text-sm font-medium">
                    <i class="ti ti-eye"></i> Preview
                </button>
            </div>
        </form>
    </div>

    <!-- Preview / Confirm -->
    <?php if (!empty($preview)): ?>
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden max-w-xl">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-800">Preview — <?= count($preview) ?> sessions to generate</h2>
            <form method="POST"
                  onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').innerHTML = '<i class=\'ti ti-loader-2 animate-spin\'></i> Generating…';">
                <input type="hidden" name="class_id"  value="<?= (int)($_POST['class_id']  ?? 0) ?>">
                <input type="hidden" name="date_from" value="<?= htmlspecialchars($_POST['date_from'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="date_to"   value="<?= htmlspecialchars($_POST['date_to']   ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="confirm"   value="1">
                <button type="submit"
                        class="bg-indigo-800 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center gap-2 text-sm">
                    <i class="ti ti-calendar-plus"></i> Confirm & Generate
                </button>
            </form>
        </div>
        <div class="max-h-96 overflow-y-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 sticky top-0">
                    <tr>
                        <th class="text-left px-4 py-2 text-xs font-medium text-slate-500 uppercase tracking-wide">#</th>
                        <th class="text-left px-4 py-2 text-xs font-medium text-slate-500 uppercase tracking-wide">Date</th>
                        <th class="text-left px-4 py-2 text-xs font-medium text-slate-500 uppercase tracking-wide">Day</th>
                        <th class="text-left px-4 py-2 text-xs font-medium text-slate-500 uppercase tracking-wide">Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($preview as $i => $p): ?>
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="px-4 py-2 text-slate-400 text-xs"><?= $i + 1 ?></td>
                        <td class="px-4 py-2 text-slate-800"><?= date('d M Y', strtotime($p['date'])) ?></td>
                        <td class="px-4 py-2 text-slate-500"><?= date('D', strtotime($p['date'])) ?></td>
                        <td class="px-4 py-2 text-slate-600"><?= htmlspecialchars($p['start_time'], ENT_QUOTES, 'UTF-8') ?> – <?= htmlspecialchars($p['end_time'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 bg-slate-50 border-t border-slate-100 text-xs text-slate-500">
            Sessions that already exist for the same class, date, and start time will be skipped automatically.
        </div>
    </div>
    <?php endif; ?>
</main>

<?php require_once '../../views/layout/footer.php'; ?>
