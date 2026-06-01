<?php ob_start(); ?>

<div class="mb-8">
    <a href="/public/projects/<?= $project['id'] ?>" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-indigo-650 transition-colors mb-4">
        <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        กลับไปหน้ารายละเอียดโครงการ
    </a>
    <div class="flex justify-between items-center bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-wide">
                ขึ้นทะเบียนผลงานตีพิมพ์ <span class="text-slate-400 font-normal text-lg">(Publication Output)</span>
            </h2>
            <p class="mt-1 text-sm text-slate-550 font-medium">ระบบบริหารงานวิจัยและนวัตกรรม (RIMS) | โครงการ: <span class="text-slate-800 font-bold"><?= e($project['title_th']) ?></span></p>
        </div>
    </div>
</div>

<form action="/public/research-outputs/store" method="POST" enctype="multipart/form-data" class="space-y-6 max-w-5xl mx-auto pb-24">
    <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
    
    <!-- ส่วนที่ 1: ข้อมูลผลงานวิชาการ -->
    <div class="card p-6 relative overflow-hidden">
        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center border-b border-slate-100 pb-2">
            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            ส่วนที่ 1: ข้อมูลผลงานตีพิมพ์ (Publication Details)
        </h3>
        
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">ชื่อบทความวิจัย (ภาษาไทย) <span class="text-rose-500">*</span></label>
                    <input type="text" name="title_th" required class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="ชื่อบทความภาษาไทย">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Research Article Title (English)</label>
                    <input type="text" name="title_en" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="English Title">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">ประเภทผลงาน <span class="text-rose-500">*</span></label>
                    <select name="publication_type" required class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm appearance-none">
                        <option value="selected">-- เลือก --</option>
                        <option value="Journal">วารสารวิชาการ (Journal)</option>
                        <option value="Conference">การประชุมวิชาการ (Conference/Proceeding)</option>
                        <option value="Book">หนังสือ/ตำรา (Book/Chapter)</option>
                        <option value="Other">อื่นๆ</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">ปีที่ตีพิมพ์ (Year) <span class="text-rose-500">*</span></label>
                    <input type="text" name="publish_year" required value="<?= date('Y') ?>" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm font-bold" placeholder="2025">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">สถานะ (Status)</label>
                    <select name="status" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm appearance-none">
                        <option value="published">ตีพิมพ์แล้ว (Published)</option>
                        <option value="accepted">ตอบรับการตีพิมพ์ (Accepted)</option>
                        <option value="under_review">อยู่ระหว่างพิจารณา (Under Review)</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">ชื่อวารสาร/การประชุม <span class="text-rose-500">*</span></label>
                    <input type="text" name="journal_name" required class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="e.g. Journal of Computer Science">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">ISSN / ISBN</label>
                    <input type="text" name="issn" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="xxxx-xxxx">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Volume (ปีที่)</label>
                    <input type="text" name="volume" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Issue (ฉบับที่)</label>
                    <input type="text" name="issue" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">เลขหน้า (Page Range)</label>
                    <input type="text" name="page_length" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="10-15">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">ฐานข้อมูล (Index)</label>
                    <select name="indexing_database" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm appearance-none">
                        <option value="">-- เลือก --</option>
                        <option value="Scopus">Scopus</option>
                        <option value="WoS">Web of Science (ISI)</option>
                        <option value="TCI 1">TCI กลุ่ม 1</option>
                        <option value="TCI 2">TCI กลุ่ม 2</option>
                        <option value="Other">อื่นๆ</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Quartile</label>
                    <select name="quartile" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm appearance-none">
                        <option value="">-- เลือก --</option>
                        <option value="Q1">Q1</option>
                        <option value="Q2">Q2</option>
                        <option value="Q3">Q3</option>
                        <option value="Q4">Q4</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Impact Factor</label>
                    <input type="text" name="impact_factor" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">DOI URL / Link</label>
                <input type="url" name="doi_url" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="https://doi.org/...">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">สรุปการนำไปใช้ประโยชน์ (Utilization Summary)</label>
                <textarea name="utilization_summary" rows="3" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="อธิบายการนำผลงานไปใช้ประโยชน์..."></textarea>
            </div>
        </div>
    </div>

    <!-- ส่วนที่ 2: รายชื่อผู้แต่ง -->
    <div class="card p-6 relative overflow-hidden">
        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center border-b border-slate-100 pb-2">
            <svg class="w-5 h-5 mr-2 text-indigo-650" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            ส่วนที่ 2: รายชื่อผู้แต่ง (Authorship)
        </h3>
        
        <div id="author-container" class="space-y-4">
            <div class="flex flex-col sm:flex-row gap-4 items-end p-4 border border-slate-200 rounded-xl bg-slate-50/50">
                <div class="w-full sm:w-1/2">
                    <label class="block text-xs font-semibold text-slate-650 mb-1.5">ชื่อ-นามสกุล (Name)</label>
                    <input type="text" name="author_name[]" value="<?= e(authUser()['name']) ?>" readonly class="block w-full px-4 py-2 border border-slate-200 rounded-xl bg-slate-100 text-slate-500 cursor-not-allowed sm:text-sm font-semibold">
                </div>
                <div class="w-full sm:w-1/2">
                    <label class="block text-xs font-semibold text-slate-650 mb-1.5">บทบาท (Author Role)</label>
                    <select name="author_role[]" class="block w-full px-4 py-2 border border-slate-200 rounded-xl bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm">
                        <option value="First Author">First Author (ผู้แต่งชื่อแรก)</option>
                        <option value="Corresponding">Corresponding Author</option>
                        <option value="Co-Author">Co-Author (ผู้แต่งร่วม)</option>
                    </select>
                </div>
            </div>
        </div>
        
        <button type="button" onclick="addAuthor()" class="mt-4 px-4 py-2 border border-indigo-650/30 text-indigo-600 rounded-xl text-sm font-semibold hover:bg-indigo-50 transition-colors flex items-center shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            เพิ่มผู้แต่งร่วม (Add Co-Author)
        </button>
    </div>

    <!-- ส่วนที่ 3: ไฟล์หลักฐาน -->
    <div class="card p-6 relative overflow-hidden">
        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center border-b border-slate-100 pb-2">
            <svg class="w-5 h-5 mr-2 text-indigo-650" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
            ส่วนที่ 3: แนบไฟล์หลักฐาน (PDF)
        </h3>
        
        <div class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <label class="text-sm font-semibold text-slate-700">1. ไฟล์บทความฉบับตีพิมพ์ (Full Paper PDF) <span class="text-xs text-slate-400 ml-1">(ถ้ามี)</span></label>
                <input type="file" name="file_full_text" accept=".pdf" class="text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-650 hover:file:bg-indigo-100 border border-slate-200 bg-white p-1 cursor-pointer">
            </div>
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <label class="text-sm font-semibold text-slate-700">2. ใบตอบรับการตีพิมพ์ (Acceptance Letter) <span class="text-xs text-slate-400 ml-1">(กรณีรอดำเนินการตีพิมพ์)</span></label>
                <input type="file" name="file_acceptance" accept=".pdf" class="text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-slate-50 file:text-slate-650 hover:file:bg-slate-100 border border-slate-200 bg-white p-1 cursor-pointer">
            </div>
        </div>
    </div>

    <!-- Sticky Footer -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 p-4 z-40 shadow-lg">
        <div class="max-w-5xl mx-auto flex justify-between items-center px-4">
            <a href="/public/projects/<?= $project['id'] ?>" class="px-6 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-600 bg-white hover:bg-slate-50 transition-colors shadow-sm">
                ยกเลิก (Cancel)
            </a>
            
            <button type="submit" class="btn-primary px-8 py-2.5 rounded-xl text-sm font-bold tracking-wide flex items-center shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                บันทึกผลงาน (Save Publication)
            </button>
        </div>
    </div>
</form>

<script>
    let authorIndex = 1;
    function addAuthor() {
        const container = document.getElementById('author-container');
        const rowHtml = `
            <div class="flex flex-col sm:flex-row gap-4 items-end p-4 border border-slate-200 rounded-xl bg-slate-50/50 relative" id="author-row-${authorIndex}">
                <button type="button" onclick="removeAuthor(${authorIndex})" class="absolute top-2 right-2 text-slate-400 hover:text-rose-600 transition-colors" title="ลบผู้วิจัย">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                <div class="w-full sm:w-1/2">
                    <label class="block text-xs font-semibold text-slate-650 mb-1.5">ชื่อ-นามสกุล (Name)</label>
                    <input type="text" name="author_name[]" required class="block w-full px-4 py-2 border border-slate-200 rounded-xl bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm">
                </div>
                <div class="w-full sm:w-1/2">
                    <label class="block text-xs font-semibold text-slate-650 mb-1.5">บทบาท (Author Role)</label>
                    <select name="author_role[]" class="block w-full px-4 py-2 border border-slate-200 rounded-xl bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm">
                        <option value="Co-Author">Co-Author (ผู้แต่งร่วม)</option>
                        <option value="Corresponding">Corresponding Author</option>
                    </select>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', rowHtml);
        authorIndex++;
    }

    function removeAuthor(index) {
        document.getElementById(`author-row-${index}`).remove();
    }
</script>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php'; 
?>
