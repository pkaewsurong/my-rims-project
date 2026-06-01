<?php ob_start(); ?>
<!-- views/profile/edit.php -->
<div class="mb-8 flex justify-between items-center">
    <div class="flex items-center gap-4">
        <a href="/public/profile" class="p-2.5 bg-slate-50 text-slate-500 hover:text-indigo-650 hover:bg-slate-100 rounded-xl transition-all border border-slate-200 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-wide flex items-center">
                แก้ไขข้อมูลส่วนตัว
            </h2>
            <p class="text-sm text-slate-500 mt-1 font-medium">อัปเดตชื่อ อีเมล และรหัสผ่านของคุณ</p>
        </div>
    </div>
</div>

<div class="max-w-3xl mx-auto pb-24">
    <?php if (isset($_SESSION['alert_msg'])): ?>
    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 flex items-start gap-3 text-sm font-semibold">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span><?= e($_SESSION['alert_msg']) ?></span>
    </div>
    <?php unset($_SESSION['alert_msg']); endif; ?>

    <div class="card p-8 bg-white">
        <form action="/public/profile/update" method="POST" class="space-y-6">
            
            <!-- Default Info Section -->
            <div class="space-y-4 pb-6 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-800">ข้อมูลพื้นฐาน</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="md:col-span-1">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">คำนำหน้า</label>
                        <input type="text" name="prefix" value="<?= e($user['prefix'] ?? '') ?>"
                            class="w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm"
                            placeholder="ดร.">
                    </div>
                    
                    <div class="md:col-span-3">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">ชื่อ (First Name) <span class="text-rose-500">*</span></label>
                        <input type="text" name="first_name" value="<?= e($user['first_name'] ?? '') ?>" required
                            class="w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm"
                            placeholder="สมชาย">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">นามสกุล (Last Name) <span class="text-rose-500">*</span></label>
                        <input type="text" name="last_name" value="<?= e($user['last_name'] ?? '') ?>" required
                            class="w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm"
                            placeholder="ใจดี">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">อีเมล (Email) <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="<?= e($user['email']) ?>" required
                            class="w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                    </div>
                </div>
            </div>

            <!-- Security Section -->
            <div class="space-y-4 pt-2">
                <h3 class="text-lg font-bold text-slate-800">ความปลอดภัย (Security)</h3>
                <p class="text-xs text-slate-450 mb-4 font-semibold">เว้นว่างไว้หากไม่ต้องการเปลี่ยนรหัสผ่าน</p>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">รหัสผ่านใหม่</label>
                    <input type="password" name="password" placeholder="ตั้งรหัสผ่านใหม่ (Optional)" autocomplete="new-password"
                        class="w-full md:w-1/2 px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                </div>
            </div>

            <div class="pt-6 mt-8 border-t border-slate-100 flex justify-end gap-4">
                <a href="/public/profile" class="px-6 py-3 border border-slate-200 text-slate-655 bg-white hover:bg-slate-55 rounded-xl text-sm font-semibold transition-colors">
                    ยกเลิก
                </a>
                <button type="submit" class="btn-primary px-6 py-3 rounded-xl text-sm font-bold transition-colors shadow-sm">
                    บันทึกข้อมูล
                </button>
            </div>
        </form>
    </div>
</div>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php'; 
?>
