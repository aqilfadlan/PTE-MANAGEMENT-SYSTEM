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

$errors = [];
$input  = [
    'class_id'   => (int)($_GET['class_id'] ?? 0),
    'session_date' => '',
    'start_time' => '',
    'end_time'   => '',
    'notes'      => '',
];

try {
    $conn = getConnection();

    $clsSql  = "SELECT c.class_id, c.name, s.name AS subject_name, g.name AS grade_name
                FROM   CLASS c
                JOIN   SUBJECT s ON s.subject_id = c.subject_id
                JOIN   GRADE   g ON g.grade_id   = c.grade_id
                WHERE  c.status = 'ACTIVE'
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
    $input['class_id']     = (int)($_POST['class_id'] ?? 0);
    $input['session_date'] = trim($_POST['session_date'] ?? '');
    $input['start_time']   = trim($_POST['start_time'] ?? '');
    $input['end_time']     = trim($_POST['end_time'] ?? '');
    $input['notes']        = trim($_POST['notes'] ?? '');

    if ($input['class_id'] <= 0)        $errors['class_id'] = 'Please select a class.';
    if ($input['session_date'] === '')  $errors['session_date'] = 'Session date is required.';
    if ($input['start_time'] === '')    $errors['start_time'] = 'Start time is required.';
    if ($input['end_time'] === '')      $errors['end_time'] = 'End time is required.';
    if ($input['start_time'] !== '' && $input['end_time'] !== '' && $input['start_time'] >= $input['end_time']) {
        $errors['end_time'] = 'End time must be after start time.';
    }

    if (empty($errors)) {
        try {
            $conn = getConnection();

            $tutSql  = 'SELECT user_id FROM CLASS WHERE class_id = :id';
            $tutStmt = oci_parse($conn, $tutSql);
            oci_bind_by_name($tutStmt, ':id', $input['class_id']);
            oci_execute($tutStmt);
            $tutRow = oci_fetch_assoc($tutStmt);
            oci_free_statement($tutStmt);

            if (!$tutRow) {
                $errors['class_id'] = 'Selected class not found.';
            } else {
                $tutorId = (int)$tutRow['USER_ID'];

                $chkSql  = "SELECT COUNT(*) AS cnt FROM CLASS_SESSION
                            WHERE class_id = :cid
                            AND   session_date = TO_DATE(:d, 'YYYY-MM-DD')
                            AND   start_time = :st";
                $chkStmt = oci_parse($conn, $chkSql);
                oci_bind_by_name($chkStmt, ':cid', $input['class_id']);
                oci_bind_by_name($chkStmt, ':d',   $input['session_date']);
                oci_bind_by_name($chkStmt, ':st',  $input['start_time']);
                oci_execute($chkStmt);
                $exists = (int)oci_fetch_assoc($chkStmt)['CNT'] > 0;
                oci_free_statement($chkStmt);

                if ($exists) {
                    $errors['_general'] = 'A session for this class already exists on that date and start time.';
                } else {
                    $notes = $input['notes'] !== '' ? $input['notes'] : null;
                    $insSql  = "INSERT INTO CLASS_SESSION
                                    (class_id, user_id, session_date, start_time, end_time, status, notes)
                                VALUES
                                    (:class_id, :user_id, TO_DATE(:session_date, 'YYYY-MM-DD'),
                                     :start_time, :end_time, 'SCHEDULED', :notes)";
                    $insStmt = oci_parse($conn, $insSql);
                    oci_bind_by_name($insStmt, ':class_id',     $input['class_id']);
                    oci_bind_by_name($insStmt, ':user_id',      $tutorId);
                    oci_bind_by_name($insStmt, ':session_date', $input['session_date']);
                    oci_bind_by_name($insStmt, ':start_time',   $input['start_time']);
                    oci_bind_by_name($insStmt, ':end_time',     $input['end_time']);
                    oci_bind_by_name($insStmt, ':notes',        $notes);
                    oci_execute($insStmt);
                    oci_commit($conn);
                    oci_free_statement($insStmt);
                    oci_close($conn);

                    $_SESSION['flash_success'] = 'Session created successfully.';
                    header('Location: /PTE-MANAGEMENT-SYSTEM/sessions?class_id=' . $input['class_id']);
                    exit;
                }
            }

            oci_close($conn);
        } catch (\RuntimeException $e) {
            $errors['_general'] = 'Database error. Please try again.';
        }
    }
}

$pageTitle = 'Add Session — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="pt-16 md:pt-10 md:ml-64 px-4 sm:px-8 pb-4 sm:pb-8 min-h-screen">
    <div class="mb-6 flex items-center gap-3">
        <a href="/PTE-MANAGEMENT-SYSTEM/sessions" class="text-slate-400 hover:text-slate-600">
            <i class="ti ti-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Add Session</h1>
            <p class="text-slate-500 text-sm mt-1">Create a one-off session — useful for reschedules or replacing a cancelled class</p>
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

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 max-w-xl">
        <form method="POST" class="space-y-5"
              onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').innerHTML = '<i class=\'ti ti-loader-2 animate-spin\'></i> Creating…';">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Class <span class="text-red-500">*</span></label>
                <select name="class_id" required data-searchable data-placeholder="Search class by name, subject, or grade…"
                        aria-invalid="<?= isset($errors['class_id']) ? 'true' : 'false' ?>"
                        class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'class_id') ?>">
                    <option value="">Select class…</option>
                    <?php foreach ($classes as $c): ?>
                    <option value="<?= (int)$c['CLASS_ID'] ?>" <?= $input['class_id'] === (int)$c['CLASS_ID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['NAME'], ENT_QUOTES, 'UTF-8') ?>
                        — <?= htmlspecialchars($c['SUBJECT_NAME'], ENT_QUOTES, 'UTF-8') ?>
                        (<?= htmlspecialchars($c['GRADE_NAME'], ENT_QUOTES, 'UTF-8') ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['class_id'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['class_id'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Session Date <span class="text-red-500">*</span></label>
                <input type="date" name="session_date" required
                       value="<?= htmlspecialchars($input['session_date'], ENT_QUOTES, 'UTF-8') ?>"
                       aria-invalid="<?= isset($errors['session_date']) ? 'true' : 'false' ?>"
                       class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'session_date') ?>">
                <?php if (isset($errors['session_date'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['session_date'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Start Time <span class="text-red-500">*</span></label>
                    <input type="time" name="start_time" required
                           value="<?= htmlspecialchars($input['start_time'], ENT_QUOTES, 'UTF-8') ?>"
                           aria-invalid="<?= isset($errors['start_time']) ? 'true' : 'false' ?>"
                           class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'start_time') ?>">
                    <?php if (isset($errors['start_time'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['start_time'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">End Time <span class="text-red-500">*</span></label>
                    <input type="time" name="end_time" required
                           value="<?= htmlspecialchars($input['end_time'], ENT_QUOTES, 'UTF-8') ?>"
                           aria-invalid="<?= isset($errors['end_time']) ? 'true' : 'false' ?>"
                           class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'end_time') ?>">
                    <?php if (isset($errors['end_time'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['end_time'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                <textarea name="notes" rows="3"
                          class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                          placeholder="e.g. Makeup session for cancelled 1 Jul class"><?= htmlspecialchars($input['notes'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="bg-indigo-800 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed inline-flex items-center gap-2 text-sm font-medium">
                    <i class="ti ti-calendar-plus"></i> Create Session
                </button>
                <a href="/PTE-MANAGEMENT-SYSTEM/sessions"
                   class="bg-slate-100 text-slate-600 px-5 py-2 rounded-lg hover:bg-slate-200 text-sm font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</main>

<?php require_once '../../views/layout/footer.php'; ?>
