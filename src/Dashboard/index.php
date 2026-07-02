<?php

session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/login');
    exit;
}

$role = $_SESSION['role'];

if ($role === 'OWNER') {
    try {
        $conn = getConnection();

        // Top KPI cards
        $kpiSql = "SELECT
                        (SELECT COUNT(*) FROM STUDENT WHERE STATUS = 'ACTIVE')  AS ACTIVE_STUDENTS,
                        (SELECT COUNT(*) FROM CLASS   WHERE STATUS = 'ACTIVE') AS ACTIVE_CLASSES,
                        (SELECT COUNT(*) FROM USERS    WHERE ROLE = 'TUTOR')    AS TOTAL_TUTORS,
                        (SELECT COUNT(*) FROM USERS    WHERE ROLE = 'ADMIN')    AS TOTAL_ADMINS
                    FROM DUAL";
        $kpiStmt = oci_parse($conn, $kpiSql);
        oci_execute($kpiStmt);
        $kpi = oci_fetch_assoc($kpiStmt);
        oci_free_statement($kpiStmt);

        // This-month revenue collected
        $revSql = "SELECT NVL(SUM(pay.amount_paid), 0) AS THIS_MONTH_REVENUE
                    FROM   PAYMENT pay
                    WHERE  TO_CHAR(pay.payment_date, 'YYYY-MM') = TO_CHAR(SYSDATE, 'YYYY-MM')";
        $revStmt = oci_parse($conn, $revSql);
        oci_execute($revStmt);
        $thisMonthRevenue = (float)oci_fetch_assoc($revStmt)['THIS_MONTH_REVENUE'];
        oci_free_statement($revStmt);

        // Revenue trend — last 6 months of payments collected
        $trendSql = "SELECT TO_CHAR(pay.payment_date, 'YYYY-MM') AS YM,
                            TO_CHAR(pay.payment_date, 'Mon YYYY') AS YM_LABEL,
                            SUM(pay.amount_paid) AS TOTAL
                     FROM   PAYMENT pay
                     WHERE  pay.payment_date >= ADD_MONTHS(TRUNC(SYSDATE, 'MM'), -5)
                     GROUP  BY TO_CHAR(pay.payment_date, 'YYYY-MM'), TO_CHAR(pay.payment_date, 'Mon YYYY')
                     ORDER  BY YM";
        $trendStmt = oci_parse($conn, $trendSql);
        oci_execute($trendStmt);
        $revenueTrend = [];
        while ($r = oci_fetch_assoc($trendStmt)) $revenueTrend[] = $r;
        oci_free_statement($trendStmt);

        // Invoice status breakdown
        $invSql = "SELECT i.status, COUNT(*) AS CNT, SUM(i.total_amount) AS TOTAL
                    FROM   INVOICE i
                    GROUP  BY i.status";
        $invStmt = oci_parse($conn, $invSql);
        oci_execute($invStmt);
        $invoiceSummary = ['UNPAID' => 0, 'PARTIAL' => 0, 'PAID' => 0, 'OVERDUE' => 0];
        while ($r = oci_fetch_assoc($invStmt)) {
            $invoiceSummary[$r['STATUS']] = (int)$r['CNT'];
        }
        oci_free_statement($invStmt);

        // Students by grade
        $gradeSql = "SELECT g.name AS GRADE_NAME, COUNT(s.student_id) AS CNT
                     FROM   GRADE g
                     LEFT   JOIN STUDENT s ON s.grade_id = g.grade_id AND s.status = 'ACTIVE'
                     GROUP  BY g.name, g.grade_level
                     ORDER  BY g.grade_level";
        $gradeStmt = oci_parse($conn, $gradeSql);
        oci_execute($gradeStmt);
        $byGrade = [];
        while ($r = oci_fetch_assoc($gradeStmt)) $byGrade[] = $r;
        oci_free_statement($gradeStmt);

        // Class utilization — top 5 by enrolment
        $classSql = "SELECT c.name AS CLASS_NAME, c.max_students AS MAX_STUDENTS,
                            COUNT(cst.student_id) AS ENROLLED
                     FROM   CLASS c
                     LEFT   JOIN CLASS_STUDENT cst ON cst.class_id = c.class_id
                     WHERE  c.status = 'ACTIVE'
                     GROUP  BY c.class_id, c.name, c.max_students
                     ORDER  BY COUNT(cst.student_id) DESC
                     OFFSET 0 ROWS FETCH NEXT 5 ROWS ONLY";
        $classStmt = oci_parse($conn, $classSql);
        oci_execute($classStmt);
        $classUtil = [];
        while ($r = oci_fetch_assoc($classStmt)) $classUtil[] = $r;
        oci_free_statement($classStmt);

        // Attendance snapshot — last 30 days
        $attSql = "SELECT sa.status, COUNT(*) AS CNT
                   FROM   STUDENT_ATTENDANCE sa
                   JOIN   CLASS_SESSION cs ON cs.session_id = sa.session_id
                   WHERE  cs.session_date >= TRUNC(SYSDATE) - 30
                   GROUP  BY sa.status";
        $attStmt = oci_parse($conn, $attSql);
        oci_execute($attStmt);
        $attendance = ['PRESENT' => 0, 'ABSENT' => 0, 'LATE' => 0];
        while ($r = oci_fetch_assoc($attStmt)) $attendance[$r['STATUS']] = (int)$r['CNT'];
        oci_free_statement($attStmt);
        $totalAttendance = $attendance['PRESENT'] + $attendance['ABSENT'] + $attendance['LATE'];
        $attendanceRate  = $totalAttendance > 0 ? round($attendance['PRESENT'] / $totalAttendance * 100) : 0;

        // Prior-period KPI comparisons — trend badges
        $prevKpiSql = "SELECT
                            (SELECT COUNT(*) FROM STUDENT WHERE STATUS = 'ACTIVE' AND created_at < TRUNC(SYSDATE, 'MM')) AS PREV_STUDENTS,
                            (SELECT COUNT(*) FROM CLASS   WHERE STATUS = 'ACTIVE' AND created_at < TRUNC(SYSDATE, 'MM')) AS PREV_CLASSES
                        FROM DUAL";
        $prevKpiStmt = oci_parse($conn, $prevKpiSql);
        oci_execute($prevKpiStmt);
        $prevKpi = oci_fetch_assoc($prevKpiStmt);
        oci_free_statement($prevKpiStmt);

        $prevRevSql = "SELECT NVL(SUM(pay.amount_paid), 0) AS PREV_REVENUE
                        FROM   PAYMENT pay
                        WHERE  TO_CHAR(pay.payment_date, 'YYYY-MM') = TO_CHAR(ADD_MONTHS(SYSDATE, -1), 'YYYY-MM')";
        $prevRevStmt = oci_parse($conn, $prevRevSql);
        oci_execute($prevRevStmt);
        $prevRevenue = (float)oci_fetch_assoc($prevRevStmt)['PREV_REVENUE'];
        oci_free_statement($prevRevStmt);

        function pctChange(float $current, float $previous): ?float {
            if ($previous <= 0) return null;
            return round((($current - $previous) / $previous) * 100);
        }
        $studentsTrend = pctChange((int)$kpi['ACTIVE_STUDENTS'], (int)$prevKpi['PREV_STUDENTS']);
        $classesTrend  = pctChange((int)$kpi['ACTIVE_CLASSES'],  (int)$prevKpi['PREV_CLASSES']);
        $revenueTrendPct = pctChange($thisMonthRevenue, $prevRevenue);

        // Fee collection rate — this month
        $collectSql = "SELECT
                            NVL(SUM(i.total_amount), 0) AS INVOICED,
                            NVL(SUM(pay.amount_paid), 0) AS COLLECTED
                        FROM   INVOICE i
                        LEFT   JOIN PAYMENT pay ON pay.invoice_id = i.invoice_id
                        WHERE  i.billing_month = TO_NUMBER(TO_CHAR(SYSDATE, 'MM'))
                        AND    i.billing_year  = TO_NUMBER(TO_CHAR(SYSDATE, 'YYYY'))";
        $collectStmt = oci_parse($conn, $collectSql);
        oci_execute($collectStmt);
        $collectRow = oci_fetch_assoc($collectStmt);
        oci_free_statement($collectStmt);
        $invoicedThisMonth  = (float)$collectRow['INVOICED'];
        $collectedThisMonth = (float)$collectRow['COLLECTED'];
        $collectionRate = $invoicedThisMonth > 0 ? min(100, round($collectedThisMonth / $invoicedThisMonth * 100)) : 0;

        // Class capacity — average enrolment across active classes
        $capSql = "SELECT NVL(ROUND(AVG(
                        (SELECT COUNT(*) FROM CLASS_STUDENT cst WHERE cst.class_id = c.class_id)
                        / NULLIF(c.max_students, 0)
                    ) * 100), 0) AS AVG_CAPACITY
                   FROM CLASS c
                   WHERE c.status = 'ACTIVE'";
        $capStmt = oci_parse($conn, $capSql);
        oci_execute($capStmt);
        $avgCapacity = min(100, (int)oci_fetch_assoc($capStmt)['AVG_CAPACITY']);
        oci_free_statement($capStmt);

        // Today's agenda
        $todaySql = "SELECT cs.session_id, cs.start_time, cs.end_time, c.name AS CLASS_NAME
                     FROM   CLASS_SESSION cs
                     JOIN   CLASS c ON c.class_id = cs.class_id
                     WHERE  cs.session_date = TRUNC(SYSDATE)
                     AND    cs.status = 'SCHEDULED'
                     ORDER  BY cs.start_time";
        $todayStmt = oci_parse($conn, $todaySql);
        oci_execute($todayStmt);
        $todaySessions = [];
        while ($r = oci_fetch_assoc($todayStmt)) $todaySessions[] = $r;
        oci_free_statement($todayStmt);
        $nowTime = date('H:i');
        $nextSession = null;
        foreach ($todaySessions as $s) {
            if ($s['START_TIME'] >= $nowTime) { $nextSession = $s; break; }
        }

        // Revenue vs Collections — dual line, last 6 months
        $dualSql = "SELECT TO_CHAR(m.mth, 'YYYY-MM') AS YM, TO_CHAR(m.mth, 'Mon YYYY') AS YM_LABEL,
                           NVL(inv.invoiced, 0) AS INVOICED, NVL(pay.collected, 0) AS COLLECTED
                    FROM (
                        SELECT ADD_MONTHS(TRUNC(SYSDATE, 'MM'), -LEVEL + 1) AS mth
                        FROM DUAL CONNECT BY LEVEL <= 6
                    ) m
                    LEFT JOIN (
                        SELECT billing_year, billing_month, SUM(total_amount) AS invoiced
                        FROM   INVOICE
                        GROUP  BY billing_year, billing_month
                    ) inv ON inv.billing_year = TO_NUMBER(TO_CHAR(m.mth, 'YYYY'))
                         AND inv.billing_month = TO_NUMBER(TO_CHAR(m.mth, 'MM'))
                    LEFT JOIN (
                        SELECT TO_CHAR(payment_date, 'YYYY-MM') AS ym, SUM(amount_paid) AS collected
                        FROM   PAYMENT
                        GROUP  BY TO_CHAR(payment_date, 'YYYY-MM')
                    ) pay ON pay.ym = TO_CHAR(m.mth, 'YYYY-MM')
                    ORDER  BY m.mth";
        $dualStmt = oci_parse($conn, $dualSql);
        oci_execute($dualStmt);
        $dualTrend = [];
        while ($r = oci_fetch_assoc($dualStmt)) $dualTrend[] = $r;
        oci_free_statement($dualStmt);

        oci_close($conn);
    } catch (\RuntimeException $e) {
        $kpi = ['ACTIVE_STUDENTS' => 0, 'ACTIVE_CLASSES' => 0, 'TOTAL_TUTORS' => 0, 'TOTAL_ADMINS' => 0];
        $thisMonthRevenue = 0.0;
        $revenueTrend = [];
        $invoiceSummary = ['UNPAID' => 0, 'PARTIAL' => 0, 'PAID' => 0, 'OVERDUE' => 0];
        $byGrade = [];
        $classUtil = [];
        $attendance = ['PRESENT' => 0, 'ABSENT' => 0, 'LATE' => 0];
        $totalAttendance = 0; $attendanceRate = 0;
        $studentsTrend = null; $classesTrend = null; $revenueTrendPct = null;
        $collectionRate = 0; $invoicedThisMonth = 0.0; $collectedThisMonth = 0.0;
        $avgCapacity = 0;
        $todaySessions = []; $nextSession = null;
        $dualTrend = [];
    }
}

$pageTitle = 'Dashboard — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="pt-14 md:pt-0 md:ml-64 p-4 sm:p-8 min-h-screen">
    <div class="mb-6 flex items-center gap-3">
        <div class="w-10 h-10 shrink-0 rounded-full bg-amber-100 flex items-center justify-center">
            <i class="ti ti-sun text-amber-600 text-lg"></i>
        </div>
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Dashboard</h1>
            <p class="text-slate-500 text-sm mt-1">
                Welcome back, <?= htmlspecialchars($_SESSION['fullname'], ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>
    </div>

    <?php require_once '../../views/partials/flash.php'; ?>

    <?php if ($role === 'OWNER'): ?>

    <?php
        function trendBadge(?float $pct): string {
            if ($pct === null) return '';
            $up = $pct >= 0;
            $bg = $up ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
            $icon = $up ? 'ti-trending-up' : 'ti-trending-down';
            return '<span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full text-xs font-semibold ' . $bg . '">'
                 . '<i class="ti ' . $icon . ' text-sm"></i>' . ($up ? '+' : '') . (int)$pct . '%</span>';
        }
    ?>

    <!-- KPI cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs text-slate-500 uppercase tracking-wide font-medium">Active Students</p>
                <?= trendBadge($studentsTrend) ?>
            </div>
            <p class="text-2xl font-bold text-slate-800 mb-3"><?= (int)$kpi['ACTIVE_STUDENTS'] ?></p>
            <div class="flex items-end gap-1 h-8">
                <?php foreach ([40, 55, 45, 65, 70, 60, 80] as $i => $v): ?>
                <div class="flex-1 rounded-sm <?= $i === 6 ? 'bg-indigo-700' : 'bg-indigo-100' ?>" style="height: <?= $v ?>%"></div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs text-slate-500 uppercase tracking-wide font-medium">Active Classes</p>
                <?= trendBadge($classesTrend) ?>
            </div>
            <p class="text-2xl font-bold text-slate-800 mb-3"><?= (int)$kpi['ACTIVE_CLASSES'] ?></p>
            <div class="flex items-end gap-1 h-8">
                <?php foreach ([50, 45, 60, 55, 70, 65, 75] as $i => $v): ?>
                <div class="flex-1 rounded-sm <?= $i === 6 ? 'bg-indigo-700' : 'bg-indigo-100' ?>" style="height: <?= $v ?>%"></div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs text-slate-500 uppercase tracking-wide font-medium">Revenue This Month</p>
                <?= trendBadge($revenueTrendPct) ?>
            </div>
            <p class="text-2xl font-bold text-slate-800 mb-3">RM <?= number_format($thisMonthRevenue, 0) ?></p>
            <div class="flex items-end gap-1 h-8">
                <?php
                $sparkVals = array_map(fn($r) => (float)$r['TOTAL'], $revenueTrend);
                $sparkMax  = max(1, ...($sparkVals ?: [1]));
                $sparkCount = count($sparkVals);
                for ($i = 0; $i < $sparkCount; $i++):
                    $pct = max(8, round($sparkVals[$i] / $sparkMax * 100));
                ?>
                <div class="flex-1 rounded-sm <?= $i === $sparkCount - 1 ? 'bg-indigo-700' : 'bg-indigo-100' ?>" style="height: <?= $pct ?>%"></div>
                <?php endfor; ?>
                <?php if ($sparkCount === 0): ?>
                <span class="text-xs text-slate-300">No data yet</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs text-slate-500 uppercase tracking-wide font-medium">Unpaid + Overdue</p>
                <?php $urgentCount = (int)$invoiceSummary['UNPAID'] + (int)$invoiceSummary['OVERDUE']; ?>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold <?= $urgentCount > 0 ? 'bg-orange-100 text-orange-700' : 'bg-green-100 text-green-700' ?>">
                    <?= $urgentCount > 0 ? $urgentCount . ' open' : 'All clear' ?>
                </span>
            </div>
            <p class="text-2xl font-bold text-slate-800 mb-3"><?= $urgentCount ?></p>
            <div class="flex items-end gap-1 h-8">
                <?php
                $invBreak = [(int)$invoiceSummary['PAID'], (int)$invoiceSummary['PARTIAL'], (int)$invoiceSummary['UNPAID'], (int)$invoiceSummary['OVERDUE']];
                $invMax = max(1, ...$invBreak);
                $invColors = ['bg-green-200', 'bg-yellow-200', 'bg-slate-200', 'bg-orange-400'];
                foreach ($invBreak as $i => $v):
                    $pct = max(8, round($v / $invMax * 100));
                ?>
                <div class="flex-1 rounded-sm <?= $invColors[$i] ?>" style="height: <?= $pct ?>%"></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <!-- Revenue trend line chart -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 lg:col-span-2">
            <p class="text-sm font-medium text-slate-500 uppercase tracking-wide mb-4">Revenue — Last 6 Months</p>
            <?php if (empty($revenueTrend)): ?>
            <p class="text-slate-400 text-sm text-center py-10">No payment data yet.</p>
            <?php else: ?>
            <div class="h-48"><canvas id="chart-revenue-trend"></canvas></div>
            <?php endif; ?>
        </div>

        <!-- Attendance donut -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500 uppercase tracking-wide mb-4">Attendance — Last 30 Days</p>
            <?php if ($totalAttendance === 0): ?>
            <p class="text-slate-400 text-sm text-center py-10">No attendance data yet.</p>
            <?php else: ?>
            <div class="relative h-36 mb-4">
                <canvas id="chart-attendance-donut"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <p class="text-2xl font-bold text-slate-800"><?= $attendanceRate ?>%</p>
                    <p class="text-xs text-slate-400">present</p>
                </div>
            </div>
            <div class="space-y-1.5 text-sm">
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-2 text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-green-500"></span>Present</span>
                    <span class="font-medium text-slate-800"><?= $attendance['PRESENT'] ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-2 text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>Absent</span>
                    <span class="font-medium text-slate-800"><?= $attendance['ABSENT'] ?></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center gap-2 text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span>Late</span>
                    <span class="font-medium text-slate-800"><?= $attendance['LATE'] ?></span>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <!-- Fee collection gauge -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500 uppercase tracking-wide mb-4">Fee Collection Rate</p>
            <?php if ($invoicedThisMonth == 0.0): ?>
            <p class="text-slate-400 text-sm text-center py-10">No invoices this month yet.</p>
            <?php else: ?>
            <div class="relative h-32 mb-2">
                <canvas id="chart-collection-gauge"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-end pb-1 pointer-events-none">
                    <p class="text-2xl font-bold text-slate-800"><?= $collectionRate ?>%</p>
                    <p class="text-xs text-slate-400">collected</p>
                </div>
            </div>
            <p class="text-xs text-slate-400 text-center">RM <?= number_format($collectedThisMonth, 0) ?> of RM <?= number_format($invoicedThisMonth, 0) ?> invoiced</p>
            <?php endif; ?>
        </div>

        <!-- Class capacity gauge -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500 uppercase tracking-wide mb-4">Avg. Class Capacity</p>
            <?php if (empty($classUtil)): ?>
            <p class="text-slate-400 text-sm text-center py-10">No active classes yet.</p>
            <?php else: ?>
            <div class="relative h-32 mb-2">
                <canvas id="chart-capacity-gauge"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-end pb-1 pointer-events-none">
                    <p class="text-2xl font-bold text-slate-800"><?= $avgCapacity ?>%</p>
                    <p class="text-xs text-slate-400">filled</p>
                </div>
            </div>
            <p class="text-xs text-slate-400 text-center">Across <?= count($classUtil) ?> active classes</p>
            <?php endif; ?>
        </div>

        <!-- Quick actions -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500 uppercase tracking-wide mb-4">Quick Actions</p>
            <div class="grid grid-cols-2 gap-2">
                <a href="/PTE-MANAGEMENT-SYSTEM/students/create"
                   class="flex flex-col items-center justify-center gap-1.5 rounded-lg border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 transition p-3 text-center">
                    <i class="ti ti-user-plus text-indigo-700 text-lg"></i>
                    <span class="text-xs font-medium text-slate-700">Add Student</span>
                </a>
                <a href="/PTE-MANAGEMENT-SYSTEM/invoices/generate"
                   class="flex flex-col items-center justify-center gap-1.5 rounded-lg border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 transition p-3 text-center">
                    <i class="ti ti-file-plus text-indigo-700 text-lg"></i>
                    <span class="text-xs font-medium text-slate-700">Generate Invoice</span>
                </a>
                <a href="/PTE-MANAGEMENT-SYSTEM/invoices"
                   class="flex flex-col items-center justify-center gap-1.5 rounded-lg border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 transition p-3 text-center">
                    <i class="ti ti-cash text-indigo-700 text-lg"></i>
                    <span class="text-xs font-medium text-slate-700">Record Payment</span>
                </a>
                <a href="/PTE-MANAGEMENT-SYSTEM/schedule/generate"
                   class="flex flex-col items-center justify-center gap-1.5 rounded-lg border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 transition p-3 text-center">
                    <i class="ti ti-calendar-plus text-indigo-700 text-lg"></i>
                    <span class="text-xs font-medium text-slate-700">Generate Sessions</span>
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <!-- Today's agenda -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-medium text-slate-500 uppercase tracking-wide">Today's Agenda</p>
                <span class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center">
                    <i class="ti ti-calendar-event text-amber-600 text-sm"></i>
                </span>
            </div>
            <?php if (empty($todaySessions)): ?>
            <p class="text-slate-400 text-sm text-center py-6">No sessions scheduled today.</p>
            <?php else: ?>
            <p class="text-2xl font-bold text-slate-800 mb-1"><?= count($todaySessions) ?> session<?= count($todaySessions) === 1 ? '' : 's' ?></p>
            <?php if ($nextSession): ?>
            <div class="mt-3 pt-3 border-t border-slate-100">
                <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Next up</p>
                <p class="text-sm font-medium text-slate-700"><?= htmlspecialchars($nextSession['CLASS_NAME'], ENT_QUOTES, 'UTF-8') ?></p>
                <p class="text-xs text-slate-500"><?= htmlspecialchars($nextSession['START_TIME'], ENT_QUOTES, 'UTF-8') ?> – <?= htmlspecialchars($nextSession['END_TIME'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <?php else: ?>
            <p class="text-xs text-slate-400 mt-3 pt-3 border-t border-slate-100">All of today's sessions are done.</p>
            <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Students by grade -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500 uppercase tracking-wide mb-4">Students by Grade</p>
            <?php if (empty($byGrade)): ?>
            <p class="text-slate-400 text-sm text-center py-6">No grades set up.</p>
            <?php else: ?>
            <div class="h-40"><canvas id="chart-grade-bars"></canvas></div>
            <?php endif; ?>
        </div>

        <!-- Top classes by enrolment -->
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500 uppercase tracking-wide mb-4">Top Classes by Enrolment</p>
            <?php if (empty($classUtil)): ?>
            <p class="text-slate-400 text-sm text-center py-6">No active classes yet.</p>
            <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($classUtil as $c): ?>
                <?php
                    $max = max(1, (int)$c['MAX_STUDENTS']);
                    $enrolled = (int)$c['ENROLLED'];
                    $pct = min(100, round($enrolled / $max * 100));
                    $barColor = $pct >= 90 ? 'bg-red-500' : ($pct >= 60 ? 'bg-indigo-700' : 'bg-green-500');
                ?>
                <div>
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="text-slate-700 font-medium truncate pr-2"><?= htmlspecialchars($c['CLASS_NAME'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="text-slate-500 shrink-0"><?= $enrolled ?> / <?= $max ?></span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="<?= $barColor ?> h-2 rounded-full" style="width: <?= $pct ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Revenue vs Collections dual-line -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm font-medium text-slate-500 uppercase tracking-wide">Invoiced vs Collected — Last 6 Months</p>
            <div class="flex items-center gap-4 text-xs">
                <span class="flex items-center gap-1.5 text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-indigo-700"></span>Invoiced</span>
                <span class="flex items-center gap-1.5 text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>Collected</span>
            </div>
        </div>
        <?php if (empty($dualTrend)): ?>
        <p class="text-slate-400 text-sm text-center py-10">No billing data yet.</p>
        <?php else: ?>
        <div class="h-56"><canvas id="chart-dual-line"></canvas></div>
        <?php endif; ?>
    </div>

    <?php
        $chartLabels = array_map(fn($r) => $r['YM_LABEL'], $revenueTrend);
        $chartData   = array_map(fn($r) => (float)$r['TOTAL'], $revenueTrend);
        $gradeLabels = array_map(fn($g) => $g['GRADE_NAME'], $byGrade);
        $gradeData   = array_map(fn($g) => (int)$g['CNT'], $byGrade);
        $dualLabels  = array_map(fn($r) => $r['YM_LABEL'], $dualTrend);
        $dualInvoiced = array_map(fn($r) => (float)$r['INVOICED'], $dualTrend);
        $dualCollected = array_map(fn($r) => (float)$r['COLLECTED'], $dualTrend);
        $prefersReducedMotion = false; // detected client-side; PHP just supplies data
    ?>
    <script>
    (function () {
        var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var animDuration = reduceMotion ? 0 : 600;
        Chart.defaults.font.family = "system-ui, -apple-system, Segoe UI, Roboto, sans-serif";
        Chart.defaults.color = '#64748b';

        var revenueEl = document.getElementById('chart-revenue-trend');
        if (revenueEl) {
            new Chart(revenueEl, {
                type: 'line',
                data: {
                    labels: <?= json_encode($chartLabels) ?>,
                    datasets: [{
                        data: <?= json_encode($chartData) ?>,
                        borderColor: '#4338ca',
                        backgroundColor: 'rgba(67, 56, 202, 0.08)',
                        borderWidth: 2,
                        tension: 0.35,
                        fill: true,
                        pointRadius: 0,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: '#4338ca',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 2,
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    animation: { duration: animDuration, easing: 'easeOutQuart' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#ffffff', titleColor: '#1e293b', bodyColor: '#1e293b',
                            borderColor: '#e2e8f0', borderWidth: 1, padding: 10, cornerRadius: 8,
                            displayColors: false,
                            callbacks: { label: function (ctx) { return 'RM ' + ctx.parsed.y.toLocaleString(); } }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                        y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, callback: function (v) { return 'RM ' + v.toLocaleString(); } } }
                    }
                }
            });
        }

        var attendanceEl = document.getElementById('chart-attendance-donut');
        if (attendanceEl) {
            new Chart(attendanceEl, {
                type: 'doughnut',
                data: {
                    labels: ['Present', 'Absent', 'Late'],
                    datasets: [{
                        data: [<?= (int)$attendance['PRESENT'] ?>, <?= (int)$attendance['ABSENT'] ?>, <?= (int)$attendance['LATE'] ?>],
                        backgroundColor: ['#22c55e', '#ef4444', '#eab308'],
                        borderWidth: 0,
                        cutout: '75%',
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    animation: { duration: animDuration, easing: 'easeOutQuart' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#ffffff', titleColor: '#1e293b', bodyColor: '#1e293b',
                            borderColor: '#e2e8f0', borderWidth: 1, padding: 10, cornerRadius: 8,
                        }
                    }
                }
            });
        }

        function renderGauge(elId, value) {
            var el = document.getElementById(elId);
            if (!el) return;
            new Chart(el, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [value, 100 - value],
                        backgroundColor: ['#4338ca', '#e0e7ff'],
                        borderWidth: 0,
                        circumference: 180,
                        rotation: 270,
                        cutout: '75%',
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    animation: { duration: animDuration, easing: 'easeOutQuart' },
                    plugins: { legend: { display: false }, tooltip: { enabled: false } }
                }
            });
        }
        renderGauge('chart-collection-gauge', <?= (int)$collectionRate ?>);
        renderGauge('chart-capacity-gauge', <?= (int)$avgCapacity ?>);

        var gradeEl = document.getElementById('chart-grade-bars');
        if (gradeEl) {
            new Chart(gradeEl, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($gradeLabels) ?>,
                    datasets: [{
                        data: <?= json_encode($gradeData) ?>,
                        backgroundColor: '#4338ca',
                        borderRadius: 4,
                        maxBarThickness: 28,
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    animation: { duration: animDuration, easing: 'easeOutQuart' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#ffffff', titleColor: '#1e293b', bodyColor: '#1e293b',
                            borderColor: '#e2e8f0', borderWidth: 1, padding: 10, cornerRadius: 8, displayColors: false,
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                        y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, precision: 0 } }
                    }
                }
            });
        }

        var dualEl = document.getElementById('chart-dual-line');
        if (dualEl) {
            new Chart(dualEl, {
                type: 'line',
                data: {
                    labels: <?= json_encode($dualLabels) ?>,
                    datasets: [
                        {
                            label: 'Invoiced',
                            data: <?= json_encode($dualInvoiced) ?>,
                            borderColor: '#4338ca',
                            backgroundColor: 'transparent',
                            borderWidth: 2, tension: 0.35, pointRadius: 0, pointHoverRadius: 5,
                            pointHoverBackgroundColor: '#4338ca', pointHoverBorderColor: '#ffffff', pointHoverBorderWidth: 2,
                        },
                        {
                            label: 'Collected',
                            data: <?= json_encode($dualCollected) ?>,
                            borderColor: '#d97706',
                            backgroundColor: 'transparent',
                            borderWidth: 2, tension: 0.35, pointRadius: 0, pointHoverRadius: 5,
                            pointHoverBackgroundColor: '#d97706', pointHoverBorderColor: '#ffffff', pointHoverBorderWidth: 2,
                        }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    animation: { duration: animDuration, easing: 'easeOutQuart' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#ffffff', titleColor: '#1e293b', bodyColor: '#1e293b',
                            borderColor: '#e2e8f0', borderWidth: 1, padding: 10, cornerRadius: 8,
                            callbacks: { label: function (ctx) { return ctx.dataset.label + ': RM ' + ctx.parsed.y.toLocaleString(); } }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                        y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, callback: function (v) { return 'RM ' + v.toLocaleString(); } } }
                    }
                }
            });
        }
    })();
    </script>

    <?php else: ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="ti ti-school text-indigo-800 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wide font-medium">Students</p>
                    <p class="text-2xl font-bold text-slate-800">—</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="ti ti-books text-indigo-800 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wide font-medium">Classes</p>
                    <p class="text-2xl font-bold text-slate-800">—</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="ti ti-file-invoice text-indigo-800 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wide font-medium">Unpaid Invoices</p>
                    <p class="text-2xl font-bold text-slate-800">—</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="ti ti-users text-indigo-800 text-xl"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-500 uppercase tracking-wide font-medium">Tutors</p>
                    <p class="text-2xl font-bold text-slate-800">—</p>
                </div>
            </div>
        </div>
    </div>

    <?php endif; ?>
</main>

<?php require_once '../../views/layout/footer.php'; ?>
