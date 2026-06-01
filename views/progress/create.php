<?php ob_start(); ?>

<div class="mb-8">
    <a href="/public/projects/<?= e($project['id']) ?>" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-indigo-650 transition-colors mb-4">
        <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        กลับไปหน้ารายละเอียดโครงการ
    </a>
    <div class="flex justify-between items-center bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-wide">
                อัปเดตความก้าวหน้าและ Milestone
            </h2>
            <p class="mt-1 text-sm text-slate-500">ระบบบริหารงานวิจัยและนวัตกรรม (RIMS) | โครงการ: <span class="text-slate-850 font-bold"><?= e($project['title']) ?></span></p>
        </div>
        <div class="text-right text-xs text-slate-400 font-semibold space-y-1">
            <p>รหัสอ้างอิง: RIMS-<?= date('Y-m') ?>-<?= str_pad($project['id'], 4, '0', STR_PAD_LEFT) ?></p>
            <p>ระยะเวลาดำเนินโครงการ: <?= date('d M Y', strtotime($project['start_date'])) ?> - <?= date('d M Y', strtotime($project['end_date'])) ?></p>
        </div>
    </div>
</div>

<div class="space-y-6">
    <!-- Overall Progress Section -->
    <div class="card p-6 relative overflow-hidden group">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 divide-y md:divide-y-0 md:divide-x divide-slate-100">
            <!-- Actual Progress -->
            <div class="text-center px-4">
                <p class="text-4xl font-extrabold text-emerald-600 mb-2"><?= $current_progress ?>%</p>
                <p class="text-sm font-bold text-slate-500">ความก้าวหน้าโครงการดำเนินการจริง (Actual Progress)</p>
            </div>
            
            <!-- Planned Progress -->
            <div class="text-center px-4">
                <p class="text-4xl font-extrabold text-amber-600 mb-2"><?= $planned_progress ?>%</p>
                <p class="text-sm font-bold text-slate-500">ความก้าวหน้าตามแผนงาน (Planned Progress)</p>
            </div>
            
            <!-- Difference -->
            <div class="text-center px-4 flex flex-col justify-center">
                <?php 
                    $diff = $current_progress - $planned_progress;
                    $diffColor = $diff >= 0 ? 'text-emerald-605' : 'text-rose-600';
                    $diffSign = $diff >= 0 ? '+' : '';
                ?>
                <p class="text-2xl font-bold <?= $diffColor ?> mb-2"><?= $diffSign ?><?= $diff ?>%</p>
                <p class="text-sm font-semibold text-slate-500">ผลต่าง (Variance)</p>
                
                <div class="mt-4 flex items-center justify-between text-xs text-slate-500">
                    <span>สถานะความคืบหน้า:</span>
                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 font-bold whitespace-nowrap overflow-hidden text-ellipsis max-w-[100px]" title="<?= $diff >= 0 ? "เป็นไปตามแผน" : "ล่าช้ากว่าแผน" ?>"><?= $diff >= 0 ? "เป็นไปตามแผน" : "ล่าช้ากว่าแผน" ?></span>
                </div>
            </div>
        </div>
        
        <div class="mt-6">
             <div class="w-full bg-slate-100 rounded-full h-2 mb-1">
                <div class="bg-emerald-500 h-2 rounded-full" style="width: <?= $current_progress ?>%"></div>
             </div>
             <div class="flex justify-between text-xs text-slate-400 font-medium">
                  <span>0%</span>
                  <span>(Actual: <?= $current_progress ?>%)</span>
                  <span>100%</span>
             </div>
        </div>
    </div>

    <!-- Timeline & Milestone Tracker -->
    <div class="card p-6 relative">
        <h3 class="text-lg font-bold text-slate-800 flex items-center mb-6">
            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            ติดตามความก้าวหน้าย่อย (Milestone Tracker)
        </h3>

        <div class="relative border-l border-slate-200 ml-3 space-y-8 pb-4">
            <?php foreach ($milestones as $index => $m): ?>
                <div class="relative pl-8">
                    <!-- Timeline Dot -->
                    <?php if ($m['status'] == 'completed'): ?>
                        <div class="absolute -left-3 top-0 w-6 h-6 rounded-full bg-emerald-500 border-4 border-white flex items-center justify-center shadow-sm">
                             <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                    <?php elseif ($m['status'] == 'in_progress'): ?>
                        <div class="absolute -left-3 top-0 w-6 h-6 rounded-full bg-amber-500 border-4 border-white flex items-center justify-center shadow-sm">
                             <div class="w-2 h-2 rounded-full bg-white animate-pulse"></div>
                        </div>
                    <?php else: ?>
                        <div class="absolute -left-3 top-0 w-6 h-6 rounded-full bg-slate-200 border-4 border-white shadow-sm"></div>
                    <?php endif; ?>

                    <!-- Content -->
                    <div class="mb-1 text-xs text-slate-400 font-bold">ชื่อระยะ: <?= e($m['milestone_name']) ?></div>
                    <div class="bg-slate-50 border border-slate-150 rounded-xl p-4 <?php echo ($m['status'] == 'in_progress') ? 'border-l-4 border-l-amber-500' : '' ?>">
                        <p class="text-slate-700 text-sm <?php echo ($m['status'] == 'completed') ? 'line-through text-slate-400 font-medium' : 'font-semibold' ?>">
                            <?= e($m['description']) ?>
                        </p>
                        
                        <!-- Mini status badge -->
                        <div class="mt-3">
                            <?php if ($m['status'] == 'completed'): ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-150">
                                    เสร็จสิ้น (Completed)
                                </span>
                            <?php elseif ($m['status'] == 'in_progress'): ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-50 text-amber-600 border border-amber-150">
                                    กำลังดำเนินการ (In Progress)
                                </span>
                                <p class="text-xs text-amber-600 mt-2 font-medium">* ต้องรายงานผลในงวดปัจจุบันตามกำหนดเวลา แจ้งปัญหาถ้ามีล่าช้า</p>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                    ยังไม่เริ่ม (Pending)
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Budget Utilization -->
    <div class="card p-6 relative">
        <h3 class="text-lg font-bold text-slate-800 flex items-center mb-4">
            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            สถานะการใช้จ่ายงบประมาณ (Budget Utilization)
        </h3>
        
        <?php
            $budget_total = $project['budget_total'] ?? 0;
            $budget_used = $project['budget_used'] ?? 0;
            $budget_percent = $budget_total > 0 ? round(($budget_used / $budget_total) * 100) : 0;
            $remaining_percent = 100 - $budget_percent;
        ?>

        <div class="mb-2 w-full bg-slate-100 rounded-full h-3">
             <div class="bg-indigo-650 h-3 rounded-full" style="width: <?= $budget_percent ?>%"></div>
        </div>
        <div class="flex justify-between text-sm font-semibold">
             <span class="text-slate-500">ใช้จ่ายแล้ว <?= $budget_percent ?>% (<?= number_format($budget_used) ?> บาท)</span>
             <span class="text-rose-600">งบประมาณคงเหลือ <?= $remaining_percent ?>%</span>
        </div>
    </div>

    <!-- Report Form Section -->
    <div class="card p-6">
        <h3 class="text-lg font-bold text-slate-800 flex items-center mb-6">
            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            รายงานผลการดำเนินงานปัจจุบัน (Current Report)
        </h3>

        <form action="/public/progress-reports/store" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="project_id" value="<?= e($project['id']) ?>">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">งวด/รอบการรายงานปัจจุบัน (Report Period)</label>
                    <input type="text" name="report_period" required placeholder="เช่น งวดที่ 2 หรือ เดือนที่ 3" class="block w-full px-4 py-2 bg-slate-50 border border-slate-205 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">ความก้าวหน้าโครงการดำเนินการจริง (%)</label>
                    <input type="number" name="percentage_complete" min="0" max="100" required value="<?= $current_progress ?>" class="block w-full px-4 py-2 bg-slate-50 border border-slate-205 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white text-sm font-bold">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">ความก้าวหน้าตามแผนงาน (%)</label>
                    <input type="number" name="planned_progress_percentage" min="0" max="100" required value="<?= $planned_progress ?>" class="block w-full px-4 py-2 bg-slate-50 border border-slate-205 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white text-sm font-bold">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">สรุปผลการดำเนินงานตั้งแต่งวดที่แล้ว - ปัจจุบัน <span class="text-rose-500">*</span></label>
                <textarea name="summary_text" rows="4" required placeholder="อธิบายกิจกรรมที่ดำเนินการ ผลที่ได้รับ และหลักฐานเชิงประจักษ์..." class="block w-full px-4 py-3 bg-slate-50 border border-slate-205 rounded-xl text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white text-sm resize-y font-semibold"></textarea>
            </div>

            <div class="bg-rose-50/50 border border-rose-200 rounded-xl p-4">
                <div class="flex justify-between items-start mb-1.5">
                    <label class="block text-sm font-bold text-rose-600 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        ปัญหา/อุปสรรคที่พบ และแนวทางแก้ไข <span class="text-slate-400 font-normal text-xs ml-2">(ถ้าไม่มีให้ข้าม)</span>
                    </label>
                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-bold text-slate-700">ระดับความเสี่ยง:</label>
                        <select name="risk_level" class="bg-white border border-slate-200 rounded-lg text-sm text-slate-800 px-2 py-1 focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="Low">Low - ต่ำ</option>
                            <option value="Medium">Medium - ปานกลาง</option>
                            <option value="High">High - สูง</option>
                        </select>
                    </div>
                </div>
                <textarea name="problems_obstacles" rows="3" placeholder="ระบุปัญหาที่อาจทำให้โครงการล่าช้า และการแก้ปัญหาเฉพาะหน้า..." class="block w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-800 focus:ring-2 focus:ring-rose-500 focus:border-transparent text-sm resize-y placeholder-slate-400 font-medium"></textarea>
            </div>

            <div class="bg-amber-50/50 border border-amber-200 rounded-xl p-4 relative overflow-hidden">
                <label class="block text-sm font-bold text-amber-600 mb-1.5 flex items-center relative z-10">
                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    แผนงานถัดไป/ขั้นถัดไป (Next Milestone) <span class="text-rose-500 ml-1">*</span>
                </label>
                <textarea name="next_milestone_plan" rows="3" required placeholder="ระบุแผนงาน/กิจกรรมที่จะดำเนินการต่อในงวดถัดไป เพื่อให้บรรลุ Milestone ถัดไป..." class="block w-full px-4 py-3 bg-white border border-amber-200 rounded-xl text-slate-850 focus:ring-2 focus:ring-amber-500 focus:border-transparent text-sm resize-y placeholder-slate-400 font-semibold relative z-10"></textarea>
            </div>

            <div class="bg-indigo-50/30 border border-indigo-150 rounded-xl p-5 relative overflow-hidden">
                <label class="block text-sm font-bold text-indigo-650 mb-4 flex items-center relative z-10">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    อัปเดตสถานะความก้าวหน้าย่อย (Update Milestones)
                </label>
                <div class="space-y-4 relative z-10">
                    <?php foreach ($milestones as $m): ?>
                        <div class="flex flex-col md:flex-row md:items-center justify-between bg-white p-4 rounded-xl border border-slate-200 hover:border-indigo-300 transition-colors">
                            <div class="mb-3 md:mb-0 pr-4">
                                <span class="text-sm text-slate-805 font-bold block mb-1"><?= e($m['milestone_name']) ?></span>
                                <span class="text-xs text-slate-500 font-semibold line-clamp-2"><?= e(empty($m['description']) ? 'ไม่มีรายละเอียด' : $m['description']) ?></span>
                            </div>
                            <div class="shrink-0 md:w-48">
                                <select name="milestones[<?= $m['id'] ?>]" class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                                    <option value="pending" <?= $m['status'] == 'pending' ? 'selected' : '' ?>>ยังไม่เริ่ม (Pending)</option>
                                    <option value="in_progress" <?= $m['status'] == 'in_progress' ? 'selected' : '' ?>>กำลังดำเนินการ (In Progress)</option>
                                    <option value="completed" <?= $m['status'] == 'completed' ? 'selected' : '' ?>>เสร็จสิ้น (Completed)</option>
                                </select>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">แนบเอกสารหลักฐาน (เช่น PDF รายงานฉบับเต็ม, บัญชีรายรับรายจ่าย) - สามารถเลือกได้หลายไฟล์</label>
                <input type="file" name="attachment[]" multiple class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-slate-100 file:text-slate-650 hover:file:bg-slate-200 border border-slate-200 rounded-xl p-1 bg-white cursor-pointer">
            </div>

            <div class="pt-4 flex justify-between items-center border-t border-slate-100 mt-6 pt-6">
                <a href="/public/projects/<?= e($project['id']) ?>" class="px-6 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 bg-white hover:bg-slate-50 transition-colors shadow-sm">ยกเลิกกลับไปหน้าโครงการ</a>
                <button type="submit" class="btn-primary px-8 py-3 rounded-xl text-sm font-bold tracking-wide flex items-center shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    บันทึกรายงานความก้าวหน้า
                </button>
            </div>
        </form>
    </div>
</div>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php'; 
?>
