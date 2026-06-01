<?php ob_start(); ?>

<div class="mb-8">
    <a href="javascript:history.back()" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-indigo-650 transition-colors mb-4">
        <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        ย้อนกลับ
    </a>
    <div class="flex justify-between items-center bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-wide">
                พิจารณาและอนุมัติข้อเสนอโครงการวิจัย
            </h2>
            <p class="mt-1 text-sm text-slate-500">ระบบบริหารงานวิจัยและนวัตกรรม (RIMS) | โครงการ: <span class="text-slate-800 font-semibold"><?= e($proposal['title']) ?></span></p>
        </div>
        <div class="text-right text-sm">
            <p class="text-slate-500">ผู้พิจารณา: <span class="text-slate-800 font-semibold"><?= e(authUser()['name']) ?></span> (คณะกรรมการวิจัย)</p>
        </div>
    </div>
</div>

<form action="/public/proposals/review" method="POST" id="review_form" class="relative z-10 max-w-7xl mb-24">
    <input type="hidden" name="proposal_id" value="<?= $proposal['id'] ?>">

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- ฝั่งซ้าย: รายละเอียดข้อเสนอโครงการ -->
        <div class="lg:col-span-8 space-y-6">
            <div class="card p-6 relative overflow-hidden">
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center justify-between border-b border-slate-100 pb-2">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        รายละเอียดโครงการที่เสนอขอ
                    </div>
                    <a href="/public/proposals/<?= $proposal['id'] ?>/edit" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-[11px] font-bold text-indigo-650 hover:bg-slate-100 transition-all flex items-center group">
                        <svg class="w-3.5 h-3.5 mr-1.5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        ดูรายละเอียดโครงการทั้งหมด
                    </a>
                </h3>
                
                <div class="space-y-4 text-sm">
                    <div>
                        <span class="block text-slate-400 mb-1 font-semibold">ชื่อโครงการ:</span>
                        <p class="text-slate-800 font-bold text-base"><?= e($proposal['title']) ?></p>
                        <?php if($proposal['title_en']): ?>
                            <p class="text-slate-550 italic font-medium"><?= e($proposal['title_en']) ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <span class="block text-slate-400 mb-1 font-semibold">นักวิจัยหลัก (PI):</span>
                        <p class="text-slate-800 font-semibold"><?= e($proposal['user_name'] ?? 'N/A') ?> (<?= $proposal['pi_proportion'] ?? 100 ?>% สัดส่วนงาน)</p>
                    </div>

                    <?php if(!empty($teams)): ?>
                        <div>
                            <span class="block text-slate-400 mb-1 font-semibold">ผู้ร่วมวิจัย:</span>
                            <ul class="list-disc list-inside text-slate-600 font-medium">
                                <?php foreach($teams as $team): ?>
                                    <li><?= e($team['name']) ?> (<?= e($team['role']) ?>, <?= $team['proportion'] ?>%)</li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <div>
                        <span class="block text-slate-400 mb-1 font-semibold">งบประมาณที่ขอ:</span>
                        <p class="text-indigo-650 font-extrabold text-xl">฿<?= number_format($proposal['budget_total'], 2) ?> บาท</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="block text-slate-400 mb-1 font-semibold">ระยะเวลา:</span>
                            <p class="text-slate-800 font-semibold">
                                <?= $proposal['start_date'] ? date('d M Y', strtotime($proposal['start_date'])) : '-' ?> 
                                ถึง 
                                <?= $proposal['end_date'] ? date('d M Y', strtotime($proposal['end_date'])) : '-' ?>
                            </p>
                        </div>
                        <div>
                            <span class="block text-slate-400 mb-1 font-semibold">ประเภทงานวิจัย:</span>
                            <p class="text-slate-800 font-semibold"><?= e($proposal['research_type']) ?: '-' ?></p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100">
                    <h4 class="text-md font-bold text-slate-800 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        บทคัดย่อ/วัตถุประสงค์
                    </h4>
                    <div class="bg-slate-50 p-4 rounded-xl text-slate-650 text-sm whitespace-pre-wrap border border-slate-150"><?= e($proposal['abstract']) ?></div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100">
                    <h4 class="text-md font-bold text-slate-800 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                        เอกสารแนบ (คลิกเพื่อดาวน์โหลด)
                    </h4>
                    <ul class="space-y-2 text-sm font-semibold">
                        <?php if($proposal['file_proposal']): ?>
                            <li><a href="/public/uploads/proposals/<?= e($proposal['file_proposal']) ?>" target="_blank" class="text-indigo-600 hover:text-indigo-850 flex items-center"><svg class="w-4 h-4 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Full Proposal (ฉบับสมบูรณ์)</a></li>
                        <?php endif; ?>
                        <?php if($proposal['file_budget']): ?>
                            <li><a href="/public/uploads/proposals/<?= e($proposal['file_budget']) ?>" target="_blank" class="text-indigo-600 hover:text-indigo-850 flex items-center"><svg class="w-4 h-4 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Budget Breakdown (รายละเอียดงบประมาณ)</a></li>
                        <?php endif; ?>
                        <?php if($proposal['file_cv']): ?>
                            <li><a href="/public/uploads/proposals/<?= e($proposal['file_cv']) ?>" target="_blank" class="text-indigo-600 hover:text-indigo-850 flex items-center"><svg class="w-4 h-4 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> CVs (ประวัติผู้วิจัย)</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100">
                    <h4 class="text-md font-bold text-slate-800 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-650" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        ความเชื่อมโยงเชิงกลยุทธ์
                    </h4>
                    <p class="text-sm text-slate-600 font-medium">ผลผลิตที่คาดหวัง: <?= e($proposal['expected_outputs'] ?: '-') ?></p>
                </div>

                <!-- Milestones Info -->
                <div class="mt-6 pt-4 border-t border-slate-100">
                    <h4 class="text-md font-bold text-slate-800 mb-4 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        แผนงานหลัก (Milestones)
                    </h4>
                    
                    <?php 
                        $milestones = [];
                        if (!empty($proposal['milestones'])) {
                            $decoded = json_decode($proposal['milestones'], true);
                            if (is_array($decoded)) {
                                $milestones = $decoded;
                            } else {
                                $milestones = [['name' => 'ภาพรวม', 'description' => $proposal['milestones']]];
                            }
                        }
                    ?>
                    
                    <?php if(empty($milestones)): ?>
                        <div class="text-slate-400 text-sm italic">ไม่มีข้อมูล Milestones</div>
                    <?php else: ?>
                        <div class="space-y-3">
                        <?php foreach($milestones as $index => $m): ?>
                            <div class="bg-slate-50 border border-slate-150 p-3 rounded-xl flex items-start">
                                <span class="flex-shrink-0 w-6 h-6 rounded bg-indigo-50 text-indigo-600 text-xs font-bold flex items-center justify-center mr-3 mt-0.5 border border-indigo-100"><?= $index + 1 ?></span>
                                <div>
                                    <p class="text-slate-800 text-sm font-bold mb-1"><?= e($m['name'] ?? 'ไม่มีชื่อระยะ') ?></p>
                                    <p class="text-xs text-slate-500 whitespace-pre-wrap leading-relaxed font-semibold"><?= e($m['description'] ?? '') ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ฝั่งขวา: ฟอร์มประเมินและการให้คะแนน -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- คะแนน -->
            <div class="card p-6 relative overflow-hidden">
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center border-b border-amber-200 pb-2">
                    <svg class="w-5 h-5 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    เกณฑ์การให้คะแนน (Scoring & Evaluation)
                </h3>
                
                <table class="w-full text-sm text-left text-slate-650 font-medium">
                    <thead class="text-xs text-slate-500 bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th scope="col" class="py-2 px-3">เกณฑ์การพิจารณา</th>
                            <th scope="col" class="py-2 px-2 text-center w-16">เต็ม</th>
                            <th scope="col" class="py-2 px-2 text-center w-20">คะแนนได้</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php 
                            $is_readonly = ($proposal['status'] === 'approved' || ($review['status'] ?? '') === 'approved' || ($review['status'] ?? '') === 'rejected'); 
                            $readonly_attr = $is_readonly ? 'disabled' : '';
                        ?>
                        <tr>
                            <td class="py-3 px-3">1. ความเป็นไปได้และความเหมาะสมของวิธีวิจัย</td>
                            <td class="py-3 px-2 text-center">(20)</td>
                            <td class="py-3 px-2"><input <?= $readonly_attr ?> type="number" name="score_concept" id="score_concept" min="0" max="20" class="score-input block w-full px-2 py-1 border border-slate-200 rounded bg-slate-50 text-center text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500" value="<?= $review['score_concept'] ?? '' ?>"></td>
                        </tr>
                        <tr>
                            <td class="py-3 px-3">2. คุณภาพและประสบการณ์ของคณะทำงาน</td>
                            <td class="py-3 px-2 text-center">(20)</td>
                            <td class="py-3 px-2"><input <?= $readonly_attr ?> type="number" name="score_team" id="score_team" min="0" max="20" class="score-input block w-full px-2 py-1 border border-slate-200 rounded bg-slate-50 text-center text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500" value="<?= $review['score_team'] ?? '' ?>"></td>
                        </tr>
                        <tr>
                            <td class="py-3 px-3">3. ความสอดคล้องกับกลยุทธ์ของผลกระทบ</td>
                            <td class="py-3 px-2 text-center">(50)</td>
                            <td class="py-3 px-2"><input <?= $readonly_attr ?> type="number" name="score_alignment" id="score_alignment" min="0" max="50" class="score-input block w-full px-2 py-1 border border-slate-200 rounded bg-slate-50 text-center text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500" value="<?= $review['score_alignment'] ?? '' ?>"></td>
                        </tr>
                        <tr>
                            <td class="py-3 px-3">4. ความคุ้มค่าและสมเหตุสมผลของงบประมาณ</td>
                            <td class="py-3 px-2 text-center">(10)</td>
                            <td class="py-3 px-2"><input <?= $readonly_attr ?> type="number" name="score_impact" id="score_impact" min="0" max="10" class="score-input block w-full px-2 py-1 border border-slate-200 rounded bg-slate-50 text-center text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500" value="<?= $review['score_impact'] ?? '' ?>"></td>
                        </tr>
                        <tr class="bg-indigo-50/50 font-bold border-t border-indigo-150">
                            <td class="py-3 px-3 text-right text-indigo-600">รวมคะแนนทั้งหมด:</td>
                            <td class="py-3 px-2 text-center text-indigo-600">100</td>
                            <td class="py-3 px-2 text-center text-2xl text-indigo-650" id="total_score_display"><?= $review['total_score'] ?? '0' ?></td>
                        </tr>
                    </tbody>
                </table>
                <p class="text-xs text-slate-500 mt-2">* เกณฑ์ผ่าน: 60 คะแนน</p>
            </div>

            <!-- ข้อเสนอแนะและความเห็น -->
            <div class="card p-6 relative overflow-hidden">
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center border-b border-slate-100 pb-2">
                    <svg class="w-5 h-5 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    ข้อเสนอแนะและความเห็น
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">ความเห็นจุดแข็ง / จุดเด่นของข้อเสนอ</label>
                        <textarea <?= $readonly_attr ?> name="comments_strengths" rows="3" class="block w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="โครงร่างมีความชัดเจน..."><?= e($review['comments_strengths'] ?? '') ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">ข้อปรับปรุง/เงื่อนไขเพิ่มเติม</label>
                        <textarea <?= $readonly_attr ?> name="comments_suggestions" rows="3" class="block w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="ควรปรับงบประมาณหมวดวัสดุ..."><?= e($review['comments_suggestions'] ?? '') ?></textarea>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <label class="block text-sm font-bold text-slate-800 mb-1.5">ผลการพิจารณาในขั้นตอนสุดท้าย (Decision)</label>
                        <select <?= $readonly_attr ?> name="status" id="status_select" class="block w-full px-4 py-3 border border-slate-200 rounded-xl bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors text-base font-bold appearance-none">
                            <option value="" disabled <?= empty($review['status']) ? 'selected' : '' ?>>-- เลือกผลการพิจารณา --</option>
                            <option value="approved" class="text-emerald-650" <?= ($review['status'] ?? '') === 'approved' ? 'selected' : '' ?>>ผ่าน (รับรอง)</option>
                            <option value="under_review" class="text-amber-600" <?= ($review['status'] ?? '') === 'under_review' ? 'selected' : '' ?>>ให้กลับไปแก้ไข (Revision)</option>
                            <option value="rejected" class="text-rose-600" <?= ($review['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>ไม่อนุมัติ (Reject)</option>
                        </select>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <!-- Sticky Footer Actions (Hidden if approved) -->
    <?php if (!$is_readonly): ?>
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 p-4 z-40 shadow-lg">
        <div class="max-w-7xl mx-auto flex justify-between items-center px-4 sm:px-6 lg:px-8">
            <a href="/public/proposals" class="px-6 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-650 bg-white hover:bg-slate-50 transition-colors shadow-sm">
                ยกเลิก (Cancel)
            </a>
            
            <input type="hidden" name="is_draft" id="is_draft_input" value="1">
            
            <div class="flex space-x-4 flex-wrap">
                <button type="button" onclick="handleReviewSubmit(1)" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 border border-slate-250 rounded-xl text-sm font-semibold text-slate-700 transition-colors shadow-sm">
                    บันทึกความเห็น (Save Draft)
                </button>
                <button type="button" onclick="handleReviewSubmit(0)" class="btn-primary px-8 py-2.5 rounded-xl text-sm font-bold tracking-wide flex items-center shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    บันทึกผลการประเมิน (Submit Review)
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
</form>

<script>
    const scoreInputs = document.querySelectorAll('.score-input');
    const totalDisplay = document.getElementById('total_score_display');

    function calculateTotal() {
        let total = 0;
        scoreInputs.forEach(input => {
            let val = parseInt(input.value) || 0;
            let max = parseInt(input.getAttribute('max')) || 100;
            if (val > max) { val = max; input.value = max; }
            if (val < 0) { val = 0; input.value = 0; }
            total += val;
        });
        totalDisplay.innerText = total;

        if (total >= 60) {
            totalDisplay.classList.remove('text-rose-600');
            totalDisplay.classList.add('text-indigo-650');
        } else {
            totalDisplay.classList.remove('text-indigo-655');
            totalDisplay.classList.add('text-rose-600');
        }
    }

    scoreInputs.forEach(input => {
        input.addEventListener('input', calculateTotal);
    });

    async function handleReviewSubmit(isDraft) {
        document.getElementById('is_draft_input').value = isDraft;
        
        if (isDraft === 0) {
            let valid = true;
            scoreInputs.forEach(input => {
                if (input.value === '') {
                    input.classList.add('border-rose-500');
                    valid = false;
                } else {
                    input.classList.remove('border-rose-500');
                }
            });

            const statusSelect = document.getElementById('status_select');
            if (!statusSelect.value) {
                statusSelect.classList.add('border-rose-500');
                valid = false;
            } else {
                statusSelect.classList.remove('border-rose-500');
            }

            if (!valid) {
                await showThemeAlert("กรุณากรอกคะแนนประเมินและสถานะให้ครบถ้วนสำหรับการส่งประเมินจริง");
                return;
            }
            
            const confirmed = await showThemeConfirm(
                'ยืนยันการประเมิน',
                'คุณแน่ใจหรือไม่ที่จะส่งผลการประเมินนี้? เมื่อยืนยันและพิจารณาให้ผ่านแล้ว จะไม่สามารถแก้ไขข้อมูลประเมินได้อีก',
                true
            );
            
            if (confirmed) {
                document.getElementById('review_form').submit();
            }
        } else {
            document.getElementById('review_form').submit();
        }
    }
</script>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php'; 
?>
