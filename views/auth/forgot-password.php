<?php ob_start(); ?>

<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white border border-slate-200 rounded-2xl p-10 shadow-sm">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-650 mb-6 shadow-sm">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
            </div>
            <h2 class="text-3xl font-extrabold text-slate-800 tracking-wide">
                ลืมรหัสผ่าน?
            </h2>
            <p class="mt-2 text-sm text-slate-500">กรอกอีเมลของคุณเพื่อรับลิงก์สำหรับเปลี่ยนรหัสผ่านใหม่</p>
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

        <form class="space-y-6" action="<?= url('/forgot-password') ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            
            <div class="space-y-5">
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
            </div>

            <div class="pt-4">
                <button type="submit" class="btn-primary w-full flex justify-center py-3.5 px-4 rounded-xl text-sm font-bold tracking-wider">
                    ส่งลิงก์เปลี่ยนรหัสผ่าน
                </button>
            </div>
        </form>
        
        <div class="mt-8 text-center">
            <p class="text-sm text-slate-500">
                <a href="<?= url('/login') ?>" class="font-semibold text-indigo-600 hover:text-indigo-750 transition-colors underline decoration-indigo-200 underline-offset-4">กลับไปยังหน้าเข้าสู่ระบบ</a>
            </p>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php'; 
?>
