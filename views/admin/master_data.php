<?php ob_start(); ?>

<div class="mb-8">
    <div class="flex justify-between items-center bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-wide flex items-center">
                <svg class="w-6 h-6 mr-3 text-indigo-650" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                จัดการข้อมูลหลัก (Master Data)
            </h2>
            <p class="mt-1 text-sm text-slate-500 font-medium">ระบบบริหารงานวิจัยและนวัตกรรม (RIMS) | ตั้งค่าแหล่งทุน ฐานข้อมูลวารสาร และเกณฑ์ประเมินคะแนน</p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto pb-24">
    
    <?php if(isset($_SESSION['flash_success'])): ?>
    <div class="bg-emerald-50 border border-emerald-255 text-emerald-700 p-4 rounded-xl mb-6 font-semibold text-sm">
        <?= e($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
    </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['flash_error'])): ?>
    <div class="bg-rose-50 border border-rose-255 text-rose-705 p-4 rounded-xl mb-6 font-semibold text-sm">
        <?= e($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
    </div>
    <?php endif; ?>

    <!-- Tabs Navigation -->
    <div class="flex flex-wrap gap-2 mb-6 border-b border-slate-200 pb-4">
        <a href="?tab=funders" class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all border <?= $tab==='funders' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-slate-50 text-slate-500 border-slate-200 hover:text-slate-800 hover:bg-slate-100/70' ?>">
            1. แหล่งทุนวิจัย (Funders)
        </a>
        <a href="?tab=journals" class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all border <?= $tab==='journals' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-slate-50 text-slate-500 border-slate-200 hover:text-slate-800 hover:bg-slate-100/70' ?>">
            2. ฐานข้อมูลวารสาร (Journals)
        </a>
        <a href="?tab=tiers" class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all border <?= $tab==='tiers' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-slate-50 text-slate-500 border-slate-200 hover:text-slate-800 hover:bg-slate-100/70' ?>">
            3. เกณฑ์คะแนน/Tiers (Metrics)
        </a>
    </div>

    <div class="card p-6 relative overflow-hidden">
        
        <?php if($tab === 'funders'): ?>
        <!-- Tab 1: Funders -->
        <h3 class="text-lg font-bold text-slate-800 mb-6 border-b border-slate-100 pb-3 flex justify-between items-center">
            <span>รายการแหล่งทุนวิจัยในระบบ</span>
            <button onclick="document.getElementById('modal-funder').classList.remove('hidden')" class="px-4 py-2 bg-indigo-50 border border-indigo-200 text-indigo-650 hover:bg-indigo-100 rounded-xl text-xs font-bold transition-all shadow-sm">
                + เพิ่มแหล่งทุน
            </button>
        </h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs uppercase tracking-wider text-slate-500 bg-slate-50">
                        <th class="px-4 py-3 rounded-tl-lg font-semibold">ชื่อแหล่งทุน</th>
                        <th class="px-4 py-3 font-semibold">ประเภท</th>
                        <th class="px-4 py-3 font-semibold">สถานะ</th>
                        <th class="px-4 py-3 font-semibold text-right rounded-tr-lg">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach($funders as $f): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors text-sm font-medium text-slate-650">
                        <td class="px-4 py-4 text-slate-800 font-bold"><?= e($f['name']) ?></td>
                        <td class="px-4 py-4">
                            <?php if($f['type'] === 'Internal'): ?>
                                <span class="text-indigo-650 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200 text-xs font-bold">ทุนภายใน</span>
                            <?php else: ?>
                                <span class="text-purple-650 bg-purple-50 px-2 py-0.5 rounded border border-purple-200 text-xs font-bold">ทุนภายนอก</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-4">
                            <?php if($f['status'] === 'Active'): ?>
                                <span class="text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200 text-xs font-bold flex items-center gap-1.5 w-max"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> ใช้งาน</span>
                            <?php else: ?>
                                <span class="text-slate-500 bg-slate-100 px-2.5 py-0.5 rounded-full border border-slate-200 text-xs font-semibold flex items-center gap-1.5 w-max"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> ปิดใช้งาน</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <a href="#" class="text-indigo-600 hover:text-indigo-850 font-bold px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition-all shadow-sm">แก้ไข</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Add Funder Modal -->
        <div id="modal-funder" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
            <div class="bg-white border border-slate-200 rounded-2xl p-6 w-full max-w-md shadow-2xl relative">
                <button onclick="document.getElementById('modal-funder').classList.add('hidden')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-650 font-bold">✕</button>
                <h4 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">เพิ่มแหล่งทุนใหม่</h4>
                <form action="/public/admin/master-data" method="POST" class="space-y-4">
                    <input type="hidden" name="type" value="funder">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">ชื่อแหล่งทุน</label>
                        <input type="text" name="name" required class="w-full px-4 py-2 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">ประเภท</label>
                        <select name="funder_type" class="w-full px-4 py-2.5 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                            <option value="External">ทุนภายนอก</option>
                            <option value="Internal">ทุนภายใน</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">สถานะ</label>
                        <select name="status" class="w-full px-4 py-2.5 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                            <option value="Active">ใช้งาน</option>
                            <option value="Inactive">ปิดใช้งาน</option>
                        </select>
                    </div>
                    <div class="pt-4 flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('modal-funder').classList.add('hidden')" class="px-5 py-2 border border-slate-200 text-slate-650 bg-white hover:bg-slate-50 rounded-xl text-sm font-semibold">ยกเลิก</button>
                        <button type="submit" class="btn-primary px-5 py-2 rounded-xl text-sm font-bold shadow-sm">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>

        <?php elseif($tab === 'journals'): ?>
        <!-- Tab 2: Journals -->
        <h3 class="text-lg font-bold text-slate-800 mb-6 border-b border-slate-100 pb-3 flex justify-between items-center">
            <span>ฐานข้อมูลวารสาร/สำนักพิมพ์</span>
            <button onclick="document.getElementById('modal-journal').classList.remove('hidden')" class="px-4 py-2 bg-indigo-50 border border-indigo-200 text-indigo-650 hover:bg-indigo-100 rounded-xl text-xs font-bold transition-all shadow-sm">
                + เพิ่มวารสาร
            </button>
        </h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs uppercase tracking-wider text-slate-500 bg-slate-50">
                        <th class="px-4 py-3 rounded-tl-lg font-semibold">ชื่อวารสาร/Proceedings</th>
                        <th class="px-4 py-3 font-semibold">ISSN</th>
                        <th class="px-4 py-3 font-semibold">ฐานข้อมูล</th>
                        <th class="px-4 py-3 font-semibold text-right rounded-tr-lg">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach($journals as $j): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors text-sm font-medium text-slate-650">
                        <td class="px-4 py-4 text-slate-800 font-bold">
                            <?= e($j['name']) ?>
                            <?php if($j['quartile'] !== 'N/A' && $j['quartile']): ?>
                                <span class="bg-slate-200 text-slate-650 px-2 py-0.5 rounded-full text-[10px] ml-2 font-bold"><?= e($j['quartile']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-4 text-slate-450 font-mono text-xs font-semibold"><?= e($j['issn']) ?></td>
                        <td class="px-4 py-4">
                            <?php 
                                $bg = 'bg-slate-100 text-slate-500 border-slate-200';
                                if($j['database_index']==='Scopus' || $j['database_index']==='WoS') $bg = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                                if($j['database_index']==='TCI') $bg = 'bg-amber-50 text-amber-700 border-amber-200';
                            ?>
                            <span class="px-2.5 py-0.5 rounded-full border text-xs font-bold <?= $bg ?>"><?= e($j['database_index']) ?></span>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <a href="#" class="text-indigo-600 hover:text-indigo-855 font-bold px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition-all shadow-sm">แก้ไข</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Add Journal Modal -->
        <div id="modal-journal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
            <div class="bg-white border border-slate-200 rounded-2xl p-6 w-full max-w-md shadow-2xl relative">
                <button onclick="document.getElementById('modal-journal').classList.add('hidden')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-655 font-bold">✕</button>
                <h4 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">เพิ่มวารสารเข้าระบบ</h4>
                <form action="/public/admin/master-data" method="POST" class="space-y-4">
                    <input type="hidden" name="type" value="journal">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">ชื่อวารสาร</label>
                        <input type="text" name="name" required class="w-full px-4 py-2 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">ISSN/ISBN</label>
                        <input type="text" name="issn" class="w-full px-4 py-2 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">ฐานข้อมูลหลัก</label>
                            <select name="database_index" class="w-full px-4 py-2.5 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                                <option value="Scopus">Scopus</option>
                                <option value="WoS">Web of Science</option>
                                <option value="TCI">TCI</option>
                                <option value="Scopus Proceeding">Scopus Proceeding</option>
                                <option value="Other">อื่นๆ / ภูมิภาค</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Quartile (ถ้ามี)</label>
                            <input type="text" name="quartile" placeholder="e.g. Q1, TCI Q1" class="w-full px-4 py-2 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                        </div>
                    </div>
                    <div class="pt-4 flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('modal-journal').classList.add('hidden')" class="px-5 py-2 border border-slate-200 text-slate-655 bg-white hover:bg-slate-50 rounded-xl text-sm font-semibold">ยกเลิก</button>
                        <button type="submit" class="btn-primary px-5 py-2 rounded-xl text-sm font-bold shadow-sm">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>

        <?php elseif($tab === 'tiers'): ?>
        <!-- Tab 3: Metric Tiers -->
        <h3 class="text-lg font-bold text-slate-800 mb-6 border-b border-slate-100 pb-3 flex justify-between items-center">
            <span>เกณฑ์การให้คะแนนผลงาน (Metric Points Mapping)</span>
            <button onclick="document.getElementById('modal-tier').classList.remove('hidden')" class="px-4 py-2 bg-indigo-50 border border-indigo-200 text-indigo-650 hover:bg-indigo-100 rounded-xl text-xs font-bold transition-all shadow-sm">
                + เพิ่มเกณฑ์คะแนน
            </button>
        </h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-xs uppercase tracking-wider text-slate-500 bg-slate-50">
                        <th class="px-4 py-3 rounded-tl-lg font-semibold">หมวดหมู่ผลงาน</th>
                        <th class="px-4 py-3 font-semibold">ระดับ (Tier/Level)</th>
                        <th class="px-4 py-3 font-semibold text-center">คะแนนที่ได้ (Points)</th>
                        <th class="px-4 py-3 font-semibold text-right rounded-tr-lg">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php 
                    $currentCat = '';
                    foreach($tiers as $t): 
                        if ($currentCat !== $t['category']) {
                            $currentCat = $t['category'];
                            echo "<tr class='bg-slate-50/50'><td colspan='4' class='px-4 py-2.5 text-xs font-bold text-indigo-700 border-y border-slate-100'>หมวด: {$currentCat}</td></tr>";
                        }
                    ?>
                    <tr class="hover:bg-slate-50/50 transition-colors text-sm font-medium text-slate-650">
                        <td class="px-4 py-4 text-slate-500 font-semibold"><?= e($t['category']) ?></td>
                        <td class="px-4 py-4">
                            <p class="text-slate-800 font-bold"><?= e($t['level_name']) ?></p>
                            <?php if($t['description']): ?>
                                <p class="text-xs text-slate-450 mt-1 font-medium"><?= e($t['description']) ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-block bg-indigo-50/60 text-indigo-700 font-mono px-3 py-1 rounded-lg border border-indigo-100 font-bold"><?= number_format($t['points'], 2) ?></span>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <a href="#" class="text-indigo-600 hover:text-indigo-855 font-bold px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition-all shadow-sm">แก้ไข</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Add Tier Modal -->
        <div id="modal-tier" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
            <div class="bg-white border border-slate-200 rounded-2xl p-6 w-full max-w-md shadow-2xl relative">
                <button onclick="document.getElementById('modal-tier').classList.add('hidden')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-655 font-bold">✕</button>
                <h4 class="text-lg font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">เพิ่มเกณฑ์ให้คะแนน</h4>
                <form action="/public/admin/master-data" method="POST" class="space-y-4">
                    <input type="hidden" name="type" value="tier">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">หมวดหมู่ผลงาน</label>
                            <select name="category" class="w-full px-4 py-2.5 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                                <option value="Journal">งานวิจัยตีพิมพ์ (Journal)</option>
                                <option value="IP">ทรัพย์สินทางปัญญา (IP)</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">ชื่อระดับ/คลาส (Level Name)</label>
                            <input type="text" name="level_name" required placeholder="e.g. Scopus Q3" class="w-full px-4 py-2 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">รายละเอียด/เงื่อนไขอ้างอิง</label>
                            <input type="text" name="description" class="w-full px-4 py-2 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">คะแนนที่ได้รับ (Points)</label>
                            <input type="number" step="0.1" name="points" required placeholder="1.0" class="w-full px-4 py-2 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors text-center font-mono font-bold text-lg">
                        </div>
                    </div>
                    <div class="pt-4 flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('modal-tier').classList.add('hidden')" class="px-5 py-2 border border-slate-200 text-slate-655 bg-white hover:bg-slate-50 rounded-xl text-sm font-semibold">ยกเลิก</button>
                        <button type="submit" class="btn-primary px-5 py-2 rounded-xl text-sm font-bold shadow-sm">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>

        <?php endif; ?>

    </div>
</div>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php'; 
?>
