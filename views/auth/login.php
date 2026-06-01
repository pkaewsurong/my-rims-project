<?php ob_start(); ?>

<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white border border-slate-200 rounded-2xl p-10 shadow-sm">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-650 mb-6 shadow-sm">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
            </div>
            <h2 class="text-3xl font-extrabold text-slate-800 tracking-wide">
                เข้าสู่ระบบ
            </h2>
            <p class="mt-2 text-sm text-slate-500">เข้าถึงระบบบริหารงานวิจัย RIMS</p>
        </div>

        <form class="space-y-6" action="/public/login" method="POST">
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
                
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">รหัสผ่าน (Password)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required 
                            class="block w-full pl-10 pr-3 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-850 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" 
                            placeholder="••••••••">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <div class="flex items-center">
                    <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 border-slate-300 text-indigo-650 focus:ring-indigo-500 rounded transition-colors">
                    <label for="remember-me" class="ml-2 block text-sm text-slate-500">จดจำการเข้าสู่ระบบ</label>
                </div>
                
                <div class="text-sm">
                    <a href="<?= url('/forgot-password') ?>" class="font-medium text-indigo-600 hover:text-indigo-750 transition-colors">ลืมรหัสผ่าน?</a>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="btn-primary w-full flex justify-center py-3.5 px-4 rounded-xl text-sm font-bold tracking-wider">
                    เข้าสู่ระบบ
                </button>
            </div>
        </form>
        
        <div class="mt-8 text-center">
            <p class="text-sm text-slate-500">
                ยังไม่มีบัญชีผู้ใช้งาน? 
                <a href="/public/register" class="font-semibold text-indigo-600 hover:text-indigo-750 transition-colors underline decoration-indigo-200 underline-offset-4">สมัครสมาชิกใหม่</a>
            </p>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php'; 
?>
