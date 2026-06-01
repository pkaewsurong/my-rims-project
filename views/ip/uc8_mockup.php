<?php ob_start(); ?>

<div class="mb-8">
    <a href="/public/projects/<?= $project['id'] ?>" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-indigo-650 transition-colors mb-4">
        <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        กลับไปหน้ารายละเอียดโครงการ
    </a>
    <div class="flex justify-between items-center bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-wide flex items-center">
                <svg class="w-6 h-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <span class="mx-3 w-px h-6 bg-slate-200"></span>
                ลงทะเบียนทรัพย์สินทางปัญญา/ผลงานสร้างสรรค์
            </h2>
            <p class="mt-2 text-sm text-slate-500 flex items-center font-medium">
                ระบบบริหารงานวิจัยและนวัตกรรม (RIMS) | ผู้ใช้: <span class="text-slate-800 font-bold ml-1">ADMINISTRATOR</span>
            </p>
        </div>
    </div>
</div>

<form action="/public/ip-assets/store" method="POST" enctype="multipart/form-data" class="space-y-6 max-w-5xl mx-auto pb-24">
    <input type="hidden" name="project_id" value="<?= e($project['id']) ?>">
    
    <!-- 1. ข้อมูลหลักและประเภทผลงาน -->
    <div class="card p-6 sm:p-8 relative overflow-hidden">
        <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center border-b border-slate-100 pb-4">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-655 mr-3 text-sm border border-indigo-100 font-bold">1</span>
            ข้อมูลหลักและประเภทผลงาน
        </h3>
        
        <div class="space-y-6">
            <!-- Work Names -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">ชื่อผลงาน (ภาษาไทย) <span class="text-rose-500">*</span></label>
                    <input type="text" name="name_th" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="เช่น เครื่องมือตรวจวัดระดับความหวาน..." required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">ชื่อผลงาน (ภาษาอังกฤษ)</label>
                    <input type="text" name="name_en" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="e.g. Sweetness Measurement Device">
                </div>
            </div>

            <!-- Abstract -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">บทสรุปผลงาน/สิ่งประดิษฐ์</label>
                <textarea name="abstract_details" rows="4" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm resize-y" placeholder="อธิบายการทำงานหรือจุดเด่นของผลงานโดยย่อ..."></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Project Ref -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">โครงการวิจัยที่เกี่ยวข้อง (ถ้ามี)</label>
                    <select class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-100 text-slate-500 cursor-not-allowed sm:text-sm appearance-none" disabled>
                        <option selected><?= e($project['title']) ?></option>
                    </select>
                </div>
                <!-- Creation Date -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">วันที่ประดิษฐ์/สร้างสรรค์</label>
                    <input type="date" name="completion_date" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                </div>
                <!-- Field/Keyword -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">สาขาวิชา/ความเชี่ยวชาญ (Keywords)</label>
                    <input type="text" name="keywords" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="เช่น IoT, Material Science, AI">
                </div>
            </div>

            <!-- IP Types Section -->
            <div class="mt-8 pt-6 border-t border-slate-100">
                <label class="block text-sm font-bold text-slate-800 mb-4">ประเภทการขอรับความคุ้มครอง (IP Type) <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Option 1 -->
                    <label class="relative flex flex-col p-5 cursor-pointer rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100/70 hover:border-indigo-300 transition-all group">
                        <div class="flex items-center space-x-3 mb-2">
                            <input type="radio" name="ip_type" value="Patent" class="form-radio h-5 w-5 text-indigo-650 border-slate-300 focus:ring-indigo-500 bg-transparent" checked>
                            <span class="text-slate-800 font-semibold text-sm group-hover:text-indigo-600 transition-colors">สิทธิบัตร/อนุสิทธิบัตร</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 pl-8">การประดิษฐ์ที่ขึ้นงานใหม่, กรรมวิธี, หรือการปรับปรุงย่อประดิษฐ์</p>
                    </label>

                    <!-- Option 2 -->
                    <label class="relative flex flex-col p-5 cursor-pointer rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100/70 hover:border-indigo-300 transition-all group">
                        <div class="flex items-center space-x-3 mb-2">
                            <input type="radio" name="ip_type" value="Copyright" class="form-radio h-5 w-5 text-indigo-650 border-slate-300 focus:ring-indigo-500 bg-transparent">
                            <span class="text-slate-800 font-semibold text-sm group-hover:text-indigo-600 transition-colors">ลิขสิทธิ์ (Copyright)</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 pl-8">งานประพันธ์ วรรณกรรม ซอฟต์แวร์ หนังสือ งานศิลปะ ศิลปกรรม</p>
                    </label>

                    <!-- Option 3 -->
                    <label class="relative flex flex-col p-5 cursor-pointer rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100/70 hover:border-indigo-300 transition-all group">
                        <div class="flex items-center space-x-3 mb-2">
                            <input type="radio" name="ip_type" value="Creative" class="form-radio h-5 w-5 text-indigo-650 border-slate-300 focus:ring-indigo-500 bg-transparent">
                            <span class="text-slate-800 font-semibold text-sm group-hover:text-indigo-600 transition-colors">ผลงานสร้างสรรค์/อื่นๆ</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 pl-8">สิ่งประดิษฐ์ที่ยังไม่ได้ยื่นจดทะเบียน นวัตกรรมชุมชน ต้นแบบ</p>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. สถานะทางกฎหมายและการนำไปใช้ -->
    <div class="card p-6 sm:p-8 relative overflow-hidden">
        <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center border-b border-slate-100 pb-4">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-655 mr-3 text-sm border border-indigo-100 font-bold">2</span>
            สถานะทางกฎหมายและการนำไปใช้ (Legal & Impact)
        </h3>
        
        <div class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Status Dropdown -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">สถานะการดำเนินการจดทะเบียน</label>
                    <select name="legal_status" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm appearance-none cursor-pointer">
                        <option value="">-- เลือก --</option>
                        <option value="Not Filed">ยังไม่ยื่น</option>
                        <option value="Pending">อยู่ระหว่างยื่นคำขอ (Pending)</option>
                        <option value="Granted">ได้รับการจดทะเบียนแล้ว (Granted/Registered)</option>
                    </select>
                </div>
                <!-- Reg No -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">เลขที่คำขอ / เลขที่จดทะเบียน</label>
                    <input type="text" name="registration_number" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="เช่น 240100XXXX">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Agency -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">หน่วยงานที่รับจดทะเบียน</label>
                    <input type="text" name="registration_agency" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm" placeholder="เช่น กรมทรัพย์สินทางปัญญา / Copyright Office">
                </div>
                <!-- Reg Date -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">วันที่ได้รับอนุมัติ (ถ้ามี)</label>
                    <input type="date" name="approval_date" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                </div>
            </div>
        </div>
    </div>

    <!-- 3. การนำไปใช้ประโยชน์/ผลกระทบ -->
    <div class="card p-6 sm:p-8 relative overflow-hidden">
        <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center border-b border-slate-100 pb-4">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-655 mr-3 text-sm border border-indigo-100 font-bold">3</span>
            การนำไปใช้ประโยชน์/ผลกระทบ (Impact)
        </h3>
        
        <div class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">สถานะการนำไปใช้ประโยชน์เชิงพาณิชย์</label>
                    <select name="commercial_status" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm appearance-none cursor-pointer">
                        <option value="">-- เลือก --</option>
                        <option value="Not Used">ยังไม่ได้นำไปใช้</option>
                        <option value="Licensed">ถ่ายทอดเทคโนโลยี/อนุญาตให้ใช้สิทธิแล้ว (License Out)</option>
                        <option value="Pilot">อยู่ระหว่างการทดลองใช้ (Pilot Phase)</option>
                        <option value="Used">นำไปใช้ประโยชน์แล้ว</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">มูลค่าทางเศรษฐกิจ/ประเมิน (บาท)</label>
                    <input type="text" name="economic_value" id="economic_value" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm font-mono" placeholder="0.00" oninput="formatNumber(this)">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">คำอธิบายการนำไปใช้ประโยชน์/ผลกระทบ</label>
                <textarea name="impact_description" rows="3" class="block w-full px-4 py-3 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm resize-y" placeholder="อธิบายการนำไปใช้ สร้างรายได้ หรือทำประโยชน์ให้กับสังคม/ผู้ใช้งานอย่างไรบ้าง"></textarea>
            </div>
        </div>
    </div>

    <!-- 4. เอกสารหลักฐานและผู้ประดิษฐ์ -->
    <div class="card p-6 sm:p-8 relative overflow-hidden">
        <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center border-b border-slate-100 pb-4">
            <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-655 mr-3 text-sm border border-indigo-100 font-bold">4</span>
            รายชื่อผู้สร้างสรรค์ / เจ้าของผลงาน และ เอกสารหลักฐาน
        </h3>

        <div class="space-y-8">
            <!-- Inventors Section -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <label class="block text-sm font-semibold text-slate-700">รายชื่อผู้สร้างสรรค์/ผู้ประดิษฐ์ (Inventors/Creators) <span class="text-rose-500">*</span></label>
                    <span class="text-xs bg-slate-100 border border-slate-200 px-3 py-1 rounded-full text-slate-500 font-medium">สัดส่วนรวมต้องเท่ากับ 100%</span>
                </div>
                
                <div id="inventors-container" class="space-y-3">
                    <!-- PI (Project Owner) -->
                    <div class="flex items-center justify-between bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center font-bold text-xs">1</div>
                            <span class="text-slate-800 text-sm font-semibold"><?= e($project['pi_name']) ?> (หัวหน้าโครงการ)</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <input type="hidden" name="inventor_name[]" value="<?= e($project['pi_name']) ?>">
                            <span class="text-sm text-slate-500 font-medium">สัดส่วน</span>
                            <div class="relative">
                                <input type="number" name="inventor_proportion[]" value="100" class="w-20 bg-white border border-slate-200 rounded-lg py-1 px-2 text-slate-855 text-sm text-right focus:border-indigo-500 focus:outline-none font-bold">
                                <span class="absolute right-2 top-1.5 text-slate-400 font-medium">%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Team Members -->
                    <?php 
                    $counter = 2;
                    if (!empty($teams)): 
                        foreach ($teams as $idx => $team): 
                    ?>
                    <div class="flex items-center justify-between bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-slate-200 text-slate-650 rounded-full flex items-center justify-center font-bold text-xs"><?= $counter++ ?></div>
                            <span class="text-slate-800 text-sm font-semibold"><?= e($team['name']) ?> (<?= e($team['role']) ?>)</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <input type="hidden" name="inventor_name[]" value="<?= e($team['name']) ?>">
                            <span class="text-sm text-slate-500 font-medium">สัดส่วน</span>
                            <div class="relative">
                                <input type="number" name="inventor_proportion[]" value="<?= e($team['proportion']) ?>" class="w-20 bg-white border border-slate-200 rounded-lg py-1 px-2 text-slate-855 text-sm text-right focus:border-indigo-500 focus:outline-none font-bold">
                                <span class="absolute right-2 top-1.5 text-slate-400 font-medium">%</span>
                            </div>
                            <button class="text-slate-400 hover:text-rose-600 p-1 transition-colors" type="button" onclick="this.closest('.flex.items-center.justify-between').remove(); updateInventorNumbers();"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                        </div>
                    </div>
                    <?php 
                        endforeach;
                    endif; 
                    ?>

                    <!-- Add Inventor Row -->
                    <div class="flex items-center space-x-3 mt-4">
                        <input type="text" name="inventor_name[]" class="flex-grow px-4 py-2 border border-slate-200 rounded-lg bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm" placeholder="ค้นหาชื่อนักวิจัย/พนักงาน...">
                        <input type="number" name="inventor_proportion[]" class="w-24 px-4 py-2 border border-slate-200 rounded-lg bg-white text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm" placeholder="สัดส่วน %">
                        <button type="button" onclick="addNewInventorRow()" class="px-4 py-2 border border-indigo-200 text-indigo-600 rounded-lg bg-indigo-50/50 hover:bg-indigo-100 hover:text-indigo-700 transition-colors text-sm font-semibold flex items-center whitespace-nowrap shadow-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            เพิ่มผู้ประดิษฐ์
                        </button>
                    </div>
                </div>
            </div>

            <!-- Documents Section -->
            <div class="mt-8 pt-6 border-t border-slate-100 space-y-6">
                <h4 class="text-md font-bold text-slate-805 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    เอกสารหลักฐานประกอบ
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Doc 1 -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 hover:border-slate-300 transition-colors">
                        <label class="block text-sm font-semibold text-slate-800 mb-1">ไฟล์แบบแสดงรายละเอียดผลงาน (Disclosure)</label>
                        <p class="text-xs text-slate-500 mb-3 font-medium">ไฟล์ที่อธิบายลักษณะเด่นของผลงาน</p>
                        <div class="flex items-center relative">
                            <input type="file" name="file_submission" class="block w-full text-sm text-slate-500 file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-650 hover:file:bg-indigo-100 file:cursor-pointer p-0 bg-transparent cursor-pointer">
                        </div>
                    </div>
                    
                    <!-- Doc 2 -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 hover:border-slate-300 transition-colors">
                        <label class="block text-sm font-semibold text-slate-800 mb-1">เอกสารคำขอ/หนังสือรับรอง (ถ้ามี)</label>
                        <p class="text-xs text-slate-500 mb-3 font-medium">หมายเลขคำขอ กรมทรัพย์สินทางปัญญา ฯลฯ</p>
                        <div class="flex items-center relative">
                            <input type="file" name="file_certificate" class="block w-full text-sm text-gray-400 file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-650 hover:file:bg-indigo-100 file:cursor-pointer p-0 bg-transparent cursor-pointer">
                        </div>
                    </div>
                    
                    <!-- Doc 3 -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 hover:border-slate-300 transition-colors md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-800 mb-1">ไฟล์สัญญา Source Code, แผนผังวงจร (ถ้าเกี่ยวข้อง) <span class="text-rose-500">*</span></label>
                        <p class="text-xs text-slate-500 mb-3 font-medium">สำหรับลิขสิทธิ์ซอฟต์แวร์ กรุณาแนบไฟล์ต้นฉบับบางส่วน หรือผังการทำงาน</p>
                        <div class="flex items-center relative border-2 border-dashed border-slate-200 rounded-xl p-6 bg-white justify-center hover:border-indigo-300 transition-colors group cursor-pointer">
                            <div class="text-center group-hover:scale-105 transition-transform">
                                <svg class="w-8 h-8 mx-auto text-slate-400 mb-2 group-hover:text-indigo-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                <span class="text-sm text-slate-500 font-semibold">คลิกเพื่อเลือกไฟล์ หรือลากไฟล์มาวางที่นี่</span>
                            </div>
                            <input type="file" name="file_evidence" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sticky Footer Actions -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 p-4 z-40 shadow-lg">
        <div class="max-w-5xl mx-auto flex justify-between items-center px-4">
            <a href="/public/projects/<?= $project['id'] ?>" class="px-6 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-650 bg-white hover:bg-slate-50 transition-colors">
                ยกเลิก (Cancel)
            </a>
            
            <div class="flex space-x-3">
                <button type="button" class="px-6 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-650 bg-white hover:bg-slate-50 transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2 text-slate-550" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    บันทึกแบบร่าง
                </button>
                <button type="submit" class="btn-primary px-8 py-2.5 rounded-xl text-sm font-bold tracking-wide flex items-center shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    บันทึกข้อมูลและส่งตรวจสอบ
                </button>
            </div>
        </div>
    </div>
</form>

<script>
function formatNumber(input) {
    let value = input.value.replace(/[^0-9.]/g, '');
    if (value !== '') {
        let parts = value.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        input.value = parts.slice(0, 2).join('.');
    } else {
        input.value = '';
    }
}

function updateInventorNumbers() {
    const container = document.getElementById('inventors-container');
    const inventors = container.querySelectorAll('.flex.items-center.justify-between');
    inventors.forEach((el, index) => {
        const numberBadge = el.querySelector('.w-8.h-8');
        if (numberBadge) {
            numberBadge.innerText = index + 1;
        }
    });
}

function addNewInventorRow() {
    const container = document.getElementById('inventors-container');
    const nameInput = document.querySelector('div.flex.items-center.space-x-3.mt-4 input[placeholder*="ค้นหาชื่อ"]');
    const proportionInput = document.querySelector('div.flex.items-center.space-x-3.mt-4 input[placeholder*="สัดส่วน"]');
    
    if (!nameInput.value) {
        alert('กรุณากรอกชื่อผู้ประดิษฐ์');
        nameInput.focus();
        return;
    }
    
    const name = nameInput.value;
    const proportion = proportionInput.value || 0;
    
    const newRow = document.createElement('div');
    newRow.className = 'flex items-center justify-between bg-slate-50 p-4 rounded-xl border border-slate-200';
    newRow.innerHTML = `
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-slate-200 text-slate-650 rounded-full flex items-center justify-center font-bold text-xs"></div>
            <span class="text-slate-800 text-sm font-semibold">${name}</span>
        </div>
        <div class="flex items-center space-x-3">
            <input type="hidden" name="inventor_name[]" value="${name}">
            <span class="text-sm text-slate-500 font-medium">สัดส่วน</span>
            <div class="relative">
                <input type="number" name="inventor_proportion[]" value="${proportion}" class="w-20 bg-white border border-slate-200 rounded-lg py-1 px-2 text-slate-855 text-sm text-right focus:border-indigo-500 focus:outline-none font-bold">
                <span class="absolute right-2 top-1.5 text-slate-400 font-medium">%</span>
            </div>
            <button class="text-slate-400 hover:text-rose-600 p-1 transition-colors" type="button" onclick="this.closest('.flex.items-center.justify-between').remove(); updateInventorNumbers();">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    `;
    
    container.appendChild(newRow);
    updateInventorNumbers();
    
    nameInput.value = '';
    proportionInput.value = '';
}
</script>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php'; 
?>
