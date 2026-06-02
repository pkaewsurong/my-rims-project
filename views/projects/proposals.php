<?php ob_start(); ?>

<div class="mb-8 flex justify-between items-center">
    <div>
        <h2 class="text-3xl font-extrabold text-slate-800 tracking-wide">
            ข้อเสนอโครงการ <span class="text-slate-400 font-normal text-xl">(Proposals)</span>
        </h2>
        <p class="mt-2 text-sm text-slate-500">รายการข้อเสนอโครงการที่รอการพิจารณาหรือได้รับอนุมัติแล้ว</p>
    </div>
</div>

<div class="card overflow-hidden">
    <ul class="divide-y divide-slate-100">
        <?php if(!empty($proposals) && is_countable($proposals) && count($proposals) > 0): ?>
            <?php foreach($proposals as $proposal): ?>
                <li class="group transition-all duration-300 hover:bg-slate-50 relative overflow-hidden">
                    <div class="absolute left-0 top-0 h-full w-1 bg-indigo-600 transform scale-y-0 group-hover:scale-y-100 transition-transform origin-top"></div>
                    <div class="px-6 py-5">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-lg font-bold text-slate-800 truncate group-hover:text-indigo-650 transition-colors">
                                <?= e($proposal['title']) ?>
                            </p>
                            <div class="ml-4 flex-shrink-0 flex items-center gap-3">
                                <?php if (($proposal['closure_requested'] ?? false)): ?>
                                    <a href="/public/projects/<?= e($proposal['project_id']) ?>?readonly=1" 
                                       class="btn-primary px-3 py-1.5 rounded-lg text-xs font-semibold tracking-wide flex items-center gap-2 group/btn uppercase whitespace-nowrap shadow-sm" 
                                       title="รายงานความก้าวหน้า">
                                        <svg class="w-4 h-4 group-hover/btn:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        รายงานความก้าวหน้า
                                    </a>
                                <?php endif; ?>

                                <?php 
                                    $statusRaw = strtolower($proposal['status']);
                                    $statusLabel = e($proposal['status']);
                                    $badgeClass = 'bg-amber-50 text-amber-600 border-amber-200';
                                    
                                    if ($statusRaw === 'closed') {
                                        $statusLabel = 'ปิดโครงการ (Closed)';
                                        $badgeClass = 'bg-rose-50 text-rose-600 border-rose-200';
                                    } elseif ($statusRaw === 'approved') {
                                        $statusLabel = 'อนุมัติแล้ว (Approved)';
                                        $badgeClass = 'bg-emerald-50 text-emerald-600 border-emerald-250';
                                    } elseif ($statusRaw === 'draft') {
                                        $statusLabel = 'ฉบับร่าง (Draft)';
                                        $badgeClass = 'bg-slate-50 text-slate-500 border-slate-200';
                                    } elseif ($statusRaw === 'submitted') {
                                        $statusLabel = 'รอตรวจสอบ (Submitted)';
                                        $badgeClass = 'bg-indigo-50 text-indigo-600 border-indigo-200';
                                    } elseif ($statusRaw === 'under_review') {
                                        $statusLabel = 'อยู่ระหว่างพิจารณา (Under Review)';
                                        $badgeClass = 'bg-amber-50 text-amber-600 border-amber-200';
                                    }
                                ?>
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full border <?= $badgeClass ?>">
                                    <?= $statusLabel ?>
                                </span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center text-sm text-slate-500">
                            <div class="flex items-center space-x-4">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    งบประมาณ (Budget): <span class="text-indigo-600 ml-1 font-semibold">฿<?= number_format($proposal['budget_total'], 2) ?></span>
                                </span>
                                <?php if (!empty($proposal['pi_name'])): ?>
                                <span class="flex items-center border-l border-slate-200 pl-4">
                                    <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    นักวิจัยหลัก (PI): <span class="text-slate-700 ml-1 font-medium"><?= e($proposal['pi_name']) ?></span>
                                </span>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-end space-x-6">
                                <span class="flex items-center pb-2">
                                    <svg class="w-4 h-4 mr-1.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    ยื่นเมื่อ: <?= date('j M Y', strtotime($proposal['created_at'])) ?>
                                </span>
                                <div class="flex items-end gap-3">
                                    <!-- Left Column: Manage Budget -->
                                    <div class="flex flex-col gap-2 items-end">
                                        <?php if (hasRole('admin') || strtolower($proposal['status']) === 'approved'): ?>
                                            <?php if (strtolower($proposal['status']) !== 'approved'): ?>
                                                <a href="javascript:void(0)" onclick="showThemeAlert('โครงการยังอยู่ในสถานะ <?= e($proposal['status']) ?> ยังไม่สามารถเข้าไปจัดการงบประมาณได้')" class="text-slate-400 font-semibold transition-colors flex items-center bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200 cursor-not-allowed whitespace-nowrap text-xs">
                                                    <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    จัดการงบประมาณ (Budget)
                                                </a>
                                            <?php else: ?>
                                                <a href="/public/proposals/budget?id=<?= $proposal['id'] ?>" class="text-indigo-650 hover:text-indigo-850 font-semibold transition-colors flex items-center bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-150 whitespace-nowrap text-xs">
                                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    จัดการงบประมาณ (Budget)
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                    <!-- Right Column: View Details/Edit and Review -->
                                    <div class="flex flex-col gap-2 items-end">
                                        <?php if ($statusRaw === 'draft'): ?>
                                            <a href="/public/proposals/<?= $proposal['id'] ?>/edit" class="text-indigo-650 hover:text-indigo-850 font-semibold transition-colors flex items-center bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-150 whitespace-nowrap text-xs">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                แก้ไข / กรอกข้อมูล
                                            </a>
                                        <?php elseif ($statusRaw === 'submitted'): ?>
                                            <a href="/public/proposals/<?= $proposal['id'] ?>" class="text-indigo-650 hover:text-indigo-850 font-semibold transition-colors flex items-center bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-150 whitespace-nowrap text-xs">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                ดูรายละเอียด
                                            </a>
                                        <?php endif; ?>

                                        <?php if (hasRole('admin')): ?>
                                            <a href="/public/proposals/review?id=<?= $proposal['id'] ?>" class="text-indigo-650 hover:text-indigo-850 font-semibold transition-colors flex items-center bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-150 whitespace-nowrap text-xs">
                                                <i class="bi bi-book-half mr-1.5 text-sm"></i>
                                                พิจารณา (Review)
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="px-6 py-8 text-center text-slate-400 italic">
                <div class="inline-flex justify-center items-center w-16 h-16 rounded-full bg-slate-50 mb-4 border border-slate-100">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <p>ยังไม่มีข้อเสนอโครงการ (No proposals found)</p>
            </li>
        <?php endif; ?>
    </ul>
</div>

<?php 
$content = ob_get_clean();
?>

<script>
async function handleApproveClosure(id) {
    const confirmed = await showThemeConfirm(
        'ยืนยันการปิดโครงการ',
        'คุณแน่ใจหรือไม่ที่จะอนุมัติการปิดโครงการนี้? การดำเนินการนี้ไม่สามารถย้อนกลับได้และจะส่งผลต่อสถิติการวิจัย',
        true
    );
    if (confirmed) {
        window.location.href = `/public/projects/approve-closure?id=${id}`;
    }
}
</script>

<?php
require __DIR__ . '/../layouts/app.php'; 
?>
