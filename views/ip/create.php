<?php ob_start(); ?>

<div class="mb-8">
    <a href="/public/projects/<?= $project['id'] ?>" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-indigo-650 transition-colors mb-4">
        <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        กลับไปหน้ารายละเอียดโครงการ
    </a>
    <div class="flex justify-between items-center bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-wide">
                ขึ้นทะเบียนทรัพย์สินทางปัญญา/ผลงานสร้างสรรค์ <span class="text-slate-400 font-normal text-lg">(IP Registration)</span>
            </h2>
            <p class="mt-1 text-sm text-slate-550 font-medium">ระบบบริหารงานวิจัยและนวัตกรรม (RIMS) | โครงการ: <span class="text-slate-800 font-bold"><?= e($project['title_th']) ?></span></p>
        </div>
    </div>
</div>

<form action="/public/ip-assets/store" method="POST" enctype="multipart/form-data" class="space-y-6 max-w-5xl mx-auto pb-24">
    <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
    
    <!-- ส่วนที่ 1: ข้อมูลผลงาน -->
    <div class="card p-6 relative overflow-hidden">
        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center border-b border-slate-100 pb-2">
            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
            ส่วนที่ 1: ข้อมูลทรัพย์สินทางปัญญา (IP Details)
        </h3>
        
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">ประเภทผลงาน (IP Type) <span class="text-rose-500">*</span></label>
                    <select name="ip_type" required class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm appearance-none">
                        <option value="Patent">สิทธิบัตร/อนุสิทธิบัตร (Patent/Petty Patent)</option>
                        <option value="Copyright">ลิขสิทธิ์ (Copyright)</option>
                        <option value="Creative">ผลงานสร้างสรรค์อื่นๆ (Creative Work)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">วันที่ผลงานสำเร็จ (Completion Date)</label>
                    <input type="date" name="completion_date" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">ชื่อผลงาน (ภาษาไทย) <span class="text-rose-500">*</span></label>
                    <input type="text" name="name_th" required class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="เช่น กรรมวิธีการสกัดสาร...">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Work Name (English)</label>
                    <input type="text" name="name_en" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="English Title">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">คำสำคัญ (Keywords)</label>
                <input type="text" name="keywords" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="Keyword1, Keyword2...">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">รายละเอียดโดยย่อ (Abstract Details)</label>
                <textarea name="abstract_details" rows="3" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="คำอธิบายผลงาน..."></textarea>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">เลขที่คำขอ/จดทะเบียน</label>
                    <input type="text" name="registration_number" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">หน่วยงานที่รับจดทะเบียน</label>
                    <input type="text" name="registration_agency" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="เช่น กรมทรัพย์สินทางปัญญา">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">วันที่ได้รับจดทะเบียน</label>
                    <input type="date" name="approval_date" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">สถานะทางกฎหมาย</label>
                    <input type="text" name="legal_status" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="เช่น อยู่ระหว่างประกาศโฆษณา">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">การนำไปใช้ประโยชน์</label>
                    <select name="commercial_status" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm appearance-none">
                        <option value="Non-Commercial">รอนำเสนอขาย/ใช้งานทั่วไป</option>
                        <option value="Commercialized">นำไปใช้เชิงพาณิชย์แล้ว</option>
                        <option value="Social">นำไปใช้เพื่อสังคม</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">มูลค่าเชิงเศรษฐกิจ (บาท)</label>
                    <input type="number" name="economic_value" value="0" step="0.01" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">ผลกระทบ/ประโยชน์ที่ได้รับ (Impact Description)</label>
                <textarea name="impact_description" rows="2" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="อธิบายรายละเอียดผลกระทบ..."></textarea>
            </div>
        </div>
    </div>

    <!-- ส่วนที่ 2: สัดส่วนและรายชื่อผู้ประดิษฐ์ -->
    <div class="card p-6 relative overflow-hidden">
        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center border-b border-slate-100 pb-2">
            <svg class="w-5 h-5 mr-2 text-indigo-650" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            ส่วนที่ 2: รายชื่อผู้ประดิษฐ์/สร้างสรรค์ (Inventors)
        </h3>
        
        <div id="inventor-container" class="space-y-4">
            <div class="flex flex-col sm:flex-row gap-4 items-end p-4 border border-slate-200 rounded-xl bg-slate-50/50">
                <div class="w-full sm:w-2/3">
                    <label class="block text-xs font-semibold text-slate-650 mb-1.5">ชื่อ-นามสกุล (Name)</label>
                    <input type="text" name="inventor_name[]" value="<?= e(authUser()['name']) ?>" readonly class="block w-full px-4 py-2 border border-slate-200 rounded-xl bg-slate-100 text-slate-500 cursor-not-allowed sm:text-sm font-semibold">
                </div>
                <div class="w-full sm:w-1/3">
                    <label class="block text-xs font-semibold text-slate-650 mb-1.5">สัดส่วนผลงาน (%)</label>
                    <input type="number" name="inventor_proportion[]" min="1" max="100" value="100" class="block w-full px-4 py-2 border border-slate-200 rounded-xl bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm proportion-input font-bold" onchange="calculateTotalProportion()">
                </div>
            </div>
        </div>
        
        <div class="mt-4 flex justify-between items-center">
            <button type="button" onclick="addInventor()" class="px-4 py-2 border border-indigo-650/30 text-indigo-605 rounded-xl text-sm font-semibold hover:bg-indigo-50 transition-colors flex items-center shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                เพิ่มผู้ประดิษฐ์ร่วม
            </button>
            <div class="text-sm font-semibold text-slate-500">
                สัดส่วนรวม: <span id="total-proportion" class="text-emerald-600 font-bold">100</span>%
            </div>
        </div>
    </div>

    <!-- ส่วนที่ 3: ไฟล์แนบ -->
    <div class="card p-6 relative overflow-hidden">
        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center border-b border-slate-100 pb-2">
            <svg class="w-5 h-5 mr-2 text-indigo-650" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
            ส่วนที่ 3: แนบไฟล์เอกสารที่เกี่ยวข้อง
        </h3>
        
        <div class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <label class="text-sm font-semibold text-slate-700">1. แบบเปิดเผยข้อมูล/คำขอจดแจ้ง (Disclosure/Submission Form) (PDF)</label>
                <input type="file" name="file_submission" accept=".pdf" class="text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-650 hover:file:bg-indigo-100 border border-slate-200 bg-white p-1 cursor-pointer">
            </div>
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <label class="text-sm font-semibold text-slate-700">2. สำเนาใบรับคําขอ/หนังสือสําคัญ (Certificate of Registration) <span class="text-xs text-slate-400 ml-1">(ถ้ามี)</span></label>
                <input type="file" name="file_certificate" accept=".pdf" class="text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-slate-50 file:text-slate-650 hover:file:bg-slate-100 border border-slate-200 bg-white p-1 cursor-pointer">
            </div>
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <label class="text-sm font-semibold text-slate-700">3. เอกสารประกอบอื่นๆ/หลักฐานการใช้งาน (Evidence) (PDF)</label>
                <input type="file" name="file_evidence" accept=".pdf" class="text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-slate-50 file:text-slate-650 hover:file:bg-slate-100 border border-slate-200 bg-white p-1 cursor-pointer">
            </div>
        </div>
    </div>

    <!-- Sticky Footer -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 p-4 z-40 shadow-lg">
        <div class="max-w-5xl mx-auto flex justify-between items-center px-4">
            <a href="/public/projects/<?= $project['id'] ?>" class="px-6 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 bg-white hover:bg-slate-50 transition-colors shadow-sm">
                ยกเลิก (Cancel)
            </a>
            
            <button type="submit" class="btn-primary px-8 py-2.5 rounded-xl text-sm font-bold tracking-wide flex items-center shadow-sm" id="submit-btn">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                บันทึกทรัพย์สินทางปัญญา (Save IP Record)
            </button>
        </div>
    </div>
</form>

<script>
    let inventorIndex = 1;
    function addInventor() {
        const container = document.getElementById('inventor-container');
        const rowHtml = `
            <div class="flex flex-col sm:flex-row gap-4 items-end p-4 border border-slate-200 rounded-xl bg-slate-50/50 relative" id="inventor-row-${inventorIndex}">
                <button type="button" onclick="removeInventor(${inventorIndex})" class="absolute top-2 right-2 text-slate-400 hover:text-rose-605 transition-colors" title="ลบผู้ประดิษฐ์">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                <div class="w-full sm:w-2/3">
                    <label class="block text-xs font-semibold text-slate-650 mb-1.5">ชื่อ-นามสกุล (Name)</label>
                    <input type="text" name="inventor_name[]" required class="block w-full px-4 py-2 border border-slate-200 rounded-xl bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm">
                </div>
                <div class="w-full sm:w-1/3">
                    <label class="block text-xs font-semibold text-slate-650 mb-1.5">สัดส่วนผลงาน (%)</label>
                    <input type="number" name="inventor_proportion[]" min="1" max="100" value="0" class="block w-full px-4 py-2 border border-slate-200 rounded-xl bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm proportion-input font-bold" onchange="calculateTotalProportion()">
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', rowHtml);
        inventorIndex++;
    }

    function removeInventor(index) {
        document.getElementById(`inventor-row-${index}`).remove();
        calculateTotalProportion();
    }

    function calculateTotalProportion() {
        const inputs = document.querySelectorAll('.proportion-input');
        let total = 0;
        inputs.forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        
        const totalSpan = document.getElementById('total-proportion');
        totalSpan.textContent = total;
        
        if (total === 100) {
            totalSpan.className = 'text-emerald-600 font-bold';
            document.getElementById('submit-btn').disabled = false;
        } else {
            totalSpan.className = 'text-rose-600 font-bold';
            document.getElementById('submit-btn').disabled = true;
        }
    }
</script>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php'; 
?>
