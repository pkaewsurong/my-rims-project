<?php 
ob_start();
$isEdit = !empty($proposal);
$isAdmin = hasRole('admin');
$isOwner = $isEdit && ($proposal['user_id'] == authUser()['id']);
$readonly = $isEdit && $isAdmin && !$isOwner;
$reviewerMode = $isEdit && $isAdmin && !$isOwner;

$researcherName = $isEdit ? ($proposal['researcher_name'] ?? e(authUser()['name'])) : e(authUser()['name']);

$editMilestones = [];
if ($isEdit && !empty($proposal['milestones'])) {
    $decoded = json_decode($proposal['milestones'], true);
    if (is_array($decoded)) $editMilestones = $decoded;
}
$editOutputs = [];
if ($isEdit && !empty($proposal['expected_outputs'])) {
    $decoded = json_decode($proposal['expected_outputs'], true);
    if (is_array($decoded)) $editOutputs = $decoded;
}
?>

<div class="mb-8">
    <a href="<?= ($reviewerMode ?? false) ? '/public/proposals/review?id=' . ($proposal['id'] ?? '') : '/public/projects' ?>" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-indigo-650 transition-colors mb-4">
        <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        ย้อนกลับ
    </a>
    <div class="flex justify-between items-center bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-800 tracking-wide">
                <?= $isEdit ? 'แก้ไขข้อเสนอโครงการ' : 'ลงทะเบียนหัวข้อวิจัยและขอทุน' ?>
            </h2>
            <p class="mt-2 text-sm text-slate-500">ระบบบริหารงานวิจัยและนวัตกรรม (RIMS) | รหัสโครงการ: <span class="text-indigo-600 font-mono font-bold"><?= $isEdit ? '#' . str_pad($proposal['id'], 6, '0', STR_PAD_LEFT) : '(New Draft)' ?></span></p>
        </div>
        <div class="text-right text-sm">
            <p class="text-slate-500">นักวิจัย: <span class="text-slate-800 font-semibold"><?= $researcherName ?></span></p>
            <p class="text-slate-500">สถานะ: <span class="text-slate-800 font-semibold"><?= $isEdit ? e($proposal['status']) : 'ร่าง (Draft)' ?></span></p>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="border-b border-slate-200 mb-8 max-w-5xl">
    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
        <button onclick="changeTab(1)" id="tab-btn-1" class="tab-btn active-tab border-indigo-600 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-colors cursor-pointer">
            1. ข้อมูลหลักและคณะทำงาน
        </button>
        <button onclick="changeTab(2)" id="tab-btn-2" class="tab-btn border-transparent text-slate-400 hover:text-slate-650 whitespace-nowrap py-4 px-1 border-b-2 font-semibold text-sm transition-colors cursor-pointer">
            2. งบประมาณและแผนงาน
        </button>
        <button onclick="changeTab(3)" id="tab-btn-3" class="tab-btn border-transparent text-slate-400 hover:text-slate-650 whitespace-nowrap py-4 px-1 border-b-2 font-semibold text-sm transition-colors cursor-pointer">
            3. ผลผลิตและเอกสารแนบ
        </button>
    </nav>
</div>

<form action="/public/proposals/<?= $isEdit ? 'update' : 'store' ?>" method="POST" enctype="multipart/form-data" id="proposal-form" class="relative z-10 max-w-5xl mb-24">
    <input type="hidden" name="status" id="proposal-status" value="draft">
    <?php if ($isEdit): ?>
    <input type="hidden" name="proposal_id" value="<?= $proposal['id'] ?>">
    <?php endif; ?>

    <!-- STEP 1 -->
    <div id="step-1" class="step-content block space-y-6">
        <div class="card p-8 relative overflow-hidden">
            <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-650" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                ข้อมูลโครงการวิจัยหลัก
            </h3>
            
            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">ชื่อโครงการวิจัย (ภาษาไทย) <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" required <?= $readonly ? 'disabled' : '' ?> class="block w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="โครงการวิจัยเพื่อพัฒนา..." value="<?= $isEdit ? e($proposal['title']) : '' ?>">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">ชื่อโครงการวิจัย (ภาษาอังกฤษ)</label>
                    <input type="text" name="title_en" <?= $readonly ? 'disabled' : '' ?> class="block w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="Research Project on Development of..." value="<?= $isEdit ? e($proposal['title_en']) : '' ?>">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">ประเภทงานวิจัย <span class="text-rose-500">*</span></label>
                    <select name="research_type" required <?= $readonly ? 'disabled' : '' ?> class="block w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm appearance-none">
                        <option value="" disabled <?= !$isEdit || empty($proposal['research_type']) ? 'selected' : '' ?>>-- เลือก --</option>
                        <option value="Basic Research" <?= $isEdit && $proposal['research_type'] === 'Basic Research' ? 'selected' : '' ?>>การวิจัยพื้นฐาน (Basic Research)</option>
                        <option value="Applied Research" <?= $isEdit && $proposal['research_type'] === 'Applied Research' ? 'selected' : '' ?>>การวิจัยประยุกต์ (Applied Research)</option>
                        <option value="Experimental Development" <?= $isEdit && $proposal['research_type'] === 'Experimental Development' ? 'selected' : '' ?>>การพัฒนาทดลอง (Experimental Development)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">คำสำคัญ (Keywords) (คั่นด้วย ,)</label>
                    <input type="text" name="keywords" <?= $readonly ? 'disabled' : '' ?> class="block w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="เช่น FinTech, Marketing, AI, Startup" value="<?= $isEdit ? e($proposal['keywords']) : '' ?>">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">บทคัดย่อ / หลักการและเหตุผล (Abstract) <span class="text-rose-500">*</span></label>
                    <textarea name="abstract" rows="4" required <?= $readonly ? 'disabled' : '' ?> class="block w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="อธิบายวัตถุประสงค์ ขอบเขต และความสำคัญของงานวิจัยโดยย่อ"><?= $isEdit ? e($proposal['abstract']) : '' ?></textarea>
                </div>
            </div>
        </div>

        <div class="card p-8 relative overflow-hidden">
            <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-650" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                คณะทำงานวิจัย
            </h3>
            
            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-4 sm:gap-x-8 items-end mb-6">
                <div class="sm:col-span-3">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">นักวิจัยหลัก / หัวหน้าโครงการ (Principal Investigator - PI) <span class="text-rose-500">*</span></label>
                    <input type="text" value="<?= $researcherName ?>" readonly class="block w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-100 text-slate-500 cursor-not-allowed sm:text-sm font-medium">
                </div>
                <div class="sm:col-span-1">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">สัดส่วนการทำงาน (%)</label>
                    <input type="number" name="pi_proportion" id="pi_proportion" value="<?= $isEdit ? e($proposal['pi_proportion']) : '100' ?>" min="1" max="100" readonly class="block w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-100 text-slate-500 cursor-not-allowed sm:text-sm font-bold">
                </div>
                
                <div id="team-members-container" class="space-y-4 w-full sm:col-span-4 mt-2">
                    <?php if ($isEdit && !empty($teams)): ?>
                        <?php foreach ($teams as $ti => $tm): ?>
                        <div class="flex flex-col sm:flex-row gap-4 items-end p-4 border border-slate-200 rounded-xl bg-slate-50/50 relative" id="team-row-<?= $ti ?>">
                            <button type="button" onclick="removeTeamMember(<?= $ti ?>)" class="absolute top-2 right-2 text-slate-400 hover:text-rose-600 transition-colors" title="ลบผู้วิจัย">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                            <div class="w-full sm:w-1/2">
                                <label class="block text-xs font-semibold text-slate-650 mb-1.5">ชื่อ-นามสกุล <span class="text-rose-500">*</span></label>
                                <input type="text" name="team_name[]" required value="<?= e($tm['name']) ?>" class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-white text-slate-800 sm:text-sm">
                            </div>
                            <div class="w-full sm:w-1/4">
                                <label class="block text-xs font-semibold text-slate-650 mb-1.5">บทบาท <span class="text-rose-500">*</span></label>
                                <select name="team_role[]" required class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-white text-slate-800 sm:text-sm appearance-none">
                                    <option value="Co-Investigator" <?= $tm['role'] === 'Co-Investigator' ? 'selected' : '' ?>>ผู้ร่วมวิจัย (Co-PI)</option>
                                    <option value="Research Assistant" <?= $tm['role'] === 'Research Assistant' ? 'selected' : '' ?>>ผู้ช่วยวิจัย (RA)</option>
                                    <option value="Coordinator" <?= $tm['role'] === 'Coordinator' ? 'selected' : '' ?>>ผู้ประสานงานโครงการ</option>
                                </select>
                            </div>
                            <div class="w-full sm:w-1/4">
                                <label class="block text-xs font-semibold text-slate-650 mb-1.5">สัดส่วน (%) <span class="text-rose-500">*</span></label>
                                <input type="number" name="team_proportion[]" value="<?= (int)$tm['proportion'] ?>" min="0" max="100" required onchange="calculatePIProportion()" onkeyup="calculatePIProportion()" class="team-proportion-input block w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-white text-slate-800 sm:text-sm">
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <?php if (!$readonly): ?>
                <div class="sm:col-span-4 mt-2">
                    <button type="button" onclick="addTeamMember()" class="px-4 py-2 border border-indigo-600/30 text-indigo-600 rounded-xl text-sm font-semibold hover:bg-indigo-50 transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        เพิ่มผู้ร่วมวิจัย (Add Co-Investigator / Assistant)
                    </button>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="text-sm text-slate-500 border-t border-slate-100 pt-4">
                <p>หมายเหตุ: สัดส่วน % การทำงานรวมกันทั้งหมด (PI + Co-PI + RA) ควรเท่ากับ 100%</p>
            </div>
        </div>
    </div>

    <!-- STEP 2 -->
    <div id="step-2" class="step-content hidden space-y-6">
        <div class="card p-8 relative overflow-hidden">
             <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                งบประมาณที่ขอรับการสนับสนุน
             </h3>
            
            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">งบประมาณรวมทั้งโครงการ (บาท) <span class="text-rose-500">*</span></label>
                    <input type="text" name="budget_total" id="budget_total" required <?= $readonly ? 'disabled' : '' ?>
                        class="amount-input block w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm"
                        placeholder="ระบุจำนวนงบประมาณ" value="<?= $isEdit ? number_format($proposal['budget_total'], 0) : '' ?>">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">แหล่งทุน</label>
                    <select name="funding_source_id" <?= $readonly ? 'disabled' : '' ?> class="block w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm appearance-none">
                        <option value="" <?= !$isEdit || empty($proposal['funding_source_id']) ? 'selected' : '' ?>>-- เลือก --</option>
                        <?php foreach($funding_sources as $source): ?>
                            <option value="<?= e($source['id']) ?>" <?= $isEdit && $proposal['funding_source_id'] == $source['id'] ? 'selected' : '' ?>><?= e($source['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">รายละเอียดการใช้จ่ายโดยย่อ (ตามหมวด)</label>
                    <textarea name="budget_details" rows="3" <?= $readonly ? 'disabled' : '' ?> class="block w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="สรุปการใช้จ่าย: หมวดค่าตอบแทน, หมวดวัสดุ, หมวดค่าใช้สอย..."><?= $isEdit ? e($proposal['budget_details']) : '' ?></textarea>
                    <p class="mt-2 text-xs text-slate-500">* การแจกแจงงบประมาณแบบละเอียดจะทำในเอกสารแนบ (Tab 3)</p>
                </div>
            </div>
        </div>

        <div class="card p-8 relative overflow-hidden">
             <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                กำหนดการและระยะเวลาโครงการ
             </h3>
            
            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">วันเริ่มต้นโครงการ (Start Date) <span class="text-rose-500">*</span></label>
                    <input type="date" name="start_date" value="<?= $isEdit ? e($proposal['start_date']) : '' ?>" <?= $readonly ? 'disabled' : '' ?>
                        class="block w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">วันสิ้นสุดโครงการ (End Date) <span class="text-rose-500">*</span></label>
                    <input type="date" name="end_date" value="<?= $isEdit ? e($proposal['end_date']) : '' ?>" <?= $readonly ? 'disabled' : '' ?>
                        class="block w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">แผนงานหลัก (Milestones) ตลอดระยะเวลาโครงการ</label>
                    <div id="milestones-container" class="space-y-3">
                        <?php if ($isEdit && !empty($editMilestones)): ?>
                            <?php foreach ($editMilestones as $mi => $ms): ?>
                            <div <?= $mi > 0 ? 'id="milestone-row-' . $mi . '"' : '' ?> class="flex items-start space-x-3 bg-slate-55 p-3 rounded-xl border border-slate-200 relative">
                                <div class="flex-grow space-y-3">
                                    <input type="text" name="milestone_name[]" value="<?= e($ms['name'] ?? '') ?>" class="block w-full px-4 py-2 border border-slate-200 rounded-lg bg-white text-slate-800 sm:text-sm" placeholder="ชื่อระยะเวลา" required>
                                    <textarea name="milestone_description[]" rows="2" class="block w-full px-4 py-2 border border-slate-200 rounded-lg bg-white text-slate-800 sm:text-sm resize-y" placeholder="รายละเอียด" required><?= e($ms['description'] ?? '') ?></textarea>
                                </div>
                                <?php if ($mi > 0): ?>
                                <button type="button" onclick="removeMilestoneRow(<?= $mi ?>)" class="p-2 text-slate-400 hover:text-rose-600 rounded-lg transition-colors flex-shrink-0"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <div class="flex items-start space-x-3 bg-slate-50 p-3 rounded-xl border border-slate-150">
                            <div class="flex-grow space-y-3">
                                <input type="text" name="milestone_name[]" class="block w-full px-4 py-2 border border-slate-200 rounded-lg bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm" placeholder="ชื่อระยะเวลา เช่น งวดที่ 1 หรือ เดือนที่ 1-3" required>
                                <textarea name="milestone_description[]" rows="2" class="block w-full px-4 py-2 border border-slate-200 rounded-lg bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm resize-y" placeholder="รายละเอียดของงานที่จะทำ/ผลผลิตที่จะได้" required></textarea>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if (!$readonly): ?>
                    <div class="mt-3 text-right">
                        <button type="button" onclick="addMilestoneRow()" class="inline-flex items-center px-4 py-2 bg-slate-50 hover:bg-slate-100 border border-slate-250 text-slate-700 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            เพิ่ม Milestone
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- STEP 3 -->
    <div id="step-3" class="step-content hidden space-y-6">
        <div class="card p-8 relative overflow-hidden">
            <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-650" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                การเชื่อมโยงเชิงกลยุทธ์และผลผลิต (Outputs)
            </h3>
            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">สอดคล้องกับกลยุทธ์มหาวิทยาลัย/คณะ ด้านใด</label>
                    <input type="text" name="strategic_link" <?= $readonly ? 'disabled' : '' ?> class="block w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="โปรดระบุ" value="<?= $isEdit ? e($proposal['strategic_link']) : '' ?>">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">ตัวชี้วัดที่คาดว่าจะส่งผลกระทบโดยตรง</label>
                    <input type="text" name="impact_indicator" <?= $readonly ? 'disabled' : '' ?> class="block w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="โปรดระบุ" value="<?= $isEdit ? e($proposal['impact_indicator']) : '' ?>">
                </div>
            </div>
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-3">ผลผลิต (Expected Outputs) ที่คาดหวัง <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <label class="flex items-center space-x-3 text-sm text-slate-650 cursor-pointer">
                            <input type="checkbox" name="expected_outputs[]" value="Journal/Proceeding" <?= in_array('Journal/Proceeding', $editOutputs) ? 'checked' : '' ?> <?= $readonly ? 'disabled' : '' ?> class="form-checkbox h-5 w-5 text-indigo-600 rounded bg-white border-slate-300 focus:ring-indigo-500">
                            <span>บทความตีพิมพ์ (Journal/Proceeding)</span>
                        </label>
                        <label class="flex items-center space-x-3 text-sm text-slate-650 cursor-pointer">
                            <input type="checkbox" name="expected_outputs[]" value="Patent/Petty Patent" <?= in_array('Patent/Petty Patent', $editOutputs) ? 'checked' : '' ?> <?= $readonly ? 'disabled' : '' ?> class="form-checkbox h-5 w-5 text-indigo-600 rounded bg-white border-slate-300 focus:ring-indigo-500">
                            <span>สิทธิบัตร/อนุสิทธิบัตร (Patent)</span>
                        </label>
                        <label class="flex items-center space-x-3 text-sm text-slate-650 cursor-pointer">
                            <input type="checkbox" name="expected_outputs[]" value="Creative Work" <?= in_array('Creative Work', $editOutputs) ? 'checked' : '' ?> <?= $readonly ? 'disabled' : '' ?> class="form-checkbox h-5 w-5 text-indigo-600 rounded bg-white border-slate-300 focus:ring-indigo-500">
                            <span>ผลงานสร้างสรรค์ (Creative Work)</span>
                        </label>
                        <label class="flex items-center space-x-3 text-sm text-slate-650 cursor-pointer">
                            <input type="checkbox" name="expected_outputs[]" value="Policy Impact" <?= in_array('Policy Impact', $editOutputs) ? 'checked' : '' ?> <?= $readonly ? 'disabled' : '' ?> class="form-checkbox h-5 w-5 text-indigo-600 rounded bg-white border-slate-300 focus:ring-indigo-500">
                            <span>การนำไปใช้ประโยชน์ในเชิงนโยบาย</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card p-8 relative overflow-hidden">
            <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                เอกสารแนบประกอบการพิจารณา
            </h3>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <label class="text-sm font-semibold text-slate-700">1. ไฟล์ข้อเสนอโครงการฉบับสมบูรณ์ (Research Proposal) <span class="text-rose-500">*</span></label>
                    <input type="file" name="file_proposal" <?= $readonly ? 'disabled' : '' ?> class="text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-650 hover:file:bg-indigo-100 cursor-pointer">
                </div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <label class="text-sm font-semibold text-slate-700">2. งบประมาณโดยละเอียด (Budget Breakdown)</label>
                    <input type="file" name="file_budget" <?= $readonly ? 'disabled' : '' ?> class="text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-slate-50 file:text-slate-650 hover:file:bg-slate-100 cursor-pointer">
                </div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <label class="text-sm font-semibold text-slate-700">3. ประวัติย่อของคณะทำงาน (CVs)</label>
                    <input type="file" name="file_cv" <?= $readonly ? 'disabled' : '' ?> class="text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-slate-50 file:text-slate-650 hover:file:bg-slate-100 cursor-pointer">
                </div>
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <label class="text-sm font-semibold text-slate-700">4. เอกสารขอรับการพิจารณาจริยธรรม (ถ้ามี)</label>
                    <input type="file" name="file_ethics" <?= $readonly ? 'disabled' : '' ?> class="text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-slate-50 file:text-slate-650 hover:file:bg-slate-100 cursor-pointer">
                </div>
                <p class="text-xs text-slate-400 mt-2">* ขอให้แนบเอกสารในรูปแบบ PDF เท่านั้น (ขนาดไม่เกิน 10MB)</p>
            </div>
        </div>
    </div>

    <!-- Sticky Footer Actions -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 p-4 z-40 shadow-lg">
        <div class="max-w-5xl mx-auto flex justify-between items-center px-4 sm:px-6 lg:px-8">
            <button type="button" id="btn-prev" onclick="changeTab(currentTab - 1)" class="hidden px-6 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 bg-white hover:bg-slate-50 transition-colors shadow-sm">
                &larr; กลับ (Previous)
            </button>
            <div id="btn-prev-placeholder" class="w-24"></div>
            
            <div class="flex space-x-4">
                <a href="<?= ($reviewerMode ?? false) ? '/public/proposals/review?id=' . ($proposal['id'] ?? '') : '/public/projects' ?>" class="px-6 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 bg-white hover:bg-slate-50 transition-colors shadow-sm">
                    <?= ($reviewerMode ?? false) ? 'กลับไปหน้าประเมิน' : 'ยกเลิก (Cancel)' ?>
                </a>
                <?php if (!$readonly): ?>
                <button type="submit" formnovalidate onclick="document.getElementById('proposal-status').value='draft'" class="px-6 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 bg-white hover:bg-slate-50 transition-colors shadow-sm">
                    บันทึกฉบับร่าง (Save Draft)
                </button>
                <?php endif; ?>
                <button type="button" id="btn-next" onclick="changeTab(currentTab + 1)" class="btn-primary px-8 py-2.5 rounded-xl text-sm font-bold tracking-wide flex items-center shadow-sm">
                    ถัดไป (Next) &rarr;
                </button>
                <?php if (!$readonly): ?>
                <button type="submit" id="btn-submit" onclick="document.getElementById('proposal-status').value='submitted'" class="hidden px-8 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold tracking-wide flex items-center shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    ส่งเพื่อพิจารณา (Submit for Review)
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</form>

<script>
    let currentTab = 1;
    const totalTabs = 3;

    function changeTab(tabNumber) {
        if (tabNumber < 1 || tabNumber > totalTabs) return;
        
        const isReadonly = <?= $readonly ? 'true' : 'false' ?>;
        if (tabNumber > currentTab && !isReadonly) {
            const currentStepEl = document.getElementById('step-' + currentTab);
            const requiredInputs = currentStepEl.querySelectorAll('[required]');
            let isValid = true;
            requiredInputs.forEach(input => {
                if (!input.value) {
                    isValid = false;
                    input.classList.add('border-rose-500', 'ring-1', 'ring-rose-500');
                } else {
                    input.classList.remove('border-rose-500', 'ring-1', 'ring-rose-500');
                }
            });
            if (!isValid) {
                alert('กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วนก่อนไปหน้าถัดไป');
                return;
            }
        }

        for(let i=1; i<=totalTabs; i++) {
            document.getElementById('step-' + i).classList.add('hidden');
            document.getElementById('step-' + i).classList.remove('block');
            
            const btn = document.getElementById('tab-btn-' + i);
            btn.className = 'tab-btn border-transparent text-slate-400 hover:text-slate-650 whitespace-nowrap py-4 px-1 border-b-2 font-semibold text-sm transition-colors cursor-pointer';
        }

        document.getElementById('step-' + tabNumber).classList.remove('hidden');
        document.getElementById('step-' + tabNumber).classList.add('block');
        
        const activeBtn = document.getElementById('tab-btn-' + tabNumber);
        activeBtn.className = 'tab-btn active-tab border-indigo-650 text-indigo-655 whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-colors cursor-pointer';

        currentTab = tabNumber;
        updateFooterButtons();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function updateFooterButtons() {
        const prevBtn = document.getElementById('btn-prev');
        const nextBtn = document.getElementById('btn-next');
        const submitBtn = document.getElementById('btn-submit');
        const placeholder = document.getElementById('btn-prev-placeholder');

        if (currentTab === 1) {
            prevBtn.classList.add('hidden');
            placeholder.classList.remove('hidden');
        } else {
            prevBtn.classList.remove('hidden');
            placeholder.classList.add('hidden');
        }

        if (currentTab === totalTabs) {
            nextBtn.classList.add('hidden');
            submitBtn.classList.remove('hidden');
        } else {
            nextBtn.classList.remove('hidden');
            submitBtn.classList.add('hidden');
        }
    }

    let teamIndex = <?= ($isEdit && !empty($teams) && is_countable($teams)) ? count($teams) : 0 ?>;
    function addTeamMember() {
        const container = document.getElementById('team-members-container');
        const rowHtml = `
            <div class="flex flex-col sm:flex-row gap-4 items-end p-4 border border-slate-200 rounded-xl bg-slate-50/50 relative" id="team-row-${teamIndex}">
                <button type="button" onclick="removeTeamMember(${teamIndex})" class="absolute top-2 right-2 text-slate-400 hover:text-rose-600 transition-colors" title="ลบผู้วิจัย">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                <div class="w-full sm:w-1/2">
                    <label class="block text-xs font-semibold text-slate-650 mb-1.5">ชื่อ-นามสกุล (Name) <span class="text-rose-500">*</span></label>
                    <input type="text" name="team_name[]" required class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors sm:text-sm" placeholder="เช่น นายสมชาย ใจดี">
                </div>
                <div class="w-full sm:w-1/4">
                    <label class="block text-xs font-semibold text-slate-650 mb-1.5">บทบาท (Role) <span class="text-rose-500">*</span></label>
                    <select name="team_role[]" required class="block w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors sm:text-sm appearance-none">
                        <option value="Co-Investigator">ผู้ร่วมวิจัย (Co-PI)</option>
                        <option value="Research Assistant">ผู้ช่วยวิจัย (RA)</option>
                        <option value="Coordinator">ผู้ประสานงานโครงการ</option>
                    </select>
                </div>
                <div class="w-full sm:w-1/4">
                    <label class="block text-xs font-semibold text-slate-650 mb-1.5">สัดส่วน (%) <span class="text-rose-500">*</span></label>
                    <input type="number" name="team_proportion[]" value="0" min="0" max="100" required onchange="calculatePIProportion()" onkeyup="calculatePIProportion()" class="team-proportion-input block w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors sm:text-sm">
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', rowHtml);
        teamIndex++;
        calculatePIProportion();
    }

    function removeTeamMember(index) {
        document.getElementById(`team-row-${index}`).remove();
        calculatePIProportion();
    }

    function calculatePIProportion() {
        const proportionInputs = document.querySelectorAll('.team-proportion-input');
        let totalCoI = 0;
        proportionInputs.forEach(input => {
            totalCoI += parseInt(input.value) || 0;
        });
        
        const piInput = document.getElementById('pi_proportion');
        let newPiProp = 100 - totalCoI;
        
        if (newPiProp < 0) {
            if (typeof showThemeAlert === 'function') {
                showThemeAlert('สัดส่วนการทำงานรวมเกิน 100% กรุณาปรับลดสัดส่วนของผู้ร่วมวิจัย');
            } else {
                alert('สัดส่วนการทำงานรวมเกิน 100% กรุณาปรับลดสัดส่วนของผู้ร่วมวิจัย');
            }
            newPiProp = 0;
            piInput.classList.add('text-rose-500');
        } else {
            piInput.classList.remove('text-rose-500');
        }
        
        piInput.value = newPiProp;
    }

    let milestoneIndex = <?= $isEdit && !empty($editMilestones) && is_countable($editMilestones) ? count($editMilestones) : 1 ?>;
    function addMilestoneRow() {
        const container = document.getElementById('milestones-container');
        const rowHtml = `
            <div id="milestone-row-${milestoneIndex}" class="flex items-start space-x-3 bg-slate-50 p-3 rounded-xl border border-slate-200 relative">
                <div class="flex-grow space-y-3">
                    <input type="text" name="milestone_name[]" class="block w-full px-4 py-2 border border-slate-205 rounded-lg bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm" placeholder="ชื่อระยะเวลา เช่น งวดที่ 2 หรือ เดือนที่ 4-6" required>
                    <textarea name="milestone_description[]" rows="2" class="block w-full px-4 py-2 border border-slate-205 rounded-lg bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm resize-y" placeholder="รายละเอียดของงานที่จะทำ/ผลผลิตที่จะได้" required></textarea>
                </div>
                <button type="button" onclick="removeMilestoneRow(${milestoneIndex})" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors flex-shrink-0" title="ลบ">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', rowHtml);
        milestoneIndex++;
    }

    function removeMilestoneRow(index) {
        document.getElementById(`milestone-row-${index}`).remove();
    }

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

        const form = document.getElementById('proposal-form');
        form.addEventListener('submit', function(e) {
            const isReadonly = <?= $readonly ? 'true' : 'false' ?>;
            if (isReadonly) {
                e.preventDefault();
                return;
            }
            
            const statusVal = document.getElementById('proposal-status').value;
            
            // If saving as draft, skip strict input validations
            if (statusVal !== 'draft') {
                const totalTabCount = 3;
                let firstInvalidTab = null;

                for (let t = 1; t <= totalTabCount; t++) {
                    const stepEl = document.getElementById('step-' + t);
                    const reqInputs = stepEl.querySelectorAll('[required]');
                    let isTabValid = true;

                    reqInputs.forEach(input => {
                        if (!input.value) {
                            isTabValid = false;
                            input.classList.add('border-rose-500', 'ring-1', 'ring-rose-500');
                        } else {
                            input.classList.remove('border-rose-500', 'ring-1', 'ring-rose-500');
                        }
                    });

                    if (!isTabValid && firstInvalidTab === null) {
                        firstInvalidTab = t;
                    }
                }

                if (firstInvalidTab !== null) {
                    e.preventDefault();
                    changeTab(firstInvalidTab);
                    alert('กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วนในแถบสีแดง');
                    return;
                }
            }
            
            // Clean up currency commas before submitting
            amountInputs.forEach(input => {
                input.value = input.value.replace(/,/g, '');
            });
        });
    });
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php'; 
?>
