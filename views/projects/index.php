<?php ob_start(); ?>

<div class="mb-8 flex justify-between items-center">
    <div>
        <h2 class="text-3xl font-extrabold text-slate-800 tracking-wide">
            โครงการของฉัน <span class="text-slate-400 font-normal text-xl">(My Projects)</span>
        </h2>
        <p class="mt-2 text-sm text-slate-500">รายการโครงการวิจัยและข้อเสนอโครงการของคุณ</p>
    </div>
    
    <a href="/public/proposals/create" class="btn-primary px-6 py-3 rounded-xl text-sm font-semibold shadow-sm flex items-center">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        ยื่นข้อเสนอโครงการใหม่
    </a>
</div>

<div class="card overflow-hidden">
    <ul class="divide-y divide-slate-100">
        <?php if(!empty($all_items) && is_countable($all_items) && count($all_items) > 0): ?>
            <?php foreach($all_items as $item): ?>
                <li class="group transition-all duration-300 hover:bg-slate-50 relative overflow-hidden">
                    <?php 
                        $isProject = $item['item_type'] === 'project'; 
                        
                        $statusRaw = strtolower($item['status_name'] ?? '');
                        $statusLabel = e($item['status_name']);
                        $statusBadgeClass = 'bg-slate-100 text-slate-650 border-slate-200';
                        
                        if ($isProject) {
                            if ($statusRaw === 'closed') {
                                $statusLabel = 'ปิดโครงการ (Closed)';
                                $statusBadgeClass = 'bg-rose-50 text-rose-600 border-rose-200';
                            } elseif ($statusRaw === 'completed') {
                                $statusLabel = 'เสร็จสิ้น (Completed)';
                                $statusBadgeClass = 'bg-indigo-50 text-indigo-600 border-indigo-200';
                            } else {
                                $statusLabel = 'กำลังดำเนินการ (Ongoing)';
                                $statusBadgeClass = 'bg-emerald-50 text-emerald-600 border-emerald-250';
                            }
                            // Overwrite badge if closure requested
                            if (isset($item['closure_requested']) && $item['closure_requested']) {
                                $statusLabel = 'อยู่ระหว่างขอปิดโครงการ (Closure Pending)';
                                $statusBadgeClass = 'bg-amber-50 text-amber-600 border-amber-200';
                            }
                        } else {
                            if ($statusRaw === 'draft') {
                                $statusLabel = 'แบบร่าง (Draft)';
                                $statusBadgeClass = 'bg-slate-50 text-slate-500 border-slate-200';
                            } elseif ($statusRaw === 'under_review') {
                                $statusLabel = 'อยู่ระหว่างพิจารณา (Under Review)';
                                $statusBadgeClass = 'bg-amber-50 text-amber-600 border-amber-200';
                            } elseif ($statusRaw === 'approved') {
                                $statusLabel = 'อนุมัติแล้ว (Approved)';
                                $statusBadgeClass = 'bg-emerald-50 text-emerald-600 border-emerald-250';
                            } elseif ($statusRaw === 'rejected') {
                                $statusLabel = 'ไม่อนุมัติ (Rejected)';
                                $statusBadgeClass = 'bg-rose-50 text-rose-600 border-rose-200';
                            }
                        }
                    ?>
                    <div class="absolute left-0 top-0 h-full w-1 <?= $isProject ? 'bg-indigo-650' : 'bg-indigo-400' ?> transform scale-y-0 group-hover:scale-y-100 transition-transform origin-top"></div>
                    <div class="block">
                        <div class="px-6 py-5">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <?php if(!$isProject): ?>
                                        <span class="mr-3 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-600 border border-indigo-100">Proposal</span>
                                    <?php endif; ?>
                                    <a href="<?= $isProject ? '/public/projects/' . e($item['id']) : '/public/proposals/' . e($item['proposal_id'] ?? $item['id']) ?>" class="block group/title">
                                        <p class="text-lg font-bold text-slate-800 truncate group-hover/title:text-indigo-650 transition-colors">
                                            <?= e($item['title']) ?>
                                        </p>
                                    </a>
                                </div>
                                <div class="ml-4 flex-shrink-0 flex items-center space-x-3">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border <?= $statusBadgeClass ?>">
                                        <?= $statusLabel ?>
                                    </span>
                                    <?php if ($isProject && $statusRaw !== 'closed' && !($item['closure_requested'] ?? false)): 
                                        $progressReady = ($item['total_progress'] ?? 0) >= 100;
                                        $reportReady = in_array($item['final_report_status'] ?? '', ['submitted', 'approved']);
                                        $allReady = $progressReady && $reportReady;
                                        
                                        $btnClass = $allReady 
                                            ? 'bg-amber-50 text-amber-600 hover:bg-amber-100 border-amber-200' 
                                            : 'bg-slate-50 text-slate-400 border-slate-200 cursor-not-allowed opacity-60';
                                        
                                        $titleHint = '';
                                        if (!$progressReady) {
                                            $titleHint = 'รายงานความก้าวหน้ายังไม่ครบ 100% (ปัจจุบัน ' . ($item['total_progress'] ?? 0) . '%)';
                                        } elseif (!$reportReady) {
                                            $titleHint = 'กรุณาส่งรายงานฉบับสมบูรณ์ (Final Report) ก่อน';
                                        } else {
                                            $titleHint = 'เสนอขอปิดโครงการ';
                                        }
                                    ?>
                                        <button onclick="handleClosureRequest(<?= $item['id'] ?>, <?= ($item['total_progress'] ?? 0) ?>, <?= $reportReady ? 'true' : 'false' ?>)"
                                           class="px-5 py-2.5 <?= $btnClass ?> rounded-xl text-xs font-bold border transition-all z-10 flex items-center gap-2 group/btn uppercase tracking-wider" 
                                           title="<?= $titleHint ?>">
                                            <svg class="w-4 h-4 <?= $progressReady ? 'group-hover/btn:scale-110' : '' ?> transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            เสนอขอปิดโครงการ
                                        </button>
                                    <?php endif; ?>

                                    <?php 
                                        $viewUrl = $isProject ? '/public/projects/' . e($item['id']) : '/public/proposals/' . e($item['proposal_id'] ?? $item['id']); 
                                    ?>

                                    <?php if (!$isProject && $statusRaw === 'draft'): ?>
                                        <!-- Draft proposal: Edit button -->
                                        <a href="/public/proposals/<?= e($item['proposal_id'] ?? $item['id']) ?>/edit"
                                           class="btn-primary px-5 py-2.5 rounded-xl text-xs font-semibold tracking-wide flex items-center gap-2 group/btn uppercase transition-all z-10 shadow-sm" 
                                           title="แก้ไขข้อเสนอโครงการ">
                                            <svg class="w-4 h-4 group-hover/btn:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            แก้ไข / กรอกข้อมูล
                                        </a>
                                        <button onclick="handleDeleteDraft(<?= e($item['proposal_id'] ?? $item['id']) ?>)"
                                           class="px-5 py-2.5 bg-rose-50 text-rose-600 border border-rose-200 rounded-xl text-xs font-bold tracking-wide flex items-center gap-2 group/btn uppercase transition-all z-10 hover:bg-rose-100"
                                           title="ลบโครงการแบบร่าง">
                                            <svg class="w-4 h-4 transition-transform group-hover/btn:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            ลบ (Delete)
                                        </button>
                                    <?php else: ?>
                                        <a href="javascript:void(0)" 
                                           onclick="handleUpdateStatus(<?= $isProject ? 'true' : 'false' ?>, '<?= $statusRaw ?>', '<?= $viewUrl ?>')"
                                           class="btn-primary px-5 py-2.5 rounded-xl text-xs font-semibold tracking-wide flex items-center gap-2 group/btn uppercase transition-all z-10 shadow-sm" 
                                           title="อัปเดตสถานะ">
                                            <svg class="w-4 h-4 group-hover/btn:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            อัปเดตสถานะ
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex justify-between items-center text-sm text-slate-500">
                                <div class="flex items-center space-x-4">
                                    <?php if($isProject): ?>
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        รหัสโครงการ (Code): <span class="text-slate-700 ml-1 font-semibold"><?= e($item['code']) ?></span>
                                    </span>
                                    <span class="flex items-center px-2 py-0.5 rounded bg-slate-50 border border-slate-200 text-[10px]">
                                        <span class="text-slate-400 mr-1.5">Progress:</span>
                                        <span class="<?= ($item['total_progress'] >= 100) ? 'text-emerald-600' : 'text-amber-600' ?> font-bold"><?= ($item['total_progress'] ?? 0) ?>%</span>
                                    </span>
                                    <?php else: ?>
                                    <span class="text-slate-400 italic">ข้อเสนอโครงการ (Proposal)</span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <?= $isProject ? 'เริ่มต้นเมื่อ' : 'ยื่นเมื่อ' ?>: <time class="text-slate-700 ml-1"><?= date('d M Y', strtotime($item['start_date'])) ?></time>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="px-6 py-8 text-center text-slate-500 italic">
                <div class="inline-flex justify-center items-center w-16 h-16 rounded-full bg-slate-50 mb-4 border border-slate-100">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                </div>
                <p>ไม่พบโครงการวิจัย (No projects found)</p>
            </li>
        <?php endif; ?>
    </ul>
</div>

<?php 
$content = ob_get_clean();
?>

<script>
async function handleClosureRequest(id, progress, reportSubmitted) {
    if (progress < 100) {
        await showThemeAlert('รายงานยังไม่สมบูรณ์ ไม่สามารถขอเสนอปิดโครงการได้<br><span class="text-xs text-amber-600 mt-2 block">ความก้าวหน้าปัจจุบัน: ' + progress + '%</span>');
        return;
    }
    if (!reportSubmitted) {
        await showThemeAlert('ไม่สามารถขอเสนอปิดโครงการได้<br><span class="text-xs text-amber-600 mt-2 block">กรุณาส่งรายงานฉบับสมบูรณ์ (Final Report) ก่อนเสนอขอปิดโครงการ</span>');
        return;
    }
    const confirmed = await showThemeConfirm(
        'เสนอขอปิดโครงการ',
        'คุณแน่ใจหรือไม่ที่จะยื่นเสนอขอปิดโครงการนี้? ระบบจะส่งการแจ้งเตือนไปยังผู้ดูแลระบบเพื่อพิจารณา'
    );
    if (confirmed) {
        window.location.href = `/public/projects/request-closure?id=${id}`;
    }
}

async function handleUpdateStatus(isProject, status, url) {
    if (!isProject && status !== 'approved') {
        await showThemeAlert('ข้อเสนอโครงการยังไม่ได้รับการอนุมัติ ไม่สามารถอัปเดตสถานะได้<br><span class="text-xs text-amber-600 mt-2 block">สถานะปัจจุบัน: ' + status.toUpperCase() + '</span>');
        return;
    }
    window.location.href = url;
}

async function handleDeleteDraft(id) {
    const confirmed = await showThemeConfirm(
        'ลบโครงการแบบร่าง',
        'คุณแน่ใจหรือไม่ที่จะลบโครงการแบบร่างนี้? ข้อมูลทั้งหมดรวมถึงไฟล์แนบจะถูกลบออกอย่างถาวรและไม่สามารถเรียกคืนได้'
    );
    if (confirmed) {
        window.location.href = `/public/proposals/${id}/delete`;
    }
}
</script>

<?php
require __DIR__ . '/../layouts/app.php'; 
?>
