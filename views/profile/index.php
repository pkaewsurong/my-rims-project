<?php ob_start(); ?>
<!-- views/profile/index.php -->
<div class="mb-8">
    <div class="flex justify-between items-center bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-wide flex items-center">
                <svg class="w-6 h-6 mr-3 text-indigo-650" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                ข้อมูลส่วนตัว (My Profile)
            </h2>
            <p class="mt-1 text-sm text-slate-500 font-medium">ระบบบริหารงานวิจัยและนวัตกรรม (RIMS)</p>
        </div>
        <div>
            <a href="/public/profile/edit" class="px-5 py-2.5 bg-indigo-50 border border-indigo-200 text-indigo-650 hover:bg-indigo-100 rounded-xl text-sm font-semibold transition-colors shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                แก้ไขข้อมูล
            </a>
        </div>
    </div>
</div>

<div class="max-w-4xl mx-auto space-y-6">
    <!-- Profile Card -->
    <div class="card p-8 relative overflow-hidden group">
        <div class="relative z-10 flex flex-col md:flex-row gap-8 items-start md:items-center">
            
            <!-- Avatar Section -->
            <div class="flex-shrink-0 relative">
                <div class="w-32 h-32 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 p-1 shadow-md">
                    <div class="w-full h-full bg-white rounded-xl flex items-center justify-center overflow-hidden">
                        <div class="w-full h-full flex items-center justify-center text-4xl font-extrabold text-indigo-600">
                            <?= strtoupper(substr(e($user['name']), 0, 1)) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Info Section -->
            <div class="flex-grow space-y-4">
                <div>
                    <h3 class="text-3xl font-extrabold text-slate-800 mb-2"><?= e($user['name']) ?></h3>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-705 border border-indigo-200">
                            <?= e($user['role_name'] ?? 'ผู้ใช้งานระบบ') ?>
                        </span>
                        <?php if(!empty($metrics)): ?>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-705 border border-purple-200">
                            H-Index: <?= e($metrics['h_index']) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <p class="text-xs font-semibold text-slate-500 mb-1">อีเมลติดต่อ (Email)</p>
                        <p class="text-slate-800 font-bold"><?= e($user['email']) ?></p>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <p class="text-xs font-semibold text-slate-500 mb-1">วันที่เข้าร่วม (Joined)</p>
                        <p class="text-slate-800 font-bold"><?= date('F j, Y', strtotime($user['created_at'])) ?></p>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php'; 
?>
