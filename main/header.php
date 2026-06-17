<?php
// main/header.php - RIMS Layout Header (PCT Pattern)
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$currentUser = authUser();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

function isActive($page) {
    global $currentPage;
    return $currentPage === $page ? 'active' : '';
}

function isParentActive(array $pages) {
    global $currentPage;
    foreach ($pages as $page) {
        if ($currentPage === $page) return 'active open';
    }
    return '';
}

// Fetch notifications
$recentNotifications = getRecentNotifications($pdo);
$unreadCount = getUnreadNotificationCount($pdo, $recentNotifications);

// Get user full session info
$userName = $_SESSION['user_name'] ?? 'ผู้ใช้งาน';
$userRoles = $_SESSION['user_roles'] ?? [];
$primaryRole = !empty($userRoles) ? $userRoles[0] : 'User';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>RIMS - <?php echo $pageTitle ?? 'ระบบบริหารงานวิจัย'; ?></title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Remix Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.5.0/remixicon.min.css">
    <!-- DataTables Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Google Fonts: Sarabun -->
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Page-specific CSS -->
    <?php if (isset($pageCss) && $pageCss): ?>
    <link rel="stylesheet" href="css/<?php echo htmlspecialchars($pageCss); ?>.css">
    <?php endif; ?>

    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #191a23;
            --sidebar-text: #b0b7c3;
            --sidebar-active: #b9ff66;
            --sidebar-active-text: #191a23;
            --topbar-height: 64px;
            --accent: #b9ff66;
            --accent-dark: #191a23;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Sarabun', 'Inter', sans-serif;
            background-color: #f5f6fa;
            color: #191a23;
            margin: 0;
            padding: 0;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
            transition: transform 0.3s ease;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 22px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            text-decoration: none;
        }

        .sidebar-logo-icon {
            width: 38px; height: 38px;
            background: var(--accent);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }

        .sidebar-logo-icon svg { width: 22px; height: 22px; }

        .sidebar-logo-text {
            font-size: 20px;
            font-weight: 800;
            color: #fff;
            letter-spacing: 1px;
        }

        .sidebar-section-title {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.3);
            padding: 16px 24px 6px;
        }

        .sidebar-nav { padding: 8px 12px; flex: 1; }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 10px;
            color: var(--sidebar-text);
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            margin-bottom: 2px;
        }

        .sidebar-nav .nav-link i {
            font-size: 18px;
            width: 20px;
            text-align: center;
        }

        .sidebar-nav .nav-link:hover {
            background: rgba(255,255,255,0.08);
            color: #fff;
        }

        .sidebar-nav .nav-link.active {
            background: var(--accent);
            color: var(--accent-dark);
            font-weight: 700;
        }

        .sidebar-nav .nav-link.active i { color: var(--accent-dark); }

        /* ===== TOPBAR ===== */
        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--topbar-height);
            background: #fff;
            border-bottom: 1px solid #e8ecf0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            z-index: 900;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }

        .topbar-title {
            font-size: 18px;
            font-weight: 700;
            color: #191a23;
        }

        .topbar-actions { display: flex; align-items: center; gap: 12px; }

        .topbar-icon-btn {
            width: 38px; height: 38px;
            border: 1px solid #e8ecf0;
            background: #f5f6fa;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            color: #526077;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            text-decoration: none;
        }

        .topbar-icon-btn:hover {
            background: var(--accent);
            color: var(--accent-dark);
            border-color: var(--accent);
        }

        .notif-badge {
            position: absolute;
            top: -5px; right: -5px;
            width: 18px; height: 18px;
            background: #ef4444;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid #fff;
        }

        .user-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px;
            background: #f5f6fa;
            border: 1px solid #e8ecf0;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .user-chip:hover { border-color: var(--accent); background: #fafff0; }

        .user-avatar {
            width: 32px; height: 32px;
            background: var(--accent);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: var(--accent-dark);
        }

        .user-info .user-name { font-size: 13px; font-weight: 600; color: #191a23; line-height: 1.2; }
        .user-info .user-role { font-size: 10px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }

        /* ===== MAIN CONTENT ===== */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            padding-top: var(--topbar-height);
            min-height: 100vh;
        }

        .page-content { padding: 28px; }

        /* ===== PAGE HEADER ===== */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 800;
            color: #191a23;
            margin: 0;
        }

        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
            font-size: 13px;
        }

        /* ===== CARDS ===== */
        .card {
            background: #fff;
            border: 1px solid #e8ecf0;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        .card-body { padding: 24px; }

        /* ===== NOTIFICATION DROPDOWN ===== */
        .notif-dropdown {
            position: fixed;
            top: calc(var(--topbar-height) + 8px);
            right: 24px;
            width: 340px;
            background: #fff;
            border: 1px solid #e8ecf0;
            border-radius: 14px;
            box-shadow: 0 12px 40px rgba(0,0,0,0.12);
            z-index: 2000;
            display: none;
        }

        .notif-dropdown.show { display: block; }

        /* ===== LOADING ===== */
        .loading-spinner {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            color: #888;
        }

        /* ===== TABLES ===== */
        .table { font-size: 14px; }
        .table th { background: #f8f9fa; font-weight: 600; color: #191a23; }

        /* ===== BADGES ===== */
        .badge-status {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .topbar, .main-wrapper { left: 0; margin-left: 0; }
            .topbar { padding: 0 16px; }
            .page-content { padding: 16px; }
            .page-title { font-size: 20px; }
            .card-body { padding: 16px; }
            
            /* Make DataTable pagination and search look nice on mobile */
            .dt-container .row:first-child, .dt-container .row:last-child {
                display: flex;
                flex-direction: column;
                gap: 10px;
                align-items: center;
                justify-content: center;
            }
            .dt-search, .dt-paging, .dt-length, .dt-info {
                display: flex !important;
                justify-content: center !important;
                text-align: center !important;
                width: 100% !important;
                margin: 4px 0 !important;
            }
        }
    </style>
</head>
<body>

<!-- ===== SIDEBAR ===== -->
<div class="sidebar" id="sidebar">
    <a href="index.php" class="sidebar-logo">
        <div class="sidebar-logo-icon">
            <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="1" y="1" width="20" height="20" rx="5" fill="#191a23"/>
                <path d="M8 14L14 8M14 14L8 8" stroke="#b9ff66" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
        </div>
        <span class="sidebar-logo-text">RIMS</span>
    </a>

    <nav class="sidebar-nav">
        <div class="sidebar-section-title">เมนูหลัก</div>

        <a href="index.php" class="nav-link <?php echo isActive('index'); ?>">
            <i class="ri-dashboard-line"></i>
            <span>ภาพรวม (Dashboard)</span>
        </a>

        <div class="sidebar-section-title">โครงการวิจัย</div>

        <a href="projects.php" class="nav-link <?php echo isActive('projects'); ?>">
            <i class="ri-folder-line"></i>
            <span>โครงการของฉัน</span>
        </a>

        <a href="projects_all.php" class="nav-link <?php echo isActive('projects_all'); ?>">
            <i class="ri-folders-line"></i>
            <span>รวมโครงการทั้งหมด</span>
        </a>

        <a href="proposals.php" class="nav-link <?php echo isActive('proposals'); ?>">
            <i class="ri-file-text-line"></i>
            <span>ข้อเสนอโครงการ</span>
        </a>

        <a href="archives.php" class="nav-link <?php echo isActive('archives'); ?>">
            <i class="ri-archive-line"></i>
            <span>คลังข้อมูลวิจัย</span>
        </a>

        <?php if (hasRole('admin') || hasRole('research_admin')): ?>
        <div class="sidebar-section-title">ผู้ดูแลระบบ</div>
        <a href="admin.php" class="nav-link <?php echo isActive('admin'); ?>">
            <i class="ri-settings-4-line"></i>
            <span>จัดการข้อมูลหลัก</span>
        </a>
        <?php endif; ?>

        <div class="sidebar-section-title">บัญชีผู้ใช้</div>

        <a href="profile.php" class="nav-link <?php echo isActive('profile'); ?>">
            <i class="ri-user-line"></i>
            <span>โปรไฟล์ของฉัน</span>
        </a>

        <a href="login.php?action=logout" class="nav-link text-danger" onclick="return confirmLogout();">
            <i class="ri-logout-box-line"></i>
            <span>ออกจากระบบ</span>
        </a>
    </nav>
</div>

<!-- ===== TOPBAR ===== -->
<div class="topbar">
    <div class="d-flex align-items-center gap-3">
        <button class="topbar-icon-btn border-0 bg-transparent d-md-none" onclick="toggleSidebar()">
            <i class="ri-menu-line fs-5"></i>
        </button>
        <span class="topbar-title"><?php echo $pageTitle ?? 'RIMS'; ?></span>
    </div>

    <div class="topbar-actions">
        <!-- Notification Bell -->
        <div class="position-relative">
            <button class="topbar-icon-btn" onclick="toggleNotif()" id="notifBtn">
                <i class="ri-notification-3-line"></i>
                <?php if ($unreadCount > 0): ?>
                <span class="notif-badge"><?php echo $unreadCount; ?></span>
                <?php endif; ?>
            </button>

            <!-- Notification Dropdown -->
            <div class="notif-dropdown" id="notifDropdown">
                <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                    <strong style="font-size:14px;">การแจ้งเตือน</strong>
                    <a href="ajax/notifications/MarkRead.php" class="text-decoration-none" style="font-size:12px;color:#888;">
                        อ่านทั้งหมด
                    </a>
                </div>
                <div style="max-height:320px; overflow-y:auto;">
                    <?php if (empty($recentNotifications)): ?>
                        <div class="text-center text-muted py-4" style="font-size:13px;">ไม่มีการแจ้งเตือน</div>
                    <?php else: foreach ($recentNotifications as $notif): ?>
                        <div class="p-3 border-bottom <?php echo !$notif['is_read'] ? 'bg-light' : ''; ?>" style="font-size:13px;">
                            <div class="fw-semibold"><?php echo htmlspecialchars($notif['title']); ?></div>
                            <div class="text-muted mt-1"><?php echo htmlspecialchars($notif['message']); ?></div>
                            <div class="text-muted mt-1" style="font-size:11px;"><?php echo date('d/m/Y H:i', strtotime($notif['created_at'])); ?></div>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </div>

        <!-- User Chip -->
        <div class="dropdown">
            <div class="user-chip" data-bs-toggle="dropdown">
                <div class="user-avatar">
                    <?php echo mb_substr($userName, 0, 1, 'UTF-8'); ?>
                </div>
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($userName); ?></div>
                    <div class="user-role"><?php echo htmlspecialchars($primaryRole); ?></div>
                </div>
                <i class="ri-arrow-down-s-line" style="color:#888; font-size:16px;"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end mt-2" style="border-radius:12px; border:1px solid #e8ecf0; min-width:180px;">
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2" href="profile.php">
                        <i class="ri-user-line"></i> โปรไฟล์
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="#" onclick="confirmLogout(); return false;">
                        <i class="ri-logout-box-line"></i> ออกจากระบบ
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- ===== MAIN WRAPPER ===== -->
<div class="main-wrapper">
    <div class="page-content">
