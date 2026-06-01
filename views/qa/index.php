<?php ob_start(); ?>

<div class="mb-8">
    <div class="flex justify-between items-center bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-wide flex items-center">
                <svg class="w-6 h-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                ตรวจสอบคุณภาพและประเมินผล (QA & Evaluation)
            </h2>
            <p class="mt-1 text-sm text-slate-500 font-medium">ระบบบริหารงานวิจัยและนวัตกรรม (RIMS) | สำหรับ: <span class="text-slate-800 font-bold">สถาบันวิจัย/ผู้บริหาร</span></p>
        </div>
        <div class="flex gap-3">
            <button onclick="document.getElementById('export-modal').classList.remove('hidden')" class="px-5 py-2.5 bg-indigo-50 border border-indigo-200 hover:bg-indigo-100 hover:text-indigo-705 rounded-xl text-sm font-semibold text-indigo-650 flex items-center transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                ออกรายงาน (Export)
            </button>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto pb-24 space-y-6">

    <!-- KPI Dashboard Preview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="card p-5 relative overflow-hidden group">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-slate-500">โครงการบรรลุเป้าหมาย</h3>
                <span class="p-2 bg-indigo-50 rounded-xl text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                </span>
            </div>
            <div class="flex items-baseline">
                <span class="text-3xl font-extrabold text-slate-800">42</span>
                <span class="ml-2 text-xs text-emerald-600 font-bold">+12% จากปีที่แล้ว</span>
            </div>
        </div>

        <div class="card p-5 relative overflow-hidden group">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-slate-500">ผลงานตีพิมพ์ (Q1/Q2)</h3>
                <span class="p-2 bg-indigo-50 rounded-xl text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </span>
            </div>
            <div class="flex items-baseline">
                <span class="text-3xl font-extrabold text-slate-800">15</span>
                <span class="ml-2 text-sm text-slate-500 font-semibold">เรื่อง</span>
            </div>
        </div>

        <div class="card p-5 relative overflow-hidden group">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-slate-500">ทรัพย์สินทางปัญญา</h3>
                <span class="p-2 bg-indigo-50 rounded-xl text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </span>
            </div>
            <div class="flex items-baseline">
                <span class="text-3xl font-extrabold text-slate-800">8</span>
                <span class="ml-2 text-sm text-slate-500 font-semibold">รายการ</span>
            </div>
        </div>
        
        <div class="card p-5 relative overflow-hidden group border-rose-200">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-slate-500">ตรวจพบความซ้ำซ้อน</h3>
                <span class="p-2 bg-rose-50 rounded-xl text-rose-605">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </span>
            </div>
            <div class="flex items-baseline">
                <span class="text-3xl font-extrabold text-rose-600">2</span>
                <span class="ml-2 text-xs text-slate-500 font-medium">โครงการ (รอตรวจสอบ)</span>
            </div>
        </div>
    </div>

    <!-- รายการรายงานฉบับสมบูรณ์ที่รอการตรวจสอบ -->
    <div class="card p-6 relative overflow-hidden">
        <div class="flex justify-between items-center border-b border-slate-100 pb-4 mb-4">
            <h3 class="text-lg font-bold text-slate-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                รายการรายงานฉบับสมบูรณ์รอตรวจสอบ (QA Queue)
            </h3>
            
            <div class="flex space-x-2">
                <select class="px-3 py-1.5 border border-slate-200 rounded-lg text-sm text-slate-600 focus:outline-none focus:ring-1 focus:ring-indigo-500 bg-white">
                    <option>ทั้งหมด</option>
                    <option>รอตรวจสอบ</option>
                    <option>พบความเสี่ยงสูง</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs uppercase tracking-wider text-slate-500 bg-slate-50">
                        <th class="px-4 py-3 rounded-tl-lg font-semibold">รหัสโครงการ</th>
                        <th class="px-4 py-3 font-semibold">ชื่อโครงการ (Title)</th>
                        <th class="px-4 py-3 font-semibold">นักวิจัย (Researcher)</th>
                        <th class="px-4 py-3 font-semibold">คะแนน QA (อัตโนมัติ)</th>
                        <th class="px-4 py-3 font-semibold">สถานะ Plagiarism</th>
                        <th class="px-4 py-3 font-semibold text-right rounded-tr-lg">จัดการ (Action)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($reports)): ?>
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-400 italic">ไม่มีรายการรอตรวจสอบ</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($reports as $report): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-4 text-sm font-semibold text-slate-600"><?= e($report['code']) ?></td>
                            <td class="px-4 py-4">
                                <p class="text-sm font-bold text-slate-800 truncate max-w-xs" title="<?= e($report['title']) ?>"><?= e($report['title']) ?></p>
                                <p class="text-xs text-slate-450 mt-1 font-medium">ส่งเมื่อ: <?= date('d M Y', strtotime($report['submission_date'])) ?></p>
                            </td>
                            <td class="px-4 py-4 text-sm font-medium text-slate-600"><?= e($report['researcher_name']) ?></td>
                            <td class="px-4 py-4">
                                <?php $score = rand(70, 95); ?>
                                <div class="flex items-center">
                                    <div class="w-full bg-slate-100 rounded-full h-2 mr-2 max-w-[4rem]">
                                        <div class="bg-<?= $score > 85 ? 'emerald' : 'amber' ?>-500 h-2 rounded-full" style="width: <?= $score ?>%"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-slate-650"><?= $score ?>/100</span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <?php $plag = rand(1, 25); ?>
                                <?php if($plag > 15): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                                        ซ้ำซ้อน <?= $plag ?>%
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                                        ผ่าน (<?= $plag ?>%)
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <a href="/public/projects/<?= $report['id'] ?>" class="text-indigo-600 hover:text-indigo-800 text-sm font-bold transition-colors">
                                    ตรวจประเมิน
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

<!-- Modal: Export Data -->
<div id="export-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="document.getElementById('export-modal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white border border-slate-200 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full">
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-lg leading-6 font-extrabold text-slate-800 flex items-center" id="modal-title">
                    <svg class="w-5 h-5 mr-2 text-indigo-650" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    ออกรายงานข้อมูลโครงการ (Export)
                </h3>
                <button type="button" onclick="document.getElementById('export-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 transition-colors focus:outline-none">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <form action="/public/qa/export" method="GET" class="px-6 py-5">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">ปีงบประมาณ</label>
                        <select name="year" class="block w-full px-4 py-2.5 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                            <option value="2026">2026</option>
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="all">ทั้งหมด (All Years)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">รูปแบบไฟล์ (Format) <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative flex cursor-pointer rounded-xl border border-slate-200 bg-slate-50 p-4 items-center justify-center hover:bg-slate-100 hover:border-indigo-300 transition-all">
                                <input type="radio" name="format" value="csv" class="peer sr-only" checked>
                                <span class="text-sm font-bold text-slate-700 peer-checked:text-indigo-600 flex flex-col items-center">
                                    <svg class="w-6 h-6 mb-1 text-slate-550 group-peer-checked:text-indigo-650" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    CSV / Excel
                                </span>
                                <span class="absolute inset-0 rounded-xl border-2 border-transparent peer-checked:border-indigo-500 pointer-events-none"></span>
                            </label>
                            <label class="relative flex cursor-pointer rounded-xl border border-slate-200 bg-slate-50 p-4 items-center justify-center opacity-40 cursor-not-allowed" title="Not available yet">
                                <input type="radio" name="format" value="pdf" class="peer sr-only" disabled>
                                <span class="text-sm font-bold text-slate-400 flex flex-col items-center">
                                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    PDF Report
                                </span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mt-4 text-xs text-slate-500 font-semibold">
                        * ระบบจะดึงข้อมูลตัวชี้วัด (KPIs), งบประมาณ, และจำนวนผลงาน (Outputs/IP) เพื่อนำไปใช้วิเคราะห์ผลกระทบ (Impact)
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('export-modal').classList.add('hidden')" class="px-5 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-650 bg-white hover:bg-slate-50 transition-colors">
                        ยกเลิก
                    </button>
                    <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-bold shadow-sm">
                        ดาวน์โหลด
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php'; 
?>
