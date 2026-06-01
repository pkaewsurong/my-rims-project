<?php ob_start(); ?>

<div class="mb-8">
    <a href="javascript:history.back()" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-indigo-650 transition-colors mb-4">
        <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        ย้อนกลับ
    </a>
    <div class="flex justify-between items-center bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-wide">
                จัดการงบประมาณและแหล่งทุน
            </h2>
            <p class="mt-1 text-sm text-slate-550 font-medium">ระบบบริหารงานวิจัยและนวัตกรรม (RIMS) | โครงการ: <span class="text-slate-850 font-bold"><?= e($proposal['title']) ?></span></p>
        </div>
        <div class="text-right text-sm">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-650 border border-indigo-150">
                สถานะ: <?= e(ucfirst($proposal['status'] ?? '')) ?>
            </span>
        </div>
    </div>
</div>

<div class="space-y-6 max-w-7xl mx-auto pb-24">
    
    <!-- Section 1: Budget Overview -->
    <div class="card p-6 relative overflow-hidden">
        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center border-b border-slate-100 pb-2">
            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            ภาพรวมงบประมาณโครงการ
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div class="bg-slate-50 border border-slate-200/60 rounded-xl p-6 text-center shadow-sm">
                <p class="text-xs text-slate-400 font-bold mb-1 uppercase tracking-wider">งบประมาณที่อนุมัติตามโครงการ</p>
                <p class="text-2xl font-extrabold text-slate-800"><?= number_format($total_sources, 2) ?> บาท</p>
            </div>
            <div class="bg-slate-50 border border-slate-200/60 rounded-xl p-6 text-center shadow-sm">
                <p class="text-xs text-slate-400 font-bold mb-1 uppercase tracking-wider">งบประมาณที่จัดสรร/ใช้ไปแล้ว</p>
                <p class="text-2xl font-extrabold text-rose-600"><?= number_format($total_allocated, 2) ?> บาท</p>
            </div>
            <div class="bg-slate-50 border border-slate-200/60 rounded-xl p-6 text-center shadow-sm">
                <p class="text-xs text-slate-400 font-bold mb-1 uppercase tracking-wider">งบประมาณคงเหลือ (Available)</p>
                <p class="text-2xl font-extrabold text-emerald-650"><?= number_format($available_balance, 2) ?> บาท</p>
            </div>
        </div>
    </div>

    <!-- Section 2: Funding Sources -->
    <div class="card p-6 relative overflow-hidden">
        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center border-b border-slate-100 pb-2">
            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            รายละเอียดแหล่งทุนที่ได้รับจัดสรร
        </h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-650 font-medium">
                <thead class="text-xs text-slate-500 bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th scope="col" class="py-3 px-4 font-bold">แหล่งทุน / ชื่อโครงการย่อย</th>
                        <th scope="col" class="py-3 px-4 w-32 text-center font-bold">ประเภททุน</th>
                        <th scope="col" class="py-3 px-4 w-48 text-right font-bold">วงเงิน (บาท)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($funding_sources)): ?>
                        <tr>
                            <td colspan="3" class="py-6 text-center text-slate-400 italic">ไม่มีแหล่งทุนที่บันทึกไว้</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($funding_sources as $source): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3 px-4 font-bold text-slate-850"><?= e($source['name']) ?></td>
                                <td class="py-3 px-4 text-center">
                                    <?php if ($source['type'] === 'Internal'): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-indigo-50 text-indigo-650 border border-indigo-150">ภายใน</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">ภายนอก</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-right text-slate-800 font-semibold"><?= number_format($source['amount'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Add Source Form -->
        <form action="/public/proposals/budget" method="POST" class="mt-4 bg-slate-50 p-4 border border-dashed border-slate-250 rounded-xl">
            <input type="hidden" name="action_type" value="source">
            <input type="hidden" name="proposal_id" value="<?= $proposal['id'] ?>">
            <div class="flex items-center space-x-2 text-sm text-indigo-650 mb-3 font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                <span>เพิ่มแหล่งทุนใหม่</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-6">
                    <input type="text" name="name" required placeholder="ชื่อแหล่งทุน เช่น ทุนอุดหนุนวิจัย ทุนจากกระทรวงฯ" class="block w-full px-3 py-2 border border-slate-205 rounded-lg bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                </div>
                <div class="md:col-span-3">
                    <select name="type_display" id="funder-selector" required class="block w-full px-3 py-2 border border-slate-205 rounded-lg bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm appearance-none">
                        <option value="" disabled selected>-- ประเภทแหล่งทุน --</option>
                        <?php foreach($all_funding_sources as $fs): ?>
                            <option value="<?= e($fs['name']) ?>" data-type="<?= e($fs['type'] ?? 'External') ?>"><?= e($fs['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="type" id="funder-type-hidden" value="External">
                </div>
                <div class="md:col-span-3 relative">
                    <input type="text" name="amount" required placeholder="วงเงิน (บาท)" class="amount-input block w-full px-3 py-2 border border-slate-205 rounded-lg bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                </div>
            </div>
            <div class="mt-4 text-right">
                <button type="submit" class="px-5 py-2.5 bg-indigo-650 hover:bg-indigo-750 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                    บันทึกแหล่งทุน
                </button>
            </div>
        </form>
    </div>

    <!-- Section 3: Line Item Budget -->
    <div class="card p-6 relative overflow-hidden">
        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center border-b border-slate-100 pb-2">
            <svg class="w-5 h-5 mr-2 text-indigo-650" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            รายละเอียดรายการค่าใช้จ่าย (Line Item Budget)
        </h3>

        <div class="overflow-x-auto mt-4">
            <table class="w-full text-sm text-left text-slate-655 font-medium">
                <thead class="text-xs text-slate-500 bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th scope="col" class="py-3 px-2 w-10 text-center font-bold">#</th>
                        <th scope="col" class="py-3 px-4 w-48 font-bold">หมวดค่าใช้จ่าย</th>
                        <th scope="col" class="py-3 px-4 font-bold">รายละเอียดรายการ</th>
                        <th scope="col" class="py-3 px-4 w-40 text-right font-bold">จำนวนเงิน (บาท)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($budget_items)): ?>
                        <tr>
                            <td colspan="4" class="py-6 text-center text-slate-400 italic">ไม่มีรายการค่าใช้จ่าย</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($budget_items as $index => $item): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-3 px-2 text-center text-slate-400 font-semibold"><?= $index + 1 ?></td>
                                <td class="py-3 px-4 text-slate-800 font-bold">
                                    <?= e($item['category']) ?>
                                </td>
                                <td class="py-3 px-4"><span class="text-slate-600"><?= e($item['description']) ?></span></td>
                                <td class="py-3 px-4 text-right text-slate-800 font-semibold"><?= number_format($item['amount'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <tr class="bg-indigo-50/40 font-bold border-t border-indigo-150">
                        <td colspan="3" class="py-4 px-4 text-right text-indigo-650">รวมงบประมาณที่จัดสรร/ใช้ไปแล้ว:</td>
                        <td class="py-4 px-4 text-right text-indigo-650"><?= number_format($total_allocated, 2) ?></td>
                    </tr>
                    <tr class="bg-<?= $available_balance < 0 ? 'rose' : 'emerald' ?>-50/40 font-bold border-t border-<?= $available_balance < 0 ? 'rose' : 'emerald' ?>-200">
                        <td colspan="3" class="py-4 px-4 text-right text-<?= $available_balance < 0 ? 'rose-600' : 'emerald-650' ?>">งบประมาณที่ยังไม่จัดสรร/คงเหลือ (<?= number_format($total_sources, 2) ?> - <?= number_format($total_allocated, 2) ?>):</td>
                        <td class="py-4 px-4 text-right text-<?= $available_balance < 0 ? 'rose-600' : 'emerald-650' ?>"><?= number_format($available_balance, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Add Line Item Form -->
        <form action="/public/proposals/budget" method="POST" class="mt-6 bg-slate-50 p-4 border border-dashed border-slate-250 rounded-xl">
            <input type="hidden" name="action_type" value="line_item">
            <input type="hidden" name="proposal_id" value="<?= $proposal['id'] ?>">
            <div class="flex items-center space-x-2 text-sm text-indigo-650 mb-3 font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                <span>เพิ่มรายการค่าใช้จ่าย</span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">ปีงบประมาณ</label>
                    <select name="year" required class="block w-full px-3 py-2 border border-slate-205 rounded-lg bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                        <?php $currentYear = date('Y') + 543; ?>
                        <?php for($y = $currentYear; $y <= $currentYear + 3; $y++): ?>
                            <option value="<?= $y ?>">ปี <?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">หมวดค่าใช้จ่าย</label>
                    <select name="category" required class="block w-full px-3 py-2 border border-slate-205 rounded-lg bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                        <option value="ค่าตอบแทน (Personnel)">ค่าตอบแทน (Personnel)</option>
                        <option value="ค่าวัสดุ (Materials)">ค่าวัสดุ (Materials)</option>
                        <option value="ค่าใช้สอย (Expenses)">ค่าใช้สอย (Expenses)</option>
                        <option value="ครุภัณฑ์ (Equipment)">ครุภัณฑ์ (Equipment)</option>
                        <option value="อื่นๆ (Others)">อื่นๆ (Others)</option>
                    </select>
                </div>
                <div class="md:col-span-4">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">รายละเอียดรายการ</label>
                    <input type="text" name="description" required placeholder="เช่น ค่าจ้างผู้ช่วยวิจัย, ซอฟต์แวร์ลิขสิทธิ์" class="block w-full px-3 py-2 border border-slate-205 rounded-lg bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">จำนวนเงิน (บาท)</label>
                    <input type="text" name="amount" required placeholder="0.00" class="amount-input block w-full px-3 py-2 border border-slate-205 rounded-lg bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                </div>
            </div>
            
            <div class="mt-4 text-right">
                <button type="submit" class="px-5 py-2.5 bg-indigo-650 hover:bg-indigo-755 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                    + เพิ่มรายการค่าใช้จ่าย
                </button>
            </div>
            <p class="mt-2 text-xs text-slate-400 text-right">
                *ระบบจะนำไปคำนวณหักลบกับงบประมาณรวมทั้งหมด
            </p>
        </form>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInputs = document.querySelectorAll('.amount-input');
    
    amountInputs.forEach(input => {
        if(input.value) {
            let val = input.value.replace(/,/g, '');
            if(!isNaN(val) && val !== '') {
                input.value = Number(val).toLocaleString('en-US');
            }
        }
        
        input.addEventListener('input', function(e) {
            let val = this.value.replace(/[^0-9.]/g, '');
            const parts = val.split('.');
            if (parts.length > 2) {
                parts.pop();
                val = parts.join('.');
            }
            
            if (val) {
                if(parts.length > 1) {
                    this.value = Number(parts[0]).toLocaleString('en-US') + '.' + parts[1];
                } else {
                    this.value = Number(val).toLocaleString('en-US');
                }
            } else {
                this.value = '';
            }
        });
    });

    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            this.querySelectorAll('.amount-input').forEach(input => {
                input.value = input.value.replace(/,/g, '');
            });
        });
    });

    const funderSelector = document.getElementById('funder-selector');
    const funderTypeHidden = document.getElementById('funder-type-hidden');
    if (funderSelector && funderTypeHidden) {
        funderSelector.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const type = selectedOption.getAttribute('data-type');
            if (type) {
                funderTypeHidden.value = type;
            }
        });
    }
});
</script>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php'; 
?>
