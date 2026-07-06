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
$input   = ['fullname' => '', 'ic_number' => '', 'phone' => '', 'status' => 'ACTIVE', 'grade_id' => '', 'parent_id' => ''];
$grades  = [];
$parents = [];

try {
    $conn = getConnection();

    $gStmt = oci_parse($conn, 'SELECT grade_id, name, grade_level FROM GRADE ORDER BY grade_level');
    oci_execute($gStmt);
    while ($r = oci_fetch_assoc($gStmt)) $grades[] = $r;
    oci_free_statement($gStmt);

    $pStmt = oci_parse($conn, 'SELECT parent_id, fullname, phone FROM PARENT ORDER BY fullname');
    oci_execute($pStmt);
    while ($r = oci_fetch_assoc($pStmt)) $parents[] = $r;
    oci_free_statement($pStmt);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input['fullname']  = trim($_POST['fullname'] ?? '');
        $input['ic_number'] = trim($_POST['ic_number'] ?? '');
        $input['phone']     = trim($_POST['phone'] ?? '');
        $input['status']    = $_POST['status'] ?? 'ACTIVE';
        $input['grade_id']  = (int)($_POST['grade_id'] ?? 0);
        $input['parent_id'] = (int)($_POST['parent_id'] ?? 0);

        if ($input['fullname'] === '')  $errors['fullname']  = 'Full name is required.';
        if ($input['grade_id'] === 0)   $errors['grade_id']  = 'Grade is required.';
        if ($input['parent_id'] === 0)  $errors['parent_id'] = 'Parent is required.';
        if (!in_array($input['status'], ['ACTIVE', 'INACTIVE'])) $errors['status'] = 'Invalid status.';

        if (empty($errors['ic_number']) && $input['ic_number'] !== '') {
            $chkStmt = oci_parse($conn, 'SELECT COUNT(*) AS cnt FROM STUDENT WHERE ic_number = :ic');
            oci_bind_by_name($chkStmt, ':ic', $input['ic_number']);
            oci_execute($chkStmt);
            if ((int)oci_fetch_assoc($chkStmt)['CNT'] > 0) $errors['ic_number'] = 'IC number already registered.';
            oci_free_statement($chkStmt);
        }

        if (empty($errors)) {
            $ic   = $input['ic_number'] !== '' ? $input['ic_number'] : null;
            $ph   = $input['phone']     !== '' ? $input['phone']     : null;

            $sql  = 'INSERT INTO STUDENT (fullname, ic_number, phone, status, grade_id, parent_id)
                     VALUES (:fullname, :ic, :phone, :status, :grade_id, :parent_id)';
            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':fullname',  $input['fullname']);
            oci_bind_by_name($stmt, ':ic',        $ic);
            oci_bind_by_name($stmt, ':phone',     $ph);
            oci_bind_by_name($stmt, ':status',    $input['status']);
            oci_bind_by_name($stmt, ':grade_id',  $input['grade_id']);
            oci_bind_by_name($stmt, ':parent_id', $input['parent_id']);
            oci_execute($stmt);
            oci_commit($conn);
            oci_free_statement($stmt);
            oci_close($conn);

            $_SESSION['flash_success'] = 'Student "' . $input['fullname'] . '" added successfully.';
            header('Location: /PTE-MANAGEMENT-SYSTEM/students');
            exit;
        }
    }

    oci_close($conn);
} catch (\RuntimeException $e) {
    $errors['_general'] = 'Database error. Please try again.';
}

$pageTitle = 'Add Student — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="pt-16 md:pt-10 md:ml-64 px-4 sm:px-8 pb-4 sm:pb-8 min-h-screen">
    <div class="mb-6 flex items-center gap-3">
        <a href="/PTE-MANAGEMENT-SYSTEM/students" class="text-slate-400 hover:text-slate-600">
            <i class="ti ti-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Add Student</h1>
            <p class="text-slate-500 text-sm mt-1">Register a new student</p>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-5 text-sm">
        <div class="flex items-center gap-2 font-medium mb-1"><i class="ti ti-alert-circle"></i> Please fix the following:</div>
        <ul class="list-disc list-inside space-y-0.5">
            <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    <?php
        function fieldRing(array $errors, string $key): string {
            return isset($errors[$key])
                ? 'border-red-400 focus:ring-2 focus:ring-red-500 focus:border-red-500'
                : 'border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500';
        }
    ?>

    <?php if (empty($grades)): ?>
    <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-lg px-4 py-3 mb-5 text-sm flex items-center gap-2">
        <i class="ti ti-alert-circle"></i>
        No grades found. <a href="/PTE-MANAGEMENT-SYSTEM/grades" class="underline font-medium ml-1">Add grades first.</a>
    </div>
    <?php endif; ?>

    <?php if (empty($parents)): ?>
    <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-lg px-4 py-3 mb-5 text-sm flex items-center gap-2">
        <i class="ti ti-alert-circle"></i>
        No parents found. <a href="/PTE-MANAGEMENT-SYSTEM/parents/create" class="underline font-medium ml-1">Add a parent first.</a>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 max-w-xl">
        <form method="POST" action="/PTE-MANAGEMENT-SYSTEM/students/create" novalidate onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').innerHTML = '<i class=\'ti ti-loader-2 animate-spin\'></i> Adding…';">
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                <input type="text" name="fullname" value="<?= htmlspecialchars($input['fullname'], ENT_QUOTES, 'UTF-8') ?>"
                       class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'fullname') ?>"
                       aria-invalid="<?= isset($errors['fullname']) ? 'true' : 'false' ?>"
                       placeholder="e.g. Aina binti Ahmad">
                <?php if (isset($errors['fullname'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['fullname'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">IC Number</label>
                <input type="text" name="ic_number" id="ic-number-input" value="<?= htmlspecialchars($input['ic_number'], ENT_QUOTES, 'UTF-8') ?>"
                       class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'ic_number') ?>"
                       aria-invalid="<?= isset($errors['ic_number']) ? 'true' : 'false' ?>"
                       data-format="ic" inputmode="numeric" maxlength="14"
                       placeholder="e.g. 100101-01-1234">
                <?php if (isset($errors['ic_number'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['ic_number'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($input['phone'], ENT_QUOTES, 'UTF-8') ?>"
                       class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'phone') ?>"
                       placeholder="e.g. 0123456789">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Grade <span class="text-red-500">*</span></label>
                <select name="grade_id" id="grade-select" class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'grade_id') ?>"
                        aria-invalid="<?= isset($errors['grade_id']) ? 'true' : 'false' ?>">
                    <option value="">Select grade…</option>
                    <?php foreach ($grades as $g): ?>
                    <option value="<?= $g['GRADE_ID'] ?>" data-grade-level="<?= (int)$g['GRADE_LEVEL'] ?>"
                            <?= (int)$input['grade_id'] === (int)$g['GRADE_ID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['NAME'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <p id="grade-suggestion-hint" class="text-xs text-indigo-600 mt-1 hidden">
                    <i class="ti ti-sparkles text-xs"></i> Suggested based on IC — change if needed.
                </p>
                <?php if (isset($errors['grade_id'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['grade_id'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <?php
                $selectedParent = null;
                foreach ($parents as $p) {
                    if ((int)$p['PARENT_ID'] === (int)$input['parent_id']) { $selectedParent = $p; break; }
                }
            ?>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Parent / Guardian <span class="text-red-500">*</span></label>
                <div class="relative" id="parent-combobox">
                    <input type="hidden" name="parent_id" id="parent-id-input" value="<?= (int)$input['parent_id'] ?>">
                    <input type="text" id="parent-search-input" autocomplete="off" role="combobox"
                           aria-expanded="false" aria-controls="parent-options" aria-autocomplete="list"
                           value="<?= $selectedParent ? htmlspecialchars($selectedParent['FULLNAME'] . ' (' . $selectedParent['PHONE'] . ')', ENT_QUOTES, 'UTF-8') : '' ?>"
                           class="border rounded-lg pl-3 pr-9 py-2 w-full text-sm <?= fieldRing($errors, 'parent_id') ?>"
                           aria-invalid="<?= isset($errors['parent_id']) ? 'true' : 'false' ?>"
                           placeholder="Search parent by name or phone…">
                    <i class="ti ti-search text-slate-400 text-sm absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>

                    <ul id="parent-options" role="listbox"
                        class="hidden absolute z-10 mt-1 w-full max-h-56 overflow-y-auto bg-white border border-slate-200 rounded-lg shadow-lg py-1 text-sm">
                        <?php foreach ($parents as $p): ?>
                        <li role="option" data-id="<?= (int)$p['PARENT_ID'] ?>"
                            data-label="<?= htmlspecialchars($p['FULLNAME'] . ' (' . $p['PHONE'] . ')', ENT_QUOTES, 'UTF-8') ?>"
                            data-search="<?= htmlspecialchars(mb_strtolower($p['FULLNAME'] . ' ' . $p['PHONE']), ENT_QUOTES, 'UTF-8') ?>"
                            class="px-3 py-2 cursor-pointer hover:bg-indigo-50 text-slate-700">
                            <?= htmlspecialchars($p['FULLNAME'], ENT_QUOTES, 'UTF-8') ?>
                            <span class="text-slate-400">(<?= htmlspecialchars($p['PHONE'], ENT_QUOTES, 'UTF-8') ?>)</span>
                        </li>
                        <?php endforeach; ?>
                        <li id="parent-no-results" class="hidden px-3 py-2 text-slate-400">No matching parents.</li>
                    </ul>
                </div>
                <?php if (isset($errors['parent_id'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['parent_id'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                <select name="status" class="border border-slate-300 rounded-lg px-3 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="ACTIVE"   <?= $input['status'] === 'ACTIVE'   ? 'selected' : '' ?>>Active</option>
                    <option value="INACTIVE" <?= $input['status'] === 'INACTIVE' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-indigo-800 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed text-sm font-medium inline-flex items-center gap-2">
                    Add Student
                </button>
                <a href="/PTE-MANAGEMENT-SYSTEM/students"
                   class="bg-slate-100 text-slate-600 px-5 py-2 rounded-lg hover:bg-slate-200 text-sm font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</main>

<script>
(function () {
    var icInput      = document.getElementById('ic-number-input');
    var gradeSelect   = document.getElementById('grade-select');
    var suggestionHint = document.getElementById('grade-suggestion-hint');
    if (!icInput || !gradeSelect) return;

    var userChangedGrade = <?= (int)$input['grade_id'] > 0 ? 'true' : 'false' ?>;
    gradeSelect.addEventListener('change', function () {
        userChangedGrade = true;
        suggestionHint.classList.add('hidden');
    });

    // Malaysian school system: Darjah 1 (grade_level 1) starts the year a child turns 7,
    // through Tingkatan 5 (grade_level 11) — ages roughly 7 to 17.
    function gradeLevelForAge(age) {
        var level = age - 6;
        if (level < 1) level = 1;
        if (level > 11) level = 11;
        return level;
    }

    function suggestGradeFromIc() {
        if (userChangedGrade) return;

        var digits = icInput.value.replace(/[^0-9]/g, '');
        if (digits.length < 6) return;

        var yy = parseInt(digits.slice(0, 2), 10);
        var mm = parseInt(digits.slice(2, 4), 10);
        var dd = parseInt(digits.slice(4, 6), 10);
        if (mm < 1 || mm > 12 || dd < 1 || dd > 31) return;

        // Students here are always minors — always resolve YY to the 2000s.
        var birthYear = 2000 + yy;
        var today = new Date();
        var age = today.getFullYear() - birthYear;
        var hadBirthdayThisYear = (today.getMonth() + 1 > mm) || (today.getMonth() + 1 === mm && today.getDate() >= dd);
        if (!hadBirthdayThisYear) age -= 1;

        if (age < 4 || age > 19) return; // implausible for a tuition-centre student; leave grade alone

        var targetLevel = gradeLevelForAge(age);
        var options = gradeSelect.querySelectorAll('option[data-grade-level]');
        for (var i = 0; i < options.length; i++) {
            if (parseInt(options[i].getAttribute('data-grade-level'), 10) === targetLevel) {
                gradeSelect.value = options[i].value;
                suggestionHint.classList.remove('hidden');
                return;
            }
        }
    }

    icInput.addEventListener('input', suggestGradeFromIc);
    suggestGradeFromIc();
})();
</script>

<script>
(function () {
    var wrapper    = document.getElementById('parent-combobox');
    var searchInput = document.getElementById('parent-search-input');
    var hiddenInput = document.getElementById('parent-id-input');
    var optionsList  = document.getElementById('parent-options');
    var noResults    = document.getElementById('parent-no-results');
    if (!wrapper || !searchInput || !hiddenInput || !optionsList) return;

    var options = Array.prototype.slice.call(optionsList.querySelectorAll('li[data-id]'));
    var activeIndex = -1;

    function visibleOptions() {
        return options.filter(function (li) { return !li.classList.contains('hidden'); });
    }

    function openList() {
        optionsList.classList.remove('hidden');
        searchInput.setAttribute('aria-expanded', 'true');
    }

    function closeList() {
        optionsList.classList.add('hidden');
        searchInput.setAttribute('aria-expanded', 'false');
        setActive(-1);
    }

    function setActive(index) {
        var visible = visibleOptions();
        visible.forEach(function (li) { li.classList.remove('bg-indigo-50'); });
        activeIndex = index;
        if (index >= 0 && index < visible.length) {
            visible[index].classList.add('bg-indigo-50');
            visible[index].scrollIntoView({ block: 'nearest' });
        }
    }

    function selectOption(li) {
        hiddenInput.value = li.getAttribute('data-id');
        searchInput.value = li.getAttribute('data-label');
        closeList();
    }

    function filter() {
        var query = searchInput.value.trim().toLowerCase();
        var anyVisible = false;
        options.forEach(function (li) {
            var match = query === '' || li.getAttribute('data-search').indexOf(query) !== -1;
            li.classList.toggle('hidden', !match);
            if (match) anyVisible = true;
        });
        noResults.classList.toggle('hidden', anyVisible);
        setActive(anyVisible ? 0 : -1);
    }

    searchInput.addEventListener('focus', function () {
        filter();
        openList();
    });

    searchInput.addEventListener('input', function () {
        // Typing invalidates any previously confirmed selection until the user picks again
        hiddenInput.value = '';
        filter();
        openList();
    });

    searchInput.addEventListener('keydown', function (e) {
        var visible = visibleOptions();
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (optionsList.classList.contains('hidden')) { filter(); openList(); return; }
            setActive(Math.min(activeIndex + 1, visible.length - 1));
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            setActive(Math.max(activeIndex - 1, 0));
        } else if (e.key === 'Enter') {
            if (!optionsList.classList.contains('hidden') && activeIndex >= 0 && visible[activeIndex]) {
                e.preventDefault();
                selectOption(visible[activeIndex]);
            }
        } else if (e.key === 'Escape') {
            closeList();
        }
    });

    options.forEach(function (li) {
        li.addEventListener('mousedown', function (e) {
            e.preventDefault(); // keep focus on input so 'blur' below doesn't fire first
            selectOption(li);
        });
    });

    document.addEventListener('click', function (e) {
        if (!wrapper.contains(e.target)) closeList();
    });

    searchInput.addEventListener('blur', function () {
        // If the field no longer matches the confirmed selection's label, clear it
        setTimeout(function () {
            if (!hiddenInput.value) searchInput.value = '';
        }, 150);
    });
})();
</script>

<?php require_once '../../views/layout/footer.php'; ?>
