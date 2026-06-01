<?php ob_start(); ?>

<div class="mb-8">
    <div class="flex justify-between items-center bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-wide">
                สร้างรายงานเชิงกลยุทธ์ <span class="text-slate-400 font-normal text-lg">(Strategic Reports)</span>
            </h2>
            <p class="mt-1 text-sm text-slate-550 font-medium">ระบบบริหารงานวิจัยและนวัตกรรม (RIMS) | ส่งออกรายงานตามเกณฑ์ EdPEx, BSC, AUN-QA</p>
        </div>
        <div>
            <form action="/public/strategic-reports" method="GET" class="flex gap-2">
                <select name="year" class="px-4 py-2 border border-slate-200 rounded-xl bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm">
                    <?php for($y = date('Y')+543; $y >= date('Y')+539; $y--): ?>
                        <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition-colors shadow-sm">
                    ดูประวัติ
                </button>
            </form>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto pb-24 space-y-6">
    
    <?php if(isset($_SESSION['flash_success'])): ?>
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-600 p-4 rounded-xl mb-6 font-semibold text-sm">
        <?= e($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
    </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['flash_error'])): ?>
    <div class="bg-rose-50 border border-rose-200 text-rose-600 p-4 rounded-xl mb-6 font-semibold text-sm">
        <?= e($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
    </div>
    <?php endif; ?>

    <!-- Section 1: Configure & Generate -->
    <div class="card p-6 relative overflow-hidden">
        <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center border-b border-slate-100 pb-2">
            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            กำหนดค่าเกณฑ์ประเมิน (Configuration)
        </h3>
        
        <form action="/public/strategic-reports/generate" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">ปีงบประมาณที่ต้องการออกรายงาน</label>
                    <input type="number" name="year" value="<?= e($year) ?>" class="block w-full px-4 py-2 border border-slate-205 rounded-xl bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">หัวข้อรายงานย่อย (Optional)</label>
                    <input type="text" name="title" placeholder="เช่น รายงานประจำปีคณะฯ" class="block w-full px-4 py-2 border border-slate-205 rounded-xl bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">ประเภทรายงาน (Template)</label>
                    <select name="report_type" class="block w-full px-4 py-2 border border-slate-205 rounded-xl bg-slate-50 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" onchange="toggleCriteria(this.value)">
                        <option value="EdPEx">EdPEx (หมวด 7 ผลลัพธ์การดำเนินการ)</option>
                        <option value="BSC">BSC (Balanced Scorecard)</option>
                        <option value="AUN-QA">AUN-QA (Research Output)</option>
                        <option value="Custom">กำหนดเอง (Custom)</option>
                    </select>
                </div>
            </div>
            
            <div class="bg-slate-50 border border-slate-150 rounded-xl p-5" id="criteria-box">
                <label class="block text-sm font-bold text-indigo-650 mb-3">ตัวชี้วัดที่รวมในใบรายงาน (สำหรับ EdPEx)</label>
                <div class="space-y-3">
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" checked class="mt-1 mr-3 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-slate-600"><strong class="text-slate-800 block">7.1 ผลลัพธ์ด้านผลิตภัณฑ์และกระบวนการ</strong><span class="text-slate-400 text-xs font-semibold">จำนวนบทความ, สิทธิบัตรตีพิมพ์</span></span>
                    </label>
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" checked class="mt-1 mr-3 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-slate-600"><strong class="text-slate-800 block">7.3 ผลลัพธ์ด้านบุคลากร</strong><span class="text-slate-400 text-xs font-semibold">นักวิจัยที่มี H-Index ก้าวหน้า, ผลงานต่อ FTE</span></span>
                    </label>
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" checked class="mt-1 mr-3 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-slate-600"><strong class="text-slate-800 block">7.5 ผลลัพธ์ด้านการเงินและตลาด</strong><span class="text-slate-400 text-xs font-semibold">งบประมาณสนับสนุนจากภายนอก, ROI จากงานวิจัย</span></span>
                    </label>
                </div>
                
                <div class="mt-6 pt-4 border-t border-slate-200 flex flex-wrap gap-2">
                    <button type="submit" name="format" value="pdf" class="flex-1 px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl text-sm font-bold transition-colors border border-rose-200 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        PDF
                    </button>
                    <button type="submit" name="format" value="word" class="flex-1 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-xl text-sm font-bold transition-colors border border-indigo-150 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Word
                    </button>
                    <button type="submit" name="format" value="excel" class="flex-1 px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-600 rounded-xl text-sm font-bold transition-colors border border-emerald-200 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        Excel
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Section 2: History Table -->
    <div class="card p-6 relative overflow-hidden">
        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center border-b border-slate-100 pb-2">
            <svg class="w-5 h-5 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            ประวัติการออกรายงาน (ปีงบประมาณ <?= e($year) ?>)
        </h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs uppercase tracking-wider text-slate-500 bg-slate-50">
                        <th class="px-4 py-3 font-bold rounded-tl-xl">ชื่อรายงาน</th>
                        <th class="px-4 py-3 font-bold">รูปแบบ/เกณฑ์</th>
                        <th class="px-4 py-3 font-bold">วันที่สร้าง</th>
                        <th class="px-4 py-3 font-bold">สร้างโดย</th>
                        <th class="px-4 py-3 font-bold text-right rounded-tr-xl">คำสั่ง</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                    <?php if (empty($reports)): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400 italic">ยังไม่มีการประมวลผลออกรายงานในปีนี้</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($reports as $r): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-4">
                                <span class="text-sm font-bold text-slate-800"><?= e($r['title']) ?></span>
                            </td>
                            <td class="px-4 py-4">
                                <?php 
                                    $col = 'bg-slate-100 text-slate-550 border-slate-200';
                                    if($r['report_type'] == 'EdPEx') $col = 'bg-indigo-50 text-indigo-600 border-indigo-150';
                                    if($r['report_type'] == 'BSC') $col = 'bg-amber-50 text-amber-600 border-amber-200';
                                    if($r['report_type'] == 'AUN-QA') $col = 'bg-emerald-50 text-emerald-600 border-emerald-250';
                                ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold border <?= $col ?>">
                                    <?= e($r['report_type']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 text-sm text-slate-500"><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></td>
                            <td class="px-4 py-4 text-sm text-slate-500"><?= e($r['generator_name'] ?: 'System') ?></td>
                            <td class="px-4 py-4 text-right">
                                <a href="#" onclick="alert('Demo: ดาวน์โหลดไฟล์สำเร็จ')" class="text-indigo-600 hover:text-indigo-850 text-sm font-bold transition-colors flex items-center justify-end gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> ดาวน์โหลด
                               </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function toggleCriteria(type) {
        const titleLabel = document.querySelector('#criteria-box > label');
        if (type === 'EdPEx') {
            titleLabel.innerText = "ตัวชี้วัดที่รวมในใบรายงาน (สำหรับ EdPEx)";
        } else if (type === 'BSC') {
            titleLabel.innerText = "ตัวชี้วัดที่รวมในใบรายงาน (สำหรับ BSC)";
        } else if (type === 'AUN-QA') {
            titleLabel.innerText = "ตัวชี้วัดที่รวมในใบรายงาน (สำหรับ AUN-QA)";
        } else {
            titleLabel.innerText = "เลือกตัวชี้วัดที่ต้องการ";
        }
    }
</script>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php'; 
?>
