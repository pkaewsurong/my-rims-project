<?php 
ob_start();
$title = 'ส่งรายงานฉบับสมบูรณ์';
?>

<div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    
    <a href="/public/projects/<?= htmlspecialchars((string)($project['id'])) ?>" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-indigo-650 transition-colors mb-4">
        <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        กลับไปหน้ารายละเอียดโครงการ
    </a>

    <!-- HEADER -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 bg-white rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                <span class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </span>
                จัดเก็บ/ส่งรายงานฉบับสมบูรณ์และผลผลิต
            </h1>
            <p class="text-slate-500 mt-2 text-sm">ระบบบริหารงานวิจัยและนวัตกรรม (RIMS) | โครงการ: <?= htmlspecialchars((string)($project['code'])) ?></p>
        </div>
        <div class="text-left md:text-right">
            <p class="text-slate-800 font-bold max-w-md truncate"><?= htmlspecialchars((string)($project['title'])) ?></p>
            <p class="text-sm text-slate-500 mt-1">สถานะโครงการ: <span class="px-2 py-0.5 rounded text-xs font-bold bg-indigo-50 border border-indigo-150 text-indigo-650">รอดำเนินการ (Pending)</span></p>
        </div>
    </div>

    <form action="/reports/final/store" method="POST" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="project_id" value="<?= htmlspecialchars((string)($project['id'])) ?>">
        
        <!-- Section 1: สรุปผลการดำเนินงานและงบประมาณ -->
        <div class="card p-6">
            <h2 class="text-lg font-bold text-slate-800 mb-5 pb-3 border-b border-slate-100 flex items-center gap-2">
                1. สรุปผลการดำเนินงานและงบประมาณ
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">วันที่ส่งรายงานฉบับสมบูรณ์ <span class="text-rose-500">*</span></label>
                    <input type="date" name="submission_date" value="<?= htmlspecialchars((string)($existingReport['submission_date'] ?? date('Y-m-d'))); ?>" class="w-full bg-slate-50 border border-slate-205 rounded-xl text-slate-800 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">สถานะการใช้งบประมาณ</label>
                    <input type="text" value="ใช้ไป <?= $budget_percentage ?>% (คงเหลือ <?= number_format($budget_remaining) ?> บาท)" class="w-full bg-slate-100 border border-slate-200 rounded-xl text-slate-500 px-4 py-2.5 cursor-not-allowed font-medium" readonly>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">ระยะเวลาที่ใช้จริง (เดือน)</label>
                    <?php
                    $start = new DateTime($project['start_date']);
                    $now = new DateTime();
                    $diff = $start->diff($now);
                    $months = ($diff->y * 12) + $diff->m;
                    ?>
                    <input type="text" value="<?= $months ?> เดือน" class="w-full bg-slate-100 border border-slate-200 rounded-xl text-slate-500 px-4 py-2.5 cursor-not-allowed font-medium" readonly>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">สรุปผลสำเร็จเทียบกับวัตถุประสงค์ (Executive Summary)</label>
                <textarea name="executive_summary" rows="4" class="w-full bg-slate-50 border border-slate-205 rounded-xl text-slate-800 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors placeholder-slate-400 font-semibold" placeholder="อธิบายว่าบรรลุวัตถุประสงค์ตามที่ตั้งไว้ในข้อเสนอโครงการหรือไม่ พร้อมให้ข้อเสนอแนะ"><?= htmlspecialchars((string)($existingReport['executive_summary'] ?? '')) ?></textarea>
            </div>
        </div>
        
        <!-- Section 2: การจัดการไฟล์รายงานและผลผลิต -->
        <div class="card p-6">
            <h2 class="text-lg font-bold text-slate-800 mb-5 pb-3 border-b border-slate-100 flex items-center gap-2">
                2. การจัดการไฟล์รายงานและผลผลิต (Outputs)
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">อัปโหลดเอกสารรายงานฉบับสมบูรณ์ (PDF) <span class="text-rose-500">*</span></label>
                    <div class="border-2 border-dashed border-slate-200 hover:border-indigo-500 rounded-xl p-6 text-center bg-slate-50/50 transition-colors cursor-pointer" onclick="document.getElementById('file_report_pdf').click()">
                        <input type="file" name="file_report_pdf" id="file_report_pdf" accept=".pdf" class="hidden" <?= !$existingReport ? 'required' : '' ?> onchange="updateFileName(this, 'file_report_pdf_name')">
                        <div class="mx-auto w-12 h-12 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center mb-3 border border-indigo-100 shadow-sm">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <span class="text-sm text-slate-500 font-bold" id="file_report_pdf_name">ลากไฟล์รายงานมาวางที่นี่ หรือคลิกเพื่อเลือกไฟล์</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">อัปโหลด/แนบผลงานตีพิมพ์/สิทธิบัตร (ถ้ามี)</label>
                    <div class="border border-slate-200 rounded-xl p-6 text-center bg-slate-50 flex flex-col justify-center min-h-[148px]">
                        <p class="text-sm text-slate-500 font-semibold mb-2">โปรดไปที่เมนูผลผลิต/คลังข้อมูลเฉพาะ</p>
                        <p class="text-xs text-indigo-600 font-bold">* ต้องทำการลงทะเบียนผลงานแยกต่างหากเพื่อคำนวณ Metrics (H-Index)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 3: การนำผลงานไปใช้ประโยชน์ -->
        <div class="card p-6">
            <h2 class="text-lg font-bold text-slate-800 mb-5 pb-3 border-b border-slate-100 flex items-center gap-2">
                3. การนำผลงานไปใช้ประโยชน์ (Utilization & Impact)
            </h2>
            
            <div class="space-y-4">
                <div class="bg-slate-50 border-l-4 border-indigo-600 p-4 rounded-r-xl border border-slate-200 border-l-none">
                    <label class="block text-sm font-bold text-indigo-650 mb-2">การนำไปใช้ประโยชน์เชิงพาณิชย์/นโยบาย/สาธารณะ</label>
                    <textarea name="utilization_impact" rows="3" class="w-full bg-white border border-slate-200 rounded-xl text-slate-800 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors placeholder-slate-400 font-semibold" placeholder="อธิบายว่าผลการวิจัยถูกนำไปใช้โดยหน่วยงานใดบ้าง เช่น ธุรกิจ SMEs นำโมเดลไปประยุกต์ใช้จริง"><?= htmlspecialchars((string) ($existingReport['utilization_impact'] ?? '')) ?></textarea>
                </div>
                
                <div class="bg-slate-50 border-l-4 border-indigo-400 p-4 rounded-r-xl border border-slate-200 border-l-none">
                    <label class="block text-sm font-bold text-indigo-650 mb-2">ข้อมูลสำหรับเชื่อมโยงสู่การเรียนการสอน (Curriculum)</label>
                    <textarea name="curriculum_suggestions" rows="3" class="w-full bg-white border border-slate-200 rounded-xl text-slate-800 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors placeholder-slate-400 font-semibold" placeholder="ข้อเสนอแนะว่าควรปรับปรุง/เพิ่มเนื้อหาส่วนใดในหลักสูตร หรือรายวิชา"><?= htmlspecialchars((string) ($existingReport['curriculum_suggestions'] ?? '')) ?></textarea>
                </div>
                
                <div class="bg-slate-50 border-l-4 border-emerald-500 p-4 rounded-r-xl border border-slate-200 border-l-none">
                    <label class="block text-sm font-bold text-emerald-650 mb-2">ข้อเสนอแนะต่อคณะ/หน่วยงานบริหารวิจัย</label>
                    <textarea name="faculty_suggestions" rows="2" class="w-full bg-white border border-slate-200 rounded-xl text-slate-800 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors placeholder-slate-400 font-semibold" placeholder="เช่น กระบวนการขออนุมัติงบประมาณมีความล่าช้า ควรปรับปรุง"><?= htmlspecialchars((string) ($existingReport['faculty_suggestions'] ?? '')) ?></textarea>
                </div>
            </div>
        </div>

        <!-- Section 4: รายการตรวจสอบก่อนปิดโครงการ -->
        <div class="card p-6">
            <h2 class="text-lg font-bold text-slate-800 mb-5 pb-3 border-b border-slate-100 flex items-center gap-2">
                4. รายการตรวจสอบก่อนปิดโครงการ
            </h2>
            
            <div class="space-y-4">
                <label class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition border border-transparent hover:border-slate-200 cursor-pointer">
                    <input type="checkbox" name="checklist_report_sent" value="1" class="w-5 h-5 rounded border-slate-350 text-indigo-600 focus:ring-indigo-500 bg-white" <?= ($existingReport['checklist_report_sent'] ?? 0) ? 'checked' : '' ?>>
                    <span class="text-slate-700 text-sm font-bold">ส่งรายงานฉบับสมบูรณ์แล้ว</span>
                </label>
                
                <label class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition border border-transparent hover:border-slate-200 cursor-pointer">
                    <input type="checkbox" name="checklist_budget_cleared" value="1" class="w-5 h-5 rounded border-slate-350 text-indigo-600 focus:ring-indigo-500 bg-white" <?= ($existingReport['checklist_budget_cleared'] ?? 0) ? 'checked' : '' ?>>
                    <span class="text-slate-700 text-sm font-bold">เคลียร์งบประมาณคงเหลือ หรือทำเรื่องส่งคืนแล้ว</span>
                </label>
                
                <label class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition border border-transparent hover:border-slate-200 cursor-pointer">
                    <input type="checkbox" name="checklist_outputs_registered" value="1" class="w-5 h-5 rounded border-slate-350 text-indigo-600 focus:ring-indigo-500 bg-white" <?= ($existingReport['checklist_outputs_registered'] ?? 0) ? 'checked' : '' ?>>
                    <span class="text-slate-700 text-sm font-bold">(ถ้ามี) ส่งข้อมูลผลงานตีพิมพ์/สิทธิบัตร เข้าสู่ระบบเรียบร้อยแล้ว</span>
                </label>
                
                <label class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition border border-transparent hover:border-slate-200 cursor-pointer">
                    <input type="checkbox" name="checklist_project_closed" value="1" class="w-5 h-5 rounded border-slate-350 text-indigo-600 focus:ring-indigo-500 bg-white" <?= ($existingReport['checklist_project_closed'] ?? 0) ? 'checked' : '' ?>>
                    <span class="text-slate-700 text-sm font-bold mt-0.5">ได้รับการอนุมัติปิดโครงการจากคณะกรรมการวิจัยแล้ว (บันทึกในระบบ)</span>
                </label>
            </div>
        </div>

        <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 p-4 z-[100] flex justify-end gap-4 shadow-lg">
            <button type="submit" name="action_type" value="draft" class="px-6 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors font-semibold shadow-sm">บันทึกร่าง (Save Draft)</button>
            <button type="submit" name="action_type" value="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-bold flex items-center gap-2 shadow-sm">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                ส่งรายงานฉบับสมบูรณ์และขอปิดโครงการ
            </button>
        </div>
        
    </form>
</div>
<div class="h-24 w-full block clear-both"></div>

<script>
function updateFileName(input, targetId) {
    const target = document.getElementById(targetId);
    if (input.files && input.files[0]) {
        target.textContent = input.files[0].name;
        target.classList.add('text-indigo-600');
        target.classList.remove('text-slate-500');
    } else {
        target.textContent = 'คลิกเพื่อเลือกไฟล์แนบ (PDF)';
        target.classList.remove('text-indigo-600');
        target.classList.add('text-slate-500');
    }
}
</script>
