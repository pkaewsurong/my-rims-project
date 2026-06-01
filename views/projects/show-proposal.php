<?php ob_start(); ?>

<div class="mb-8">
    <a href="javascript:history.back()" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-indigo-600 transition-colors mb-4">
        <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        ย้อนกลับ
    </a>
    <div class="flex justify-between items-center bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-wide">
                รายละเอียดข้อเสนอโครงการ
            </h2>
            <p class="mt-1 text-sm text-slate-550 font-medium">ข้อมูลการยื่นเสนอโครงการวิจัย | สถานะ: 
                <?php
                    $statusRaw = strtolower($proposal['status'] ?? '');
                    $statusColor = 'text-slate-500';
                    if ($statusRaw === 'under_review') $statusColor = 'text-amber-600';
                    elseif ($statusRaw === 'approved') $statusColor = 'text-emerald-600';
                    elseif ($statusRaw === 'rejected') $statusColor = 'text-rose-600';
                    elseif ($statusRaw === 'draft') $statusColor = 'text-indigo-600';
                ?>
                <span class="<?= $statusColor ?> font-bold uppercase"><?= e($proposal['status']) ?></span>
            </p>
        </div>
        <div class="text-right flex flex-col items-end gap-2">
            <div>
                <span class="text-xs text-slate-400 block mb-1 font-semibold">เลขที่อ้างอิง (Ref ID)</span>
                <span class="text-slate-700 font-mono bg-slate-50 px-3 py-1 rounded-lg border border-slate-200 font-bold">#<?= str_pad($proposal['id'], 6, '0', STR_PAD_LEFT) ?></span>
            </div>
            
            <?php if (hasRole('admin') && ($proposal['closure_requested'] ?? false)): ?>
                <button onclick="handleApproveClosure(<?= e($proposal['project_id']) ?>)"
                   class="px-5 py-3 bg-rose-600 text-white rounded-xl text-xs font-bold hover:bg-rose-700 transition-all flex items-center gap-3 border border-rose-350 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    อนุมัติปิดโครงการ
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-12">
    <!-- Main Content -->
    <div class="lg:col-span-8 space-y-6">
        <div class="card p-8 relative overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                <h3 class="text-xl font-bold text-slate-800 flex items-center">
                    <svg class="w-6 h-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    ข้อมูลพื้นฐานโครงการ
                </h3>
            </div>
            
            <div class="space-y-6">
                <div>
                    <label class="block text-xs uppercase tracking-widest text-slate-400 font-bold mb-2">ชื่อโครงการ (ภาษาไทย)</label>
                    <p class="text-lg text-slate-800 leading-relaxed font-bold"><?= e($proposal['title']) ?></p>
                </div>

                <?php if($proposal['title_en']): ?>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-slate-400 font-bold mb-2">Project Title (English)</label>
                    <p class="text-md text-slate-600 italic leading-relaxed font-semibold"><?= e($proposal['title_en']) ?></p>
                </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4">
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-slate-400 font-bold mb-2">ประเภทการวิจัย</label>
                        <div class="flex items-center group">
                            <span class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center mr-3 border border-indigo-100 text-indigo-600 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            </span>
                            <span class="text-slate-800 font-semibold"><?= e($proposal['research_type']) ?: 'ไม่ได้ระบุ' ?></span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-slate-400 font-bold mb-2">ระยะเวลาดำเนินการ</label>
                        <div class="flex items-center">
                            <span class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center mr-3 border border-indigo-100 text-indigo-600 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </span>
                            <span class="text-slate-800 font-semibold">
                                <?= $proposal['start_date'] ? date('d M Y', strtotime($proposal['start_date'])) : '-' ?> 
                                ถึง 
                                <?= $proposal['end_date'] ? date('d M Y', strtotime($proposal['end_date'])) : '-' ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100">
                    <label class="block text-xs uppercase tracking-widest text-slate-400 font-bold mb-3">บทคัดย่อ / วัตถุประสงค์ (Abstract)</label>
                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-150 text-slate-650 leading-relaxed whitespace-pre-wrap italic">
                        <?= e($proposal['abstract']) ?>
                    </div>
                </div>

                <?php if($proposal['keywords']): ?>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-slate-400 font-bold mb-2">คำสำคัญ (Keywords)</label>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach(explode(',', $proposal['keywords']) as $kw): ?>
                            <span class="px-3 py-1 rounded-full bg-slate-50 border border-slate-200 text-xs text-slate-500 font-medium">#<?= trim(e($kw)) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Milestones Info -->
        <div class="card p-8">
            <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center border-b border-slate-100 pb-4">
                <svg class="w-5 h-5 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                แผนงานหลัก (Milestones)
            </h3>
            
            <?php 
                $milestones = [];
                if (!empty($proposal['milestones'])) {
                    $decoded = json_decode($proposal['milestones'], true);
                    if (is_array($decoded)) {
                        $milestones = $decoded;
                    } else {
                        $milestones = [['name' => 'ภาพรวม', 'description' => $proposal['milestones']]];
                    }
                }
            ?>
            
            <?php if(empty($milestones)): ?>
                <div class="text-slate-400 text-sm italic">ไม่มีข้อมูล Milestones</div>
            <?php else: ?>
                <div class="space-y-4">
                <?php foreach($milestones as $index => $m): ?>
                    <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl flex">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center mr-4 text-indigo-650 font-bold border border-indigo-150"><?= $index + 1 ?></div>
                        <div class="flex-grow">
                            <p class="text-slate-800 font-bold mb-1"><?= e($m['name'] ?? 'ไม่มีชื่อระยะ') ?></p>
                            <p class="text-sm text-slate-550 whitespace-pre-wrap leading-relaxed"><?= e($m['description'] ?? '') ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Team -->
        <div class="card p-8">
            <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center border-b border-slate-100 pb-4">
                <svg class="w-5 h-5 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                คณะทำงานโครงการ (Research Team)
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl flex items-center">
                    <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center mr-4 text-indigo-600 font-bold border border-indigo-100">PI</div>
                    <div>
                        <p class="text-slate-800 font-bold"><?= e($proposal['user_name'] ?? 'N/A') ?></p>
                        <p class="text-xs text-slate-500 font-medium">หัวหน้าโครงการ (Principal Investigator) | <?= $proposal['pi_proportion'] ?? 100 ?>%</p>
                    </div>
                </div>
                <?php foreach($teams as $member): ?>
                    <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl flex items-center">
                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center mr-4 text-slate-500 font-bold border border-slate-200">Co</div>
                        <div>
                            <p class="text-slate-800 font-bold"><?= e($member['name']) ?></p>
                            <p class="text-xs text-slate-500 font-medium"><?= e($member['role']) ?> | <?= $member['proportion'] ?>%</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="lg:col-span-4 space-y-6">
        <!-- Budget Card -->
        <div class="card p-8 border-t-4 border-t-indigo-600 shadow-sm">
            <h3 class="text-slate-400 text-xs uppercase tracking-widest font-bold mb-4">งบประมาณที่เสนอขอ</h3>
            <div class="mb-4">
                <span class="text-4xl font-extrabold text-slate-800">฿<?= number_format($proposal['budget_total'], 2) ?></span>
                <span class="text-slate-550 text-sm ml-1">บาท</span>
            </div>
            <div class="space-y-3 pt-4 border-t border-slate-100">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-slate-500">แหล่งทุน:</span>
                    <span class="text-indigo-600 font-bold"><?= e($proposal['funding_source_name']) ?: 'ทุนภายใน' ?></span>
                </div>
                <?php if($proposal['budget_details']): ?>
                <div class="mt-4">
                    <span class="text-slate-500 text-xs block mb-2 font-semibold">คำอธิบายงบประมาณ:</span>
                    <p class="text-slate-600 text-xs leading-relaxed"><?= e($proposal['budget_details']) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Documents -->
        <div class="card p-8">
            <h3 class="text-slate-800 font-bold text-sm mb-4 flex items-center">
                <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                เอกสารแนบ
            </h3>
            <div class="space-y-3">
                <?php if($proposal['file_proposal']): ?>
                <a href="/public/uploads/proposals/<?= e($proposal['file_proposal']) ?>" target="_blank" class="flex items-center p-3 rounded-xl bg-slate-50 border border-slate-200 hover:bg-slate-100 hover:border-indigo-650 transition-all group">
                    <div class="p-2 rounded-lg bg-rose-50 text-rose-600 mr-3 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-slate-800 text-xs font-bold leading-none mb-1">Full Proposal</p>
                        <p class="text-[10px] text-slate-400">PDF Document</p>
                    </div>
                </a>
                <?php endif; ?>

                <?php if($proposal['file_budget']): ?>
                <a href="/public/uploads/proposals/<?= e($proposal['file_budget']) ?>" target="_blank" class="flex items-center p-3 rounded-xl bg-slate-50 border border-slate-200 hover:bg-slate-100 hover:border-indigo-650 transition-all group">
                    <div class="p-2 rounded-lg bg-emerald-50 text-emerald-600 mr-3 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-slate-800 text-xs font-bold leading-none mb-1">Budget Table</p>
                        <p class="text-[10px] text-slate-400">Excel / Worksheet</p>
                    </div>
                </a>
                <?php endif; ?>

                <?php if($proposal['file_cv']): ?>
                <a href="/public/uploads/proposals/<?= e($proposal['file_cv']) ?>" target="_blank" class="flex items-center p-3 rounded-xl bg-slate-50 border border-slate-200 hover:bg-slate-100 hover:border-indigo-650 transition-all group">
                    <div class="p-2 rounded-lg bg-indigo-50 text-indigo-605 mr-3 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div>
                        <p class="text-slate-800 text-xs font-bold leading-none mb-1">Researcher CVs</p>
                        <p class="text-[10px] text-slate-400">Combined Profiles</p>
                    </div>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
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
