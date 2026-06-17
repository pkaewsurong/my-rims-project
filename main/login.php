<?php
// main/login.php - RIMS Login & Auth Page
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

// Handle Login POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'กรุณากรอกอีเมลและรหัสผ่าน';
    } else {
        $stmt = $pdo->prepare('SELECT u.*, GROUP_CONCAT(r.name) as roles
            FROM users u
            LEFT JOIN model_has_roles mhr ON u.id = mhr.model_id
            LEFT JOIN roles r ON mhr.role_id = r.id
            WHERE u.email = ?
            GROUP BY u.id');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_name']  = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_roles'] = $user['roles'] ? explode(',', $user['roles']) : ['Researcher'];

            header('Location: index.php');
            exit;
        } else {
            $error = 'อีเมลหรือรหัสผ่านไม่ถูกต้อง';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RIMS - เข้าสู่ระบบ</title>
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
            padding: 16px;
        }

        .login-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 24px 80px rgba(0,0,0,0.3);
            overflow: hidden;
            width: 100%;
            max-width: 440px;
        }

        .login-header {
            background: #191a23;
            padding: 40px 40px 32px;
            text-align: center;
        }

        .login-logo {
            width: 56px; height: 56px;
            background: #b9ff66;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
        }

        .login-logo svg { width: 32px; height: 32px; }

        .login-title {
            font-size: 28px;
            font-weight: 800;
            color: #fff;
            letter-spacing: 2px;
            margin: 0;
        }

        .login-subtitle {
            color: rgba(255,255,255,0.5);
            font-size: 13px;
            margin-top: 6px;
        }

        .login-body { padding: 40px; }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #191a23;
            margin-bottom: 6px;
        }

        .form-control {
            border: 1.5px solid #e8ecf0;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 14px;
            font-family: 'Sarabun', 'Inter', sans-serif;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: #b9ff66;
            box-shadow: 0 0 0 3px rgba(185,255,102,0.2);
        }

        .input-group .form-control { border-radius: 10px !important; }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: #191a23;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Sarabun', 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 8px;
        }

        .btn-login:hover {
            background: #b9ff66;
            color: #191a23;
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(185,255,102,0.4);
        }

        .alert {
            border-radius: 10px;
            font-size: 14px;
            padding: 12px 16px;
        }

        .toggle-password {
            cursor: pointer;
            border: 1.5px solid #e8ecf0;
            border-left: none;
            border-radius: 0 10px 10px 0;
            background: #f8f9fa;
            padding: 0 14px;
            color: #888;
            transition: all 0.2s;
        }

        .toggle-password:hover { color: #191a23; }

        .login-footer {
            text-align: center;
            padding: 0 40px 32px;
            font-size: 13px;
            color: #888;
        }

        .login-footer a { color: #191a23; font-weight: 600; text-decoration: none; }
        .login-footer a:hover { color: #b9ff66; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .login-card { animation: fadeInUp 0.4s ease; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-header">
        <div class="login-logo">
            <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="2" y="2" width="28" height="28" rx="6" fill="#191a23"/>
                <path d="M10 20L22 8M22 20L10 8" stroke="#b9ff66" stroke-width="3.5" stroke-linecap="round"/>
            </svg>
        </div>
        <h1 class="login-title">RIMS</h1>
        <p class="login-subtitle">ระบบบริหารงานวิจัยและนวัตกรรม</p>
    </div>

    <div class="login-body">
        <?php if ($error): ?>
        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
            <i class="ri-error-warning-line"></i>
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="mb-4">
                <label class="form-label">อีเมล (Email)</label>
                <div class="input-group">
                    <input type="email" name="email" class="form-control"
                           placeholder="example@email.com"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                           required autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">รหัสผ่าน (Password)</label>
                <div class="input-group">
                    <input type="password" name="password" id="passwordInput"
                           class="form-control" placeholder="••••••••" required>
                    <button type="button" class="toggle-password" onclick="togglePass()">
                        <i class="ri-eye-line" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="ri-login-box-line me-2"></i>เข้าสู่ระบบ
            </button>
        </form>
    </div>

    <div class="login-footer d-flex justify-content-between px-4">
        <div>ลืมรหัสผ่าน? <a href="../public/forgot-password">คลิกที่นี่</a></div>
        <div><a href="register.php">สมัครสมาชิกใหม่</a></div>
    </div>
</div>

<script>
function togglePass() {
    const input = document.getElementById('passwordInput');
    const icon = document.getElementById('eyeIcon');
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
