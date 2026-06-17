<?php
// main/register.php - RIMS Registration Page
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prefix = trim($_POST['prefix'] ?? '');
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirmation = $_POST['password_confirmation'] ?? '';

    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        $error = 'กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน';
    } elseif ($password !== $password_confirmation) {
        $error = 'รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน';
    } elseif (strlen($password) < 6) {
        $error = 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'อีเมลนี้ถูกใช้งานแล้ว';
        } else {
            $fullName = trim($prefix . ' ' . $first_name . ' ' . $last_name);
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare('INSERT INTO users (prefix, first_name, last_name, name, email, password) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$prefix, $first_name, $last_name, $fullName, $email, $hashedPassword]);
            $newUserId = $pdo->lastInsertId();

            // Automatically assign 'Researcher' role to new users
            $roleName = 'Researcher';
            $roleStmt = $pdo->prepare('SELECT id FROM roles WHERE name = ? LIMIT 1');
            $roleStmt->execute([$roleName]);
            $roleId = $roleStmt->fetchColumn();

            // If the role doesn't exist, create it
            if (!$roleId) {
                $createRoleStmt = $pdo->prepare('INSERT INTO roles (name, guard_name, created_at, updated_at) VALUES (?, "web", NOW(), NOW())');
                $createRoleStmt->execute([$roleName]);
                $roleId = $pdo->lastInsertId();
            }

            if ($roleId) {
                $assignStmt = $pdo->prepare('INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES (?, "App\\\\Models\\\\User", ?)');
                $assignStmt->execute([$roleId, $newUserId]);
            }

            $success = 'ลงทะเบียนสำเร็จ กรุณาเข้าสู่ระบบ';
            // Clear post values after success
            $_POST = [];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RIMS - สมัครสมาชิก</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.5.0/remixicon.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Sarabun', 'Inter', sans-serif;
            background: linear-gradient(135deg, #191a23 0%, #2d2f3d 50%, #191a23 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        .register-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 24px 80px rgba(0,0,0,0.3);
            overflow: hidden;
            width: 100%;
            max-width: 520px;
        }

        .register-header {
            background: #191a23;
            padding: 32px 40px 24px;
            text-align: center;
        }

        .register-logo {
            width: 48px; height: 48px;
            background: #b9ff66;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 12px;
        }

        .register-logo svg { width: 26px; height: 26px; }

        .register-title {
            font-size: 24px;
            font-weight: 850;
            color: #fff;
            letter-spacing: 1.5px;
            margin: 0;
        }

        .register-subtitle {
            color: rgba(255,255,255,0.5);
            font-size: 13px;
            margin-top: 4px;
        }

        .register-body { padding: 32px 40px; }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #191a23;
            margin-bottom: 6px;
        }

        .form-control {
            border: 1.5px solid #e8ecf0;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 14px;
            font-family: 'Sarabun', 'Inter', sans-serif;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: #b9ff66;
            box-shadow: 0 0 0 3px rgba(185,255,102,0.2);
        }

        .input-group .form-control { border-radius: 10px !important; }

        .btn-register {
            width: 100%;
            padding: 12px;
            background: #191a23;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Sarabun', 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 12px;
        }

        .btn-register:hover {
            background: #b9ff66;
            color: #191a23;
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(185,255,102,0.4);
        }

        .alert {
            border-radius: 10px;
            font-size: 14px;
            padding: 10px 14px;
        }

        .toggle-password {
            cursor: pointer;
            border: 1.5px solid #e8ecf0;
            border-left: none;
            border-radius: 0 10px 10px 0;
            background: #f8f9fa;
            padding: 0 12px;
            color: #888;
            transition: all 0.2s;
        }

        .toggle-password:hover { color: #191a23; }

        .register-footer {
            text-align: center;
            padding: 0 40px 32px;
            font-size: 13px;
            color: #888;
        }

        .register-footer a { color: #191a23; font-weight: 600; text-decoration: none; }
        .register-footer a:hover { color: #b9ff66; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .register-card { animation: fadeInUp 0.4s ease; }
    </style>
</head>
<body>
<div class="register-card">
    <div class="register-header">
        <div class="register-logo">
            <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="2" y="2" width="28" height="28" rx="6" fill="#191a23"/>
                <path d="M10 20L22 8M22 20L10 8" stroke="#b9ff66" stroke-width="3.5" stroke-linecap="round"/>
            </svg>
        </div>
        <h1 class="register-title">สมัครสมาชิก</h1>
        <p class="register-subtitle">ลงทะเบียนผู้ใช้งานระบบ RIMS</p>
    </div>

    <div class="register-body">
        <?php if ($error): ?>
        <div class="alert alert-danger d-flex align-items-center gap-2 mb-3">
            <i class="ri-error-warning-line"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="alert alert-success d-flex align-items-center gap-2 mb-3">
            <i class="ri-checkbox-circle-line"></i>
            <?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="row g-2 mb-3">
                <div class="col-sm-3">
                    <label class="form-label">คำนำหน้า</label>
                    <input type="text" name="prefix" class="form-control" placeholder="ดร. / นาย"
                           value="<?php echo htmlspecialchars($_POST['prefix'] ?? ''); ?>">
                </div>
                <div class="col-sm-9">
                    <label class="form-label">ชื่อ (First Name) *</label>
                    <input type="text" name="first_name" class="form-control" placeholder="สมชาย"
                           value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">นามสกุล (Last Name) *</label>
                <input type="text" name="last_name" class="form-control" placeholder="รักชาติ"
                       value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">อีเมล (Email Address) *</label>
                <input type="email" name="email" class="form-control" placeholder="somchai@email.com"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-sm-6">
                    <label class="form-label">รหัสผ่าน *</label>
                    <div class="input-group">
                        <input type="password" name="password" id="passwordInput"
                               class="form-control" placeholder="••••••••" required>
                        <button type="button" class="toggle-password" onclick="togglePass('passwordInput', 'eyeIcon1')">
                            <i class="ri-eye-line" id="eyeIcon1"></i>
                        </button>
                    </div>
                </div>
                <div class="col-sm-6">
                    <label class="form-label">ยืนยันรหัสผ่าน *</label>
                    <div class="input-group">
                        <input type="password" name="password_confirmation" id="passwordConfirmInput"
                               class="form-control" placeholder="••••••••" required>
                        <button type="button" class="toggle-password" onclick="togglePass('passwordConfirmInput', 'eyeIcon2')">
                            <i class="ri-eye-line" id="eyeIcon2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-register">
                <i class="ri-user-add-line me-2"></i>สร้างบัญชีผู้ใช้งาน
            </button>
        </form>
    </div>

    <div class="register-footer">
        มีบัญชีผู้ใช้อยู่แล้ว? <a href="login.php">เข้าสู่ระบบที่นี่</a>
    </div>
</div>

<script>
function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'ri-eye-off-line';
    } else {
        input.type = 'password';
        icon.className = 'ri-eye-line';
    }
}
</script>
</body>
</html>
