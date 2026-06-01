<?php ob_start(); ?>
<!-- views/dashboard/researchers_list.php -->
<div class="mb-8">
    <div class="flex justify-between items-center bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center gap-4">
            <a href="/public/dashboard?year=<?= $year ?>" class="p-2.5 bg-slate-50 text-slate-500 hover:text-indigo-650 hover:bg-slate-100 rounded-xl transition-all border border-slate-200 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800 tracking-wide flex items-center">
                    นักวิจัยที่มีผลงานโดดเด่นทั้งหมด (All Researchers Ranking)
                </h2>
                <p class="mt-1 text-sm text-slate-500 font-medium">ระบบบริหารงานวิจัยและนวัตกรรม (RIMS) | ปีงบประมาณ <?= $year ?></p>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto pb-24 space-y-6">
    <div class="card p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs uppercase tracking-wider text-slate-500 bg-slate-50">
                        <th class="px-6 py-4 rounded-tl-lg font-semibold">อันดับ (Rank)</th>
                        <th class="px-6 py-4 font-semibold">ชื่อ-สกุล (Name)</th>
                        <th class="px-6 py-4 font-semibold text-center">โครงการที่สมบูรณ์ (Completed)</th>
                        <th class="px-6 py-4 font-semibold text-center">รวมทุนวิจัย (Total Grants - บาท)</th>
                        <th class="px-6 py-4 font-semibold text-center rounded-tr-lg">H-Index</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if(empty($researchers)): ?>
                        <tr><td colspan="5" class="px-6 py-8 text-center text-slate-400 italic">ไม่พบข้อมูลนักวิจัยในปีนี้</td></tr>
                    <?php else: ?>
                        <?php foreach($researchers as $i => $tr): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="w-10 h-10 rounded-full bg-slate-100 border <?=$i < 3 ? 'border-amber-300 text-amber-700 bg-amber-50':'border-slate-200 text-slate-550'?> flex items-center justify-center font-bold text-sm shadow-sm">#<?= $i+1 ?></div>
                            </td>
                            <td class="px-6 py-4 text-slate-800 font-bold">
                                <?= e($tr['name']) ?>
                            </td>
                            <td class="px-6 py-4 text-center text-emerald-600 font-extrabold text-lg">
                                <?= number_format($tr['completed_count'] ?? 0) ?>
                            </td>
                            <td class="px-6 py-4 text-center text-amber-600 font-bold">
                                <?= number_format($tr['total_grant'] ?? 0) ?>
                            </td>
                            <td class="px-6 py-4 text-center text-slate-700 font-mono font-extrabold text-lg">
                                <?= number_format($tr['h_index'] ?? 0) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php'; 
?>
