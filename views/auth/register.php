<?php ob_start(); ?>

<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white border border-slate-200 rounded-2xl p-10 shadow-sm">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-650 mb-6 shadow-sm">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            </div>
            <h2 class="text-3xl font-extrabold text-slate-800 tracking-wide">
                สมัครสมาชิก
            </h2>
            <p class="mt-2 text-sm text-slate-500">ลงทะเบียนผู้ใช้งานระบบ RIMS</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 mb-6">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-rose-500 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <p class="ml-3 text-sm text-rose-800"><?= e($error) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="bg-emerald-50 border border-emerald-250 rounded-xl p-4 mb-6">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-emerald-600 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.283 12.217l-3-3a1 1 0 00-1.414 1.414l3.707 3.707a1 1 0 001.414 0l7-7a1 1 0 00-1.414-1.414L8.283 12.217z" clip-rule="evenodd" />
                    </svg>
                    <p class="ml-3 text-sm text-emerald-800"><?= e($success) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <form class="space-y-6" action="/public/register" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            
            <div class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div class="sm:col-span-1">
                        <label for="prefix" class="block text-sm font-semibold text-slate-700 mb-1.5">คำนำหน้า</label>
                        <input id="prefix" name="prefix" type="text"
                            class="block w-full px-3 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-850 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm"
                            placeholder="ดร." value="<?= e($_POST['prefix'] ?? '') ?>">
                    </div>
                    <div class="sm:col-span-3">
                        <label for="first_name" class="block text-sm font-semibold text-slate-700 mb-1.5">ชื่อ (First Name)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </div>
                            <input id="first_name" name="first_name" type="text" autocomplete="given-name" required 
                                class="block w-full pl-10 pr-3 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-850 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" 
                                placeholder="สมชาย" value="<?= e($_POST['first_name'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-semibold text-slate-700 mb-1.5">นามสกุล (Last Name)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                        <input id="last_name" name="last_name" type="text" autocomplete="family-name" required 
                            class="block w-full pl-10 pr-3 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-850 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" 
                            placeholder="ใจดี" value="<?= e($_POST['last_name'] ?? '') ?>">
                    </div>
                </div>

                <div>
                    <label for="email-address" class="block text-sm font-semibold text-slate-700 mb-1.5">อีเมล (Email)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg>
                        </div>
                        <input id="email-address" name="email" type="email" autocomplete="email" required 
                            class="block w-full pl-10 pr-3 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-850 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" 
                            placeholder="you@university.ac.th" value="<?= e($_POST['email'] ?? '') ?>">
                    </div>
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">รหัสผ่าน (Password)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="new-password" required 
                            class="block w-full pl-10 pr-3 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-850 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" 
                            placeholder="••••••••">
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1.5">ยืนยันรหัสผ่าน (Confirm Password)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </div>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required 
                            class="block w-full pl-10 pr-3 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-850 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" 
                            placeholder="••••••••">
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="btn-primary w-full flex justify-center py-3.5 px-4 rounded-xl text-sm font-bold tracking-wider">
                    สร้างบัญชีผู้ใช้งาน
                </button>
            </div>
        </form>
        
        <div class="mt-8 text-center">
            <p class="text-sm text-slate-500">
                มีบัญชีอยู่แล้ว? 
                <a href="/public/login" class="font-semibold text-indigo-600 hover:text-indigo-750 transition-colors underline decoration-indigo-200 underline-offset-4">เข้าสู่ระบบ</a>
            </p>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php'; 
?>
