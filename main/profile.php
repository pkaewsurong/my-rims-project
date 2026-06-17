<?php
// main/profile.php
$pageTitle = 'โปรไฟล์ของฉัน';
$pageCss   = 'profile';
$pageJs    = 'profile';
require 'header.php';

// Fetch user data
$stmt = $pdo->prepare('SELECT u.*, GROUP_CONCAT(r.name SEPARATOR ", ") as roles
    FROM users u
    LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id
    LEFT JOIN roles r ON mhr.role_id = r.id
    WHERE u.id = ? GROUP BY u.id');
$stmt->execute([$currentUser['id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Profile update
$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($name) || empty($email)) {
        $error = 'กรุณากรอกชื่อและอีเมล';
    } else {
        // Check email uniqueness
        $check = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
        $check->execute([$email, $currentUser['id']]);
        if ($check->fetch()) {
            $error = 'อีเมลนี้ถูกใช้งานแล้ว';
        } else {
            $updateData = ['name' => $name, 'email' => $email];
            $updateSQL = 'UPDATE users SET name = ?, email = ?';
            $updateParams = [$name, $email];

            // Password change
            if (!empty($_POST['new_password'])) {
                if (strlen($_POST['new_password']) < 8) {
                    $error = 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร';
                } elseif ($_POST['new_password'] !== ($_POST['confirm_password'] ?? '')) {
                    $error = 'รหัสผ่านไม่ตรงกัน';
                } else {
                    $updateSQL .= ', password = ?';
                    $updateParams[] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                }
            }

            if (!$error) {
                $updateSQL .= ' WHERE id = ?';
                $updateParams[] = $currentUser['id'];
                $pdo->prepare($updateSQL)->execute($updateParams);
                $_SESSION['user_name'] = $name;
                $success = 'อัปเดตโปรไฟล์สำเร็จแล้ว';
                // Refresh user data
                $stmt->execute([$currentUser['id']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $userName = $name;
            }
        }
    }
}
?>
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="ri-user-line me-2"></i>โปรไฟล์ของฉัน</h1>
        <nav aria-label="breadcrumb" class="mt-1">
            <ol class="breadcrumb mb-0" style="font-size:13px;">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">หน้าหลัก</a></li>
                <li class="breadcrumb-item active">โปรไฟล์</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row g-4">
    <!-- Profile Card -->
    <div class="col-lg-4">
        <div class="card text-center">
            <div class="card-body py-5">
                <div style="width:100px; height:100px; background:#b9ff66; border-radius:50%;
                            display:flex; align-items:center; justify-content:center;
                            font-size:42px; font-weight:800; color:#191a23; margin:0 auto 16px;">
                    <?php echo mb_substr($user['name'] ?? 'U', 0, 1, 'UTF-8'); ?>
                </div>
                <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($user['name'] ?? ''); ?></h5>
                <p class="text-muted mb-2" style="font-size:14px;"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
                <?php if (!empty($user['roles'])): ?>
                <span class="badge bg-dark"><?php echo htmlspecialchars($user['roles']); ?></span>
                <?php endif; ?>
                <hr class="my-4">
                <div class="text-start">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted" style="font-size:13px;">สมัครเมื่อ</span>
                        <span class="fw-semibold" style="font-size:13px;">
                            <?php echo $user['created_at'] ? date('d/m/Y', strtotime($user['created_at'])) : '-'; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-4 pb-2 border-bottom">
                    <i class="ri-edit-2-line me-2"></i>แก้ไขข้อมูลส่วนตัว
                </h6>

                <?php if ($success): ?>
                <div class="alert alert-success d-flex align-items-center gap-2">
                    <i class="ri-checkbox-circle-line"></i> <?php echo htmlspecialchars($success); ?>
                </div>
                <?php endif; ?>

                <?php if ($error): ?>
                <div class="alert alert-danger d-flex align-items-center gap-2">
                    <i class="ri-error-warning-line"></i> <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="action" value="update">

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">ชื่อ-นามสกุล</label>
                            <input type="text" name="name" class="form-control"
                                   value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">อีเมล</label>
                            <input type="email" name="email" class="form-control"
                                   value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold mb-3" style="font-size:14px; color:#888;">
                        <i class="ri-lock-line me-1"></i>เปลี่ยนรหัสผ่าน (ทิ้งว่างถ้าไม่ต้องการเปลี่ยน)
                    </h6>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">รหัสผ่านใหม่</label>
                            <input type="password" name="new_password" class="form-control"
                                   placeholder="อย่างน้อย 8 ตัวอักษร">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">ยืนยันรหัสผ่านใหม่</label>
                            <input type="password" name="confirm_password" class="form-control"
                                   placeholder="พิมพ์รหัสผ่านอีกครั้ง">
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-end mt-4">
                        <a href="index.php" class="btn btn-outline-secondary">ยกเลิก</a>
                        <button type="submit" class="btn btn-dark fw-bold">
                            <i class="ri-save-line me-1"></i> บันทึกการเปลี่ยนแปลง
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require 'footer.php'; ?>
