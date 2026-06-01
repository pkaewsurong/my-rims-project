<?php ob_start(); ?>

<div class="mb-8">
    <a href="/public/dashboard" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-indigo-650 transition-colors mb-4">
        <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        กลับสู่หน้าหลัก
    </a>
    <div class="flex justify-between items-center bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-wide flex items-center">
                <svg class="w-6 h-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                คำนวณตัวชี้วัดสมรรถนะวิจัย (Research Metrics)
            </h2>
            <p class="mt-1 text-sm text-slate-500 font-medium">ระบบบริหารงานวิจัยและนวัตกรรม (RIMS) | ประมวลผล H-Index, Citation Count, และ External Grants</p>
        </div>
        <div class="text-right font-medium">
            <p class="text-sm text-slate-500">หน่วยวัด: <span class="text-slate-800 font-bold">ระดับมหาวิทยาลัย/คณะ/บุคคล</span></p>
            <p class="text-xs text-slate-400 mt-1">ข้อมูล ณ: สิ้นปีงบประมาณ <?= e($year) ?></p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto pb-24 space-y-6">

    <!-- Section 1: Key Performance Metrics -->
    <div class="card p-6 relative overflow-hidden">
        <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center border-b border-slate-100 pb-3">
            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
            ส่วนที่ 1: ตัวชี้วัดหลัก (Key Performance Metrics)
        </h3>
        
        <form action="/public/metrics" method="GET" class="flex flex-col md:flex-row gap-4 items-end mb-8 bg-slate-50 p-5 border border-slate-200 rounded-xl">
            <div class="w-full md:w-1/3">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">เลือกปีงบประมาณ</label>
                <select name="year" class="block w-full px-4 py-2.5 border border-slate-205 rounded-xl bg-white text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors sm:text-sm">
                    <?php for($y = date('Y')+543; $y >= date('Y')+539; $y--): ?>
                        <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="w-full md:w-1/3">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">เลือกนักวิจัย (ถ้าต้องการดูรายบุคคล)</label>
                <select name="user_id" class="block w-full px-4 py-2.5 border border-slate-205 rounded-xl bg-white text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors sm:text-sm">
                    <option value="">ภาพรวมทั้งหมด (All Researchers)</option>
                    <?php foreach($researchers as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= $r['id'] == $user_id ? 'selected' : '' ?>><?= e($r['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="px-6 py-2.5 bg-indigo-50 border border-indigo-200 text-indigo-650 hover:bg-indigo-100 rounded-xl text-sm font-semibold transition-colors shadow-sm flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    แสดงผล
                </button>
            </div>
        </form>

        <div class="flex justify-between items-center mb-4">
            <h4 class="text-md font-bold text-slate-805">ผลลัพธ์: <?= $user_id ? 'รายบุคคล' : 'ภาพรวม' ?> (ปีงบประมาณ <?= e($year) ?>)</h4>
            <?php if(hasRole('admin')): ?>
            <form action="/public/metrics/sync" method="POST" class="inline">
                <input type="hidden" name="year" value="<?= e($year) ?>">
                <input type="hidden" name="user_id" value="<?= $user_id ? e($user_id) : 'all' ?>">
                <button type="submit" class="px-4 py-2 border border-slate-200 rounded-xl text-slate-650 bg-white hover:bg-slate-50 transition-colors shadow-sm text-xs font-semibold flex items-center">
                    <svg class="w-3 h-3 mr-1.5 text-slate-550" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    บันทึก Snapshot/Sync ข้อมูล
                </button>
            </form>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 text-center hover:bg-slate-100/50 transition-colors shadow-sm">
                <div class="text-4xl font-extrabold text-slate-805 mb-2"><?= number_format($hIndex) ?></div>
                <div class="text-xs font-bold text-indigo-650 uppercase tracking-wider">H-INDEX (Scopus/WoS)</div>
                <div class="text-xs text-slate-450 font-medium mt-2">ประเมินจากผลงานทั้งหมด</div>
            </div>
            
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 text-center hover:bg-slate-100/50 transition-colors shadow-sm">
                <div class="text-4xl font-extrabold text-indigo-650 mb-2"><?= number_format($citationCount) ?></div>
                <div class="text-xs font-bold text-indigo-650 uppercase tracking-wider">Citation Count รวม</div>
                <div class="text-xs text-slate-450 font-medium mt-2">จำนวนอ้างอิงสะสม</div>
            </div>

            <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 text-center hover:bg-slate-100/50 transition-colors shadow-sm">
                <div class="text-4xl font-extrabold text-amber-600 mb-2"><?= number_format($externalGrants / 1000000, 2) ?> M</div>
                <div class="text-xs font-bold text-amber-600 uppercase tracking-wider">ทุนภายนอกที่ได้รับ (บาท)</div>
                <div class="text-xs text-slate-450 font-medium mt-2">External Grants (ล้านบาท)</div>
            </div>

            <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 text-center hover:bg-slate-100/50 transition-colors shadow-sm">
                <div class="text-4xl font-extrabold text-purple-650 mb-2"><?= $q1q2Pubs ?> <span class="text-sm text-slate-450 font-normal">/ <?= $totalPubs ?></span></div>
                <div class="text-xs font-bold text-purple-600 uppercase tracking-wider">ผลงานตีพิมพ์ Q1/Q2</div>
                <div class="text-xs text-slate-450 font-medium mt-2">รวมทั้งหมด <?= $totalPubs ?> บทความ</div>
            </div>
        </div>
    </div>

    <!-- Section 2: Source Data Table -->
    <div class="card p-6 relative overflow-hidden">
        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center border-b border-slate-100 pb-3">
            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
            ส่วนที่ 2: ข้อมูลผลงานตีพิมพ์ที่ใช้ในการคำนวณ (Source Data)
        </h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs uppercase tracking-wider text-slate-500 bg-slate-50">
                        <th class="px-4 py-3 rounded-tl-lg font-semibold">บทความ (ชื่อย่อ)</th>
                        <th class="px-4 py-3 font-semibold">วารสาร (Indexing)</th>
                        <th class="px-4 py-3 font-semibold text-center">ปี</th>
                        <th class="px-4 py-3 font-semibold text-center">อ้างอิงจากโครงการ</th>
                        <th class="px-4 py-3 font-semibold text-right rounded-tr-lg">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($publicationsList)): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400 italic">ไม่พบข้อมูลผลงานตีพิมพ์ในปีนี้</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($publicationsList as $pub): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-4">
                                <p class="text-sm font-bold text-slate-800 truncate max-w-sm" title="<?= e($pub['title']) ?>"><?= e($pub['title']) ?></p>
                                <p class="text-xs text-slate-450 mt-1 font-medium"><?= e($pub['journal_name']) ?></p>
                            </td>
                            <td class="px-4 py-4">
                                <?php 
                                    $idxClass = 'bg-slate-100 text-slate-500 border-slate-200';
                                    if(str_contains($pub['indexing'], 'Scopus') || str_contains($pub['indexing'], 'WoS')) $idxClass = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                                    if(str_contains($pub['indexing'], 'TCI')) $idxClass = 'bg-amber-50 text-amber-700 border-amber-200';
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border <?= $idxClass ?>">
                                    <?= e($pub['indexing']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center text-sm font-semibold text-slate-600"><?= e($pub['publication_year']) ?></td>
                            <td class="px-4 py-4 text-center text-sm">
                                <span class="font-mono bg-slate-100 border border-slate-200 px-2 py-1 rounded text-xs font-semibold text-slate-650"><?= e($pub['project_code']) ?></span>
                            </td>
                            <td class="px-4 py-4 text-right">
                                <a href="#" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold transition-colors border border-indigo-200 px-3 py-1.5 rounded-xl bg-indigo-50/50 hover:bg-indigo-100 shadow-sm">ดูรายละเอียด</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 3: Summary text -->
    <div class="card p-6 relative overflow-hidden">
        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center border-b border-slate-100 pb-3">
            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            ส่วนที่ 3: ข้อมูลสรุปสำหรับเชิงกลยุทธ์ (EdPEx/BSC)
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">ตัวชี้วัดองค์กร (Output/Outcome Summary)</label>
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-sm text-slate-600 font-mono whitespace-pre-line h-40 overflow-y-auto">
- จำนวนผลงานตีพิมพ์ในวารสารนานาชาติ (<?= $q1q2Pubs ?> บทความ)
- สัดส่วนผลงานตีพิมพ์ Q1/Q2 ต่อผลงานทั้งหมด (<?= $totalPubs > 0 ? round(($q1q2Pubs/$totalPubs)*100, 2) : 0 ?>%)
- มูลค่าทุนวิจัยภายนอกรวม (<?= number_format($externalGrants) ?> บาท)
- จำนวนสิทธิบัตรและอนุสิทธิบัตร (<?= $totalIps ?> รายการ)
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">ข้อสังเกตเบื้องต้น</label>
                <textarea class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white text-sm h-40 resize-none" placeholder="โปรแกรมประมวลผลให้ระบุข้อสังเกตได้ที่นี่..."></textarea>
            </div>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 p-4 z-40 shadow-lg">
        <div class="max-w-7xl mx-auto flex justify-end items-center px-4 gap-3">
            <a href="/public/dashboard" class="px-6 py-2.5 border border-slate-200 text-slate-650 bg-white hover:bg-slate-50 rounded-xl text-sm font-semibold transition-colors">
                กลับ/ยกเลิก
            </a>
            <a href="/public/dashboard" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-bold flex items-center shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                บันทึกผลลัพธ์เพื่อนำไปแสดงบน Dashboard
            </a>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php'; 
?>
