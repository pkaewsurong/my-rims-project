<?php ob_start(); ?>

<div class="mb-8 flex justify-between items-center">
    <div>
        <h2 class="text-3xl font-extrabold text-slate-800 tracking-wide">
            รวมโครงการทั้งหมด <span class="text-slate-400 font-normal text-xl">(All Projects)</span>
        </h2>
        <p class="mt-2 text-sm text-slate-500">รายการโครงการวิจัยและนวัตกรรมทั้งหมดในระบบ</p>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 bg-slate-50">
                    <th class="px-6 py-4 font-bold">รหัสโครงการ</th>
                    <th class="px-6 py-4 font-bold">ชื่อโครงการ</th>
                    <th class="px-6 py-4 font-bold">หัวหน้าโครงการ</th>
                    <th class="px-6 py-4 font-bold text-center">ความก้าวหน้า</th>
                    <th class="px-6 py-4 font-bold text-center">สถานะ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if(!empty($projects) && is_countable($projects) && count($projects) > 0): ?>
                    <?php foreach($projects as $item): ?>
                        <tr class="group hover:bg-slate-50/85 transition-colors">
                            <td class="px-6 py-5 whitespace-nowrap">
                                <span class="font-mono bg-slate-100 px-2 py-1 rounded text-xs text-indigo-600 border border-slate-200 font-semibold"><?= e($item['code'] ?? '-') ?></span>
                            </td>
                            <td class="px-6 py-5">
                                <p class="text-sm font-bold text-slate-800 line-clamp-2"><?= e($item['title']) ?></p>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <span class="text-sm text-slate-650 font-medium"><?= e($item['researcher_name'] ?? '-') ?></span>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-xs font-bold <?= ($item['total_progress'] >= 100) ? 'text-emerald-600' : 'text-amber-600' ?> mb-1.5"><?= ($item['total_progress'] ?? 0) ?>%</span>
                                    <div class="w-24 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full <?= ($item['total_progress'] >= 100) ? 'bg-emerald-550' : 'bg-amber-500' ?> transition-all duration-1000" style="width: <?= min(100, ($item['total_progress'] ?? 0)) ?>%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center whitespace-nowrap">
                                <?php
                                    $statusRaw = strtolower($item['status_name'] ?? '');
                                    $statusLabel = e($item['status_name']);
                                    $statusBadgeClass = 'bg-slate-100 text-slate-500 border-slate-200';
                                    
                                    if ($statusRaw === 'closed') {
                                        $statusLabel = 'ปิดโครงการ';
                                        $statusBadgeClass = 'bg-rose-50 text-rose-600 border-rose-200';
                                    } elseif ($statusRaw === 'completed') {
                                        $statusLabel = 'เสร็จสิ้น';
                                        $statusBadgeClass = 'bg-indigo-50 text-indigo-600 border-indigo-205';
                                    } else {
                                        $statusLabel = 'กำลังดำเนินการ';
                                        $statusBadgeClass = 'bg-emerald-50 text-emerald-600 border-emerald-250';
                                    }
                                    
                                    if ($item['closure_requested'] ?? false) {
                                        $statusLabel = 'รอปิดโครงการ';
                                        $statusBadgeClass = 'bg-amber-50 text-amber-650 border-amber-200';
                                    }
                                ?>
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full border <?= $statusBadgeClass ?> uppercase tracking-wider">
                                    <?= $statusLabel ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">
                            ไม่พบโครงการวิจัยในระบบ
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php'; 
?>
