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

$id = (int)($_GET['id'] ?? 0);
if ($id === 0) {
    header('Location: /PTE-MANAGEMENT-SYSTEM/parents');
    exit;
}

$errors = [];
$input  = [];

try {
    $conn      = getConnection();
    $fetchStmt = oci_parse($conn, 'SELECT * FROM PARENT WHERE parent_id = :id');
    oci_bind_by_name($fetchStmt, ':id', $id);
    oci_execute($fetchStmt);
    $parent = oci_fetch_assoc($fetchStmt);
    oci_free_statement($fetchStmt);

    if (!$parent) {
        oci_close($conn);
        $_SESSION['flash_error'] = 'Parent not found.';
        header('Location: /PTE-MANAGEMENT-SYSTEM/parents');
        exit;
    }

    $input = [
        'fullname'  => $parent['FULLNAME'],
        'ic_number' => $parent['IC_NUMBER'] ?? '',
        'email'     => $parent['EMAIL']     ?? '',
        'phone'     => $parent['PHONE'],
        'address'   => $parent['ADDRESS']   ?? '',
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input['fullname']  = trim($_POST['fullname'] ?? '');
        $input['ic_number'] = trim($_POST['ic_number'] ?? '');
        $input['email']     = trim($_POST['email'] ?? '');
        $input['phone']     = trim($_POST['phone'] ?? '');
        $input['address']   = trim($_POST['address'] ?? '');

        if ($input['fullname'] === '') $errors['fullname'] = 'Full name is required.';
        if ($input['phone'] === '')    $errors['phone'] = 'Phone number is required.';
        if ($input['email'] !== '' && !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email address.';

        if (empty($errors['ic_number']) && $input['ic_number'] !== '') {
            $chkStmt = oci_parse($conn, 'SELECT COUNT(*) AS cnt FROM PARENT WHERE ic_number = :ic AND parent_id != :id');
            oci_bind_by_name($chkStmt, ':ic', $input['ic_number']);
            oci_bind_by_name($chkStmt, ':id', $id);
            oci_execute($chkStmt);
            if ((int)oci_fetch_assoc($chkStmt)['CNT'] > 0) $errors['ic_number'] = 'IC number already registered to another parent.';
            oci_free_statement($chkStmt);
        }

        if (empty($errors)) {
            $ic   = $input['ic_number'] !== '' ? $input['ic_number'] : null;
            $em   = $input['email']     !== '' ? $input['email']     : null;
            $addr = $input['address']   !== '' ? $input['address']   : null;

            $updSql  = 'UPDATE PARENT SET fullname = :fullname, ic_number = :ic, email = :email,
                        phone = :phone, address = :address, updated_at = SYSTIMESTAMP
                        WHERE parent_id = :id';
            $updStmt = oci_parse($conn, $updSql);
            oci_bind_by_name($updStmt, ':fullname', $input['fullname']);
            oci_bind_by_name($updStmt, ':ic',       $ic);
            oci_bind_by_name($updStmt, ':email',    $em);
            oci_bind_by_name($updStmt, ':phone',    $input['phone']);
            oci_bind_by_name($updStmt, ':address',  $addr);
            oci_bind_by_name($updStmt, ':id',       $id);
            oci_execute($updStmt);
            oci_commit($conn);
            oci_free_statement($updStmt);
            oci_close($conn);

            $_SESSION['flash_success'] = 'Parent updated successfully.';
            header('Location: /PTE-MANAGEMENT-SYSTEM/parents');
            exit;
        }
    }

    oci_close($conn);
} catch (\RuntimeException $e) {
    $errors['_general'] = 'Database error. Please try again.';
}

$pageTitle = 'Edit Parent — PTE Management System';
require_once '../../views/layout/header.php';
require_once '../../views/layout/sidebar.php';
?>

<main class="pt-16 md:pt-10 md:ml-64 px-4 sm:px-8 pb-4 sm:pb-8 min-h-screen">
    <div class="mb-6 flex items-center gap-3">
        <a href="/PTE-MANAGEMENT-SYSTEM/parents" class="text-slate-400 hover:text-slate-600">
            <i class="ti ti-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-xl font-semibold text-slate-800">Edit Parent</h1>
            <p class="text-slate-500 text-sm mt-1"><?= htmlspecialchars($input['fullname'], ENT_QUOTES, 'UTF-8') ?></p>
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

    <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 max-w-xl">
        <form method="POST" action="/PTE-MANAGEMENT-SYSTEM/parents/edit?id=<?= $id ?>" novalidate
              onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').innerHTML = '<i class=\'ti ti-loader-2 animate-spin\'></i> Saving…';">
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                <input type="text" name="fullname" value="<?= htmlspecialchars($input['fullname'], ENT_QUOTES, 'UTF-8') ?>"
                       class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'fullname') ?>"
                       aria-invalid="<?= isset($errors['fullname']) ? 'true' : 'false' ?>">
                <?php if (isset($errors['fullname'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['fullname'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">IC Number</label>
                <input type="text" name="ic_number" value="<?= htmlspecialchars($input['ic_number'], ENT_QUOTES, 'UTF-8') ?>"
                       class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'ic_number') ?>"
                       aria-invalid="<?= isset($errors['ic_number']) ? 'true' : 'false' ?>"
                       data-format="ic" inputmode="numeric" maxlength="14">
                <?php if (isset($errors['ic_number'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['ic_number'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Phone <span class="text-red-500">*</span></label>
                <input type="text" name="phone" value="<?= htmlspecialchars($input['phone'], ENT_QUOTES, 'UTF-8') ?>"
                       class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'phone') ?>"
                       aria-invalid="<?= isset($errors['phone']) ? 'true' : 'false' ?>">
                <?php if (isset($errors['phone'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['phone'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($input['email'], ENT_QUOTES, 'UTF-8') ?>"
                       class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'email') ?>"
                       aria-invalid="<?= isset($errors['email']) ? 'true' : 'false' ?>">
                <?php if (isset($errors['email'])): ?>
                <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Address</label>
                <textarea name="address" rows="3"
                          class="border rounded-lg px-3 py-2 w-full text-sm <?= fieldRing($errors, 'address') ?>"><?= htmlspecialchars($input['address'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-indigo-800 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed text-sm font-medium inline-flex items-center gap-2">
                    Save Changes
                </button>
                <a href="/PTE-MANAGEMENT-SYSTEM/parents"
                   class="bg-slate-100 text-slate-600 px-5 py-2 rounded-lg hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 text-sm font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</main>

<?php require_once '../../views/layout/footer.php'; ?>
