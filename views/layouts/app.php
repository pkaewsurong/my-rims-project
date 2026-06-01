<?php 
ob_start(); 
global $pdo;
$unreadCount = getUnreadNotificationCount($pdo);
$recentNotifications = getRecentNotifications($pdo);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RIMS - ระบบบริหารงานวิจัยและนวัตกรรม</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'Sarabun', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f9fafb',
                            100: '#b9ff66',
                            600: '#191a23',
                            700: '#111827',
                            900: '#030712',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Sarabun:wght@300;400;500;600;700&display=swap');
        
        body {
            background-color: #f9fafb;
            color: #191a23;
            transition: all 0.3s ease;
        }
        
        .card {
            background: #ffffff;
            border: 1px solid #191a23;
            border-radius: 1.25rem;
            box-shadow: 0px 5px 0px #191a23;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0px 8px 0px #191a23;
        }

        .btn-primary {
            background-color: #191a23;
            color: #ffffff;
            border: 1px solid #191a23;
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            background-color: #b9ff66;
            color: #191a23;
            transform: translateY(-2px);
            box-shadow: 0px 4px 0px #191a23;
        }

        .nav-link {
            color: #191a23;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }
        .nav-link:hover {
            color: #191a23;
            background-color: #b9ff66;
            border-color: #191a23;
        }

        /* Notification Dropdown */
        .notification-dropdown {
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            background: #ffffff;
            border: 1px solid #191a23;
            box-shadow: 0px 6px 0px #191a23;
        }
        .notification-dropdown.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        /* Custom Theme Modal */
        #custom-modal-container {
            position: fixed;
            inset: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            backdrop-filter: blur(4px);
            background-color: rgba(25, 26, 35, 0.3);
            padding: 1rem;
        }

        #custom-modal-container.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-glass {
            background: #ffffff;
            border: 1px solid #191a23;
            box-shadow: 0px 8px 0px #191a23;
            border-radius: 1.5rem;
            max-width: 450px;
            width: 100%;
            transform: scale(0.95);
            transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
            overflow: hidden;
        }

        #custom-modal-container.active .modal-glass {
            transform: scale(1);
        }

        /* Global overrides to map Indigo utilities to Positivus style colors */
        .text-indigo-500, .text-indigo-600, .text-indigo-655, .text-indigo-650, .text-indigo-700, .text-indigo-850, .text-indigo-705 {
            color: #191a23 !important;
        }
        .hover\:text-indigo-650:hover, .hover\:text-indigo-600:hover, .hover\:text-indigo-700:hover {
            color: #000000 !important;
        }
        .bg-indigo-50, .bg-indigo-50\/50, .bg-indigo-50\/60, .bg-indigo-100 {
            background-color: #b9ff66 !important;
            color: #191a23 !important;
        }
        .hover\:bg-indigo-50:hover, .hover\:bg-indigo-100:hover {
            background-color: #a3e635 !important;
        }
        .border-indigo-200, .border-indigo-150, .border-indigo-100 {
            border-color: #191a23 !important;
        }
        .focus\:ring-indigo-500:focus, .focus\:ring-indigo-600:focus {
            --tw-ring-color: #191a23 !important;
            border-color: #191a23 !important;
        }
        .focus\:border-indigo-500:focus, .focus\:border-indigo-600:focus {
            border-color: #191a23 !important;
        }
        .file\:bg-indigo-50::file-selector-button {
            background-color: #b9ff66 !important;
            color: #191a23 !important;
            border-color: #191a23 !important;
        }
        .file\:bg-indigo-50:hover::file-selector-button {
            background-color: #a3e635 !important;
        }
        .text-slate-800 {
            color: #191a23 !important;
        }
        .text-slate-700 {
            color: #374151 !important;
        }
        .text-slate-600 {
            color: #4b5563 !important;
        }
    </style>
</head>
<body class="font-sans antialiased min-h-screen flex flex-col">
    <nav class="bg-white border-b border-slate-250/80 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-3 gap-8">
                    <a href="<?= url('/') ?>" class="flex-shrink-0 flex items-center group">
                        <div class="flex items-center justify-center mr-3">
                            <!-- Geometric Positivus-style Logo -->
                            <svg class="w-8 h-8 text-[#191a23] transition-all duration-300 group-hover:rotate-90" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="2" y="2" width="28" height="28" rx="6" fill="#191a23"/>
                                <path d="M12 20L20 12M20 20L12 12" stroke="#b9ff66" stroke-width="4" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h1 class="text-2xl font-black text-[#191a23] tracking-wide font-sans">RIMS</h1>
                    </a>
                    
                    <?php if (isLoggedIn()): ?>
                    <div class="hidden md:flex space-x-2 h-full items-center">
                        <a href="/public/dashboard" class="nav-link px-3 py-2 rounded-lg text-sm font-medium">ภาพรวม (Dashboard)</a>
                        <a href="/public/projects/all" class="nav-link px-3 py-2 rounded-lg text-sm font-medium">รวมโครงการ (All Projects)</a>
                        <a href="/public/projects" class="nav-link px-3 py-2 rounded-lg text-sm font-medium">โครงการของฉัน (My Projects)</a>
                        
                        <?php if (hasRole('admin')): ?>
                            <a href="/public/proposals" class="nav-link px-3 py-2 rounded-lg text-sm font-medium">ข้อเสนอ (Proposals)</a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if (isLoggedIn()): 
                        $roles = $_SESSION['user_roles'] ?? [];
                        $primaryRole = !empty($roles) ? $roles[0] : 'User';
                    ?>
                        <div class="flex items-center gap-4 ml-auto">
                            <div class="flex items-center bg-slate-50 rounded-full py-1.5 px-4 border border-slate-200">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 shadow-sm animate-pulse"></span>
                                <div class="flex flex-col mr-4 border-r border-slate-200 pr-4">
                                    <span class="text-slate-700 text-sm font-semibold leading-tight"><?= e(authUser()['name']) ?></span>
                                    <span class="text-indigo-600 text-[10px] uppercase tracking-wider font-bold mt-0.5"><?= e($primaryRole) ?></span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <a href="/public/profile" class="text-slate-600 hover:text-indigo-600 text-sm font-medium transition-colors">โปรไฟล์ส่วนตัว</a>
                                    <span class="text-slate-300">|</span>
                                    <form action="/public/logout" method="POST" class="inline m-0 p-0 flex items-center">
                                        <button type="submit" class="text-rose-600 hover:text-rose-700 text-sm font-medium transition-colors">ออกจากระบบ</button>
                                    </form>
                                </div>
                            </div>

                            <!-- Notification Bell -->
                            <div class="relative">
                                <button onclick="toggleNotifications()" class="p-2 text-slate-500 hover:text-indigo-600 transition-all relative group bg-slate-50 rounded-full hover:bg-slate-100 border border-slate-200" title="การแจ้งเตือน">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                    <?php if ($unreadCount > 0): ?>
                                        <span class="absolute top-1 right-1 w-4 h-4 bg-rose-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center border-2 border-white"><?= $unreadCount ?></span>
                                    <?php endif; ?>
                                </button>

                                <!-- Dropdown -->
                                <div id="notif-dropdown" class="notification-dropdown absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-xl overflow-hidden z-[60] border border-slate-200">
                                    <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                                        <h3 class="text-sm font-bold text-slate-800 tracking-wide">การแจ้งเตือน</h3>
                                        <a href="/public/notifications/mark-read" class="text-[10px] text-indigo-600 hover:underline uppercase tracking-widest font-bold">อ่านทั้งหมด</a>
                                    </div>
                                    <div class="max-h-96 overflow-y-auto">
                                        <?php if (empty($recentNotifications)): ?>
                                            <div class="p-8 text-center">
                                                <p class="text-slate-400 text-sm">ไม่มีการแจ้งเตือนใหม่</p>
                                            </div>
                                        <?php else: ?>
                                            <?php foreach ($recentNotifications as $notif): ?>
                                                <div class="p-4 border-b border-slate-50 hover:bg-slate-50 transition-colors <?= $notif['is_read'] ? '' : 'bg-indigo-50/30' ?>">
                                                    <p class="text-xs font-bold text-slate-800 mb-1"><?= e($notif['title']) ?></p>
                                                    <p class="text-[11px] text-slate-600 leading-relaxed"><?= e($notif['message']) ?></p>
                                                    <p class="text-[9px] text-slate-400 mt-2"><?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?></p>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center gap-4">
                            <a href="/public/login" class="text-slate-600 hover:text-indigo-600 px-4 py-2 font-medium transition-colors">เข้าสู่ระบบ</a>
                            <a href="/public/register" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold tracking-wide shadow-sm">สมัครสมาชิก</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow py-12 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?= $content ?? '' ?>
        </div>
    </main>
    
    <footer class="bg-white border-t border-slate-200 py-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-slate-500 text-sm">
            &copy; <?= date('Y') ?> Research & Innovation Management System. ระบบบริหารงานวิจัยและนวัตกรรม.
        </div>
    </footer>

    <!-- Custom Theme Modal HTML -->
    <div id="custom-modal-container" class="items-center justify-center">
        <div id="custom-modal" class="modal-glass">
            <div class="p-8 text-center">
                <div id="modal-icon-container" class="w-16 h-16 rounded-2xl bg-indigo-50 flex items-center justify-center mx-auto mb-6 border border-indigo-100 text-indigo-600 shadow-sm">
                    <svg id="modal-icon-confirm" class="w-8 h-8 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <svg id="modal-icon-alert" class="w-8 h-8 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 id="modal-title" class="text-xl font-bold text-slate-800 mb-3 tracking-wider">ยืนยันการทำรายการ</h3>
                <p id="modal-message" class="text-slate-650 text-sm leading-relaxed mb-8">คุณแน่ใจหรือไม่ที่จะดำเนินการนี้?</p>
                <div class="flex gap-4 justify-center">
                    <button id="modal-cancel-btn" class="px-6 py-2.5 rounded-xl border border-slate-200 text-slate-500 font-medium text-sm hover:bg-slate-50 transition-all">ยกเลิก</button>
                    <button id="modal-confirm-btn" class="px-8 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold text-sm shadow-sm hover:bg-indigo-700 transition-all">ยืนยัน</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleNotifications() {
            const dropdown = document.getElementById('notif-dropdown');
            dropdown.classList.toggle('active');
            
            // Close dropdown when clicking outside
            const closeHandler = (e) => {
                if (!dropdown.contains(e.target) && !e.target.closest('button')) {
                    dropdown.classList.remove('active');
                    document.removeEventListener('click', closeHandler);
                }
            };
            setTimeout(() => document.addEventListener('click', closeHandler), 10);
        }

        // Custom Theme Modal System
        const modalContainer = document.getElementById('custom-modal-container');
        const modalBox = document.getElementById('custom-modal');
        const modalTitle = document.getElementById('modal-title');
        const modalMessage = document.getElementById('modal-message');
        const modalConfirmBtn = document.getElementById('modal-confirm-btn');
        const modalCancelBtn = document.getElementById('modal-cancel-btn');
        const modalIconContainer = document.getElementById('modal-icon-container');
        const iconConfirm = document.getElementById('modal-icon-confirm');
        const iconAlert = document.getElementById('modal-icon-alert');

        let modalResolve = null;

        modalConfirmBtn.onclick = () => {
            modalContainer.classList.remove('active');
            setTimeout(() => { modalContainer.style.display = 'none'; }, 300);
            if (modalResolve) modalResolve(true);
        };

        modalCancelBtn.onclick = () => {
            modalContainer.classList.remove('active');
            setTimeout(() => { modalContainer.style.display = 'none'; }, 300);
            if (modalResolve) modalResolve(false);
        };

        function showThemeConfirm(title, message, isDanger = false) {
            return Swal.fire({
                title: title,
                html: message,
                icon: isDanger ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: isDanger ? '#dc2626' : '#191a23',
                cancelButtonColor: '#d1d5db',
                background: '#ffffff',
                color: '#191a23',
                customClass: {
                    confirmButton: 'rounded-xl px-6 py-2.5 text-sm font-bold border border-[#191a23]',
                    cancelButton: 'rounded-xl px-6 py-2.5 text-sm font-bold text-slate-700',
                    popup: 'rounded-2xl border border-slate-200 shadow-xl'
                }
            }).then((result) => {
                return result.isConfirmed;
            });
        }

        function showThemeAlert(message, redirectUrl = '') {
            return Swal.fire({
                title: 'แจ้งเตือน',
                html: message,
                icon: 'info',
                confirmButtonText: 'ตกลง',
                confirmButtonColor: '#191a23',
                background: '#ffffff',
                color: '#191a23',
                customClass: {
                    confirmButton: 'rounded-xl px-6 py-2.5 text-sm font-bold border border-[#191a23]',
                    popup: 'rounded-2xl border border-slate-200 shadow-xl'
                }
            }).then((result) => {
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                }
                return result.isConfirmed;
            });
        }

        // Override standard browser alert and confirm
        window.alert = function(message) {
            showThemeAlert(message);
        };

        window.confirm = function(message) {
            return Swal.fire({
                title: 'ยืนยันการทำรายการ',
                html: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'ตกลง',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#191a23',
                cancelButtonColor: '#d1d5db',
                background: '#ffffff',
                color: '#191a23'
            }).then((result) => {
                return result.isConfirmed;
            });
        };

        document.addEventListener('DOMContentLoaded', () => {
            <?php if (isset($_SESSION['alert_msg'])): ?>
            setTimeout(() => {
                showThemeAlert(<?= json_encode($_SESSION['alert_msg']) ?>);
            }, 500);
            <?php unset($_SESSION['alert_msg']); endif; ?>
        });
    </script>
</body>
</html>
