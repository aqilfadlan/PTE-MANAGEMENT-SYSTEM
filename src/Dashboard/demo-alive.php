<?php

session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/login');
    exit;
}
if ($_SESSION['role'] !== 'OWNER') {
    header('Location: /PTE-MANAGEMENT-SYSTEM/dashboard');
    exit;
}

try {
    $conn = getConnection();

    // Activity feed — union of recent events across modules, newest first.
    // Each branch selects the same 4 columns (kind, headline, detail,
    // occurred_at) so UNION ALL can stack them; TO_CHAR(NULL) placeholders
    // keep column types aligned where a branch has nothing to put there.
    $feedSql = "SELECT * FROM (
                    SELECT 'ENROLMENT' AS kind,
                           st.fullname || ' enrolled in ' || c.name AS headline,
                           g.name AS detail,
                           cst.enrolled_at AS occurred_at
                    FROM   CLASS_STUDENT cst
                    JOIN   STUDENT st ON st.student_id = cst.student_id
                    JOIN   CLASS   c  ON c.class_id    = cst.class_id
                    JOIN   GRADE   g  ON g.grade_id    = st.grade_id

                    UNION ALL

                    SELECT 'PAYMENT' AS kind,
                           p.fullname || ' paid RM' || TO_CHAR(pay.amount_paid, 'FM999,999.00') AS headline,
                           'Invoice #' || TO_CHAR(i.invoice_id) AS detail,
                           pay.created_at AS occurred_at
                    FROM   PAYMENT pay
                    JOIN   INVOICE i ON i.invoice_id = pay.invoice_id
                    JOIN   PARENT  p ON p.parent_id  = i.parent_id

                    UNION ALL

                    SELECT 'NEW_STUDENT' AS kind,
                           st.fullname || ' was added as a new student' AS headline,
                           g.name AS detail,
                           st.created_at AS occurred_at
                    FROM   STUDENT st
                    JOIN   GRADE   g ON g.grade_id = st.grade_id

                    UNION ALL

                    SELECT 'ABSENT' AS kind,
                           st.fullname || ' was marked absent' AS headline,
                           c.name AS detail,
                           sa.created_at AS occurred_at
                    FROM   STUDENT_ATTENDANCE sa
                    JOIN   STUDENT       st ON st.student_id = sa.student_id
                    JOIN   CLASS_SESSION cs ON cs.session_id = sa.session_id
                    JOIN   CLASS         c  ON c.class_id    = cs.class_id
                    WHERE  sa.status = 'ABSENT'
                )
                ORDER  BY occurred_at DESC
                FETCH FIRST 10 ROWS ONLY";
    $feedStmt = oci_parse($conn, $feedSql);
    oci_execute($feedStmt);
    $activity = [];
    while ($r = oci_fetch_assoc($feedStmt)) $activity[] = $r;
    oci_free_statement($feedStmt);

    // Alerts — reuses the same logic already proven in Invoices/report pages,
    // just condensed into a single "needs attention" list.
    $alerts = [];

    $overdueSql = "SELECT COUNT(*) AS cnt, NVL(SUM(i.total_amount), 0) AS total
                    FROM   INVOICE i
                    WHERE  i.status IN ('UNPAID', 'OVERDUE')
                    AND    i.due_date < TRUNC(SYSDATE)";
    $overdueStmt = oci_parse($conn, $overdueSql);
    oci_execute($overdueStmt);
    $overdue = oci_fetch_assoc($overdueStmt);
    oci_free_statement($overdueStmt);
    if ((int)$overdue['CNT'] > 0) {
        $alerts[] = [
            'icon' => 'ti-file-invoice', 'color' => 'orange',
            'text' => (int)$overdue['CNT'] . ' invoice' . ((int)$overdue['CNT'] === 1 ? '' : 's') . ' overdue — RM ' . number_format((float)$overdue['TOTAL'], 2) . ' outstanding',
            'link' => '/PTE-MANAGEMENT-SYSTEM/invoices?status=OVERDUE',
        ];
    }

    // Low attendance — same HAVING pattern as Attendance/report.php, last 30 days
    $lowAttSql = "SELECT COUNT(*) AS cnt FROM (
                    SELECT sa.student_id
                    FROM   STUDENT_ATTENDANCE sa
                    JOIN   CLASS_SESSION cs ON cs.session_id = sa.session_id
                    WHERE  cs.session_date >= TRUNC(SYSDATE) - 30
                    GROUP  BY sa.student_id
                    HAVING SUM(CASE WHEN sa.status IN ('PRESENT', 'LATE') THEN 1 ELSE 0 END) < 0.8 * COUNT(*)
                  )";
    $lowAttStmt = oci_parse($conn, $lowAttSql);
    oci_execute($lowAttStmt);
    $lowAtt = (int)oci_fetch_assoc($lowAttStmt)['CNT'];
    oci_free_statement($lowAttStmt);
    if ($lowAtt > 0) {
        $alerts[] = [
            'icon' => 'ti-alert-triangle', 'color' => 'red',
            'text' => $lowAtt . ' student' . ($lowAtt === 1 ? '' : 's') . ' below 80% attendance in the last 30 days',
            'link' => '/PTE-MANAGEMENT-SYSTEM/attendance/report',
        ];
    }

    // Tutors with zero sessions scheduled in the next 7 days
    $idleTutorSql = "SELECT COUNT(*) AS cnt FROM (
                        SELECT u.user_id
                        FROM   USERS u
                        JOIN   CLASS c ON c.user_id = u.user_id AND c.status = 'ACTIVE'
                        WHERE  u.role = 'TUTOR'
                        AND    u.user_id NOT IN (
                            SELECT cs.user_id FROM CLASS_SESSION cs
                            WHERE  cs.session_date BETWEEN TRUNC(SYSDATE) AND TRUNC(SYSDATE) + 7
                            AND    cs.status = 'SCHEDULED'
                            AND    cs.user_id IS NOT NULL
                        )
                        GROUP BY u.user_id
                      )";
    $idleStmt = oci_parse($conn, $idleTutorSql);
    oci_execute($idleStmt);
    $idleTutors = (int)oci_fetch_assoc($idleStmt)['CNT'];
    oci_free_statement($idleStmt);
    if ($idleTutors > 0) {
        $alerts[] = [
            'icon' => 'ti-calendar-off', 'color' => 'yellow',
            'text' => $idleTutors . ' tutor' . ($idleTutors === 1 ? '' : 's') . ' with no sessions in the next 7 days',
            'link' => '/PTE-MANAGEMENT-SYSTEM/sessions',
        ];
    }

    oci_close($conn);
} catch (\RuntimeException $e) {
    $activity = [];
    $alerts   = [];
}

$kindMeta = [
    'ENROLMENT'   => ['icon' => 'ti-user-plus',       'color' => 'indigo'],
    'PAYMENT'     => ['icon' => 'ti-cash',            'color' => 'green'],
    'NEW_STUDENT' => ['icon' => 'ti-school',           'color' => 'indigo'],
    'ABSENT'      => ['icon' => 'ti-calendar-x',       'color' => 'red'],
];

function timeAgo(string $timestamp): string
{
    $diff = time() - strtotime($timestamp);
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    return floor($diff / 86400) . 'd ago';
}

$pageTitle = 'Dashboard Concept — Alive & Interactive (Demo)';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="pt-16 md:pt-10 md:ml-64 px-4 sm:px-8 pb-4 sm:pb-8 min-h-screen">
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-1">
            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Concept / Not Live</span>
        </div>
        <h1 class="text-xl font-semibold text-slate-800">Dashboard Concept — Making It Feel Alive</h1>
        <p class="text-slate-500 text-sm mt-1">
            A standalone example of 3 additions discussed for the real dashboard: an activity feed, an alerts card,
            and a staggered entrance animation. Nothing here is wired into <code class="text-xs bg-slate-100 px-1 py-0.5 rounded">/dashboard</code> yet.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        <!-- Alerts card -->
        <div class="reveal bg-white rounded-lg shadow-sm border border-slate-200 p-6 lg:col-span-1" style="--delay: 0ms">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-medium text-slate-500 uppercase tracking-wide">Needs Attention</p>
                <span class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center">
                    <i class="ti ti-bell text-orange-600 text-sm"></i>
                </span>
            </div>
            <?php if (empty($alerts)): ?>
            <div class="text-center py-8">
                <i class="ti ti-circle-check text-3xl text-green-500 block mb-2"></i>
                <p class="text-slate-400 text-sm">All clear — nothing needs attention.</p>
            </div>
            <?php else: ?>
            <div class="space-y-2">
                <?php foreach ($alerts as $a): ?>
                <a href="<?= htmlspecialchars($a['link'], ENT_QUOTES, 'UTF-8') ?>"
                   class="flex items-start gap-3 p-3 rounded-lg border border-<?= $a['color'] ?>-200 bg-<?= $a['color'] ?>-50 hover:bg-<?= $a['color'] ?>-100 transition group">
                    <i class="ti <?= $a['icon'] ?> text-<?= $a['color'] ?>-600 text-lg shrink-0 mt-0.5"></i>
                    <span class="text-sm text-<?= $a['color'] ?>-800 flex-1"><?= htmlspecialchars($a['text'], ENT_QUOTES, 'UTF-8') ?></span>
                    <i class="ti ti-chevron-right text-<?= $a['color'] ?>-400 text-sm shrink-0 mt-0.5 group-hover:translate-x-0.5 transition-transform"></i>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Activity feed -->
        <div class="reveal bg-white rounded-lg shadow-sm border border-slate-200 p-6 lg:col-span-2" style="--delay: 90ms">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-medium text-slate-500 uppercase tracking-wide">Recent Activity</p>
                <span class="flex items-center gap-1.5 text-xs text-slate-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Live
                </span>
            </div>
            <?php if (empty($activity)): ?>
            <p class="text-slate-400 text-sm text-center py-10">No recent activity yet.</p>
            <?php else: ?>
            <div class="space-y-0.5">
                <?php foreach ($activity as $i => $ev):
                    $meta = $kindMeta[$ev['KIND']] ?? ['icon' => 'ti-dot', 'color' => 'slate'];
                ?>
                <div class="flex items-start gap-3 py-2.5 <?= $i < count($activity) - 1 ? 'border-b border-slate-50' : '' ?>">
                    <div class="w-8 h-8 rounded-full bg-<?= $meta['color'] ?>-100 flex items-center justify-center shrink-0">
                        <i class="ti <?= $meta['icon'] ?> text-<?= $meta['color'] ?>-600 text-sm"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-slate-700"><?= htmlspecialchars($ev['HEADLINE'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="text-xs text-slate-400"><?= htmlspecialchars($ev['DETAIL'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <span class="text-xs text-slate-400 shrink-0 whitespace-nowrap" data-timestamp="<?= htmlspecialchars($ev['OCCURRED_AT'], ENT_QUOTES, 'UTF-8') ?>">
                        <?= timeAgo($ev['OCCURRED_AT']) ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sample KPI cards, purely to demonstrate the entrance animation -->
        <div class="reveal bg-white rounded-lg shadow-sm border border-slate-200 p-5" style="--delay: 180ms">
            <p class="text-xs text-slate-500 uppercase tracking-wide font-medium mb-2">Example Card A</p>
            <p class="text-2xl font-bold text-slate-800">128</p>
            <p class="text-xs text-slate-400 mt-1">Staggered entrance demo</p>
        </div>
        <div class="reveal bg-white rounded-lg shadow-sm border border-slate-200 p-5" style="--delay: 240ms">
            <p class="text-xs text-slate-500 uppercase tracking-wide font-medium mb-2">Example Card B</p>
            <p class="text-2xl font-bold text-slate-800">42</p>
            <p class="text-xs text-slate-400 mt-1">Staggered entrance demo</p>
        </div>
        <div class="reveal bg-white rounded-lg shadow-sm border border-slate-200 p-5" style="--delay: 300ms">
            <p class="text-xs text-slate-500 uppercase tracking-wide font-medium mb-2">Example Card C</p>
            <p class="text-2xl font-bold text-slate-800">RM 9,300</p>
            <p class="text-xs text-slate-400 mt-1">Staggered entrance demo</p>
        </div>
    </div>

    <div class="mt-6 bg-slate-50 border border-slate-200 rounded-lg p-4 text-xs text-slate-500">
        <p class="font-medium text-slate-600 mb-1">What's real here vs. decorative:</p>
        <ul class="list-disc list-inside space-y-0.5">
            <li><strong>Activity feed</strong> — genuinely queries CLASS_STUDENT.ENROLLED_AT, PAYMENT.CREATED_AT, STUDENT.CREATED_AT, and STUDENT_ATTENDANCE (status=ABSENT) via UNION ALL, ordered by real timestamps.</li>
            <li><strong>Alerts card</strong> — overdue invoices and low-attendance (HAVING, same query shape as the real Attendance Report) are live counts from the DB; "idle tutors" is a new NOT IN anti-join example.</li>
            <li><strong>Entrance animation</strong> — pure CSS, respects <code>prefers-reduced-motion</code>, no data dependency.</li>
            <li><strong>"Live" pulse dot</strong> — purely decorative in this demo (no polling/websocket) — signals "this section refreshes" without actually being real-time.</li>
        </ul>
    </div>
</main>

<style>
@media (prefers-reduced-motion: no-preference) {
    .reveal {
        animation: reveal-in 0.5s ease-out both;
        animation-delay: var(--delay, 0ms);
    }
    @keyframes reveal-in {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
}
</style>

<script>
// Re-derive "time ago" client-side every 30s so it keeps counting up without a page reload
(function () {
    var els = document.querySelectorAll('[data-timestamp]');
    if (!els.length) return;

    function relativeTime(iso) {
        var then = new Date(iso.replace(' ', 'T')).getTime();
        var diff = Math.floor((Date.now() - then) / 1000);
        if (diff < 60) return 'just now';
        if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
        if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
        return Math.floor(diff / 86400) + 'd ago';
    }

    function tick() {
        els.forEach(function (el) {
            var ts = el.getAttribute('data-timestamp');
            if (ts) el.textContent = relativeTime(ts);
        });
    }

    setInterval(tick, 30000);
})();
</script>

<?php require_once '../../views/layout/footer.php'; ?>
