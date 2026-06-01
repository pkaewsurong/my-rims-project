<?php ob_start(); ?>

<div class="mb-8">
    <a href="/public/projects/<?= $project['id'] ?>" class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-indigo-650 transition-colors mb-4">
        <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        กลับไปหน้ารายละเอียดโครงการ
    </a>
    <div class="flex justify-between items-center bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 tracking-wide flex items-center">
                <svg class="w-6 h-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v12m0 0l-4-4m4 4l4-4m0 6a9 9 0 0118 0v-2a7 7 0 00-14 0V5a2 2 0 114 0v9a3 3 0 003 3h4a3 3 0 003-3V5a2 2 0 10-4 0v2"></path></svg>
                คลังข้อมูลวิจัย (Data Archive & Governance)
            </h2>
            <p class="mt-1 text-sm text-slate-500 font-medium">ระบบบริหารงานวิจัยและนวัตกรรม (RIMS) | โครงการ: <span class="text-slate-800 font-bold"><?= e($project['title']) ?></span></p>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto pb-24 space-y-6">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- ฝั่งซ้าย: นโยบายการจัดเก็บและชุดข้อมูลที่มี -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- นโยบายการจัดเก็บและการทำลายข้อมูล -->
            <div class="card p-6 relative overflow-hidden">
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center border-b border-slate-100 pb-3">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    นโยบายการจัดเก็บและทำลายข้อมูล (Data Retention Policy)
                </h3>
                
                <form action="/public/archives/settings" method="POST" class="space-y-4">
                    <input type="hidden" name="proposal_id" value="<?= $project['id'] ?>">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">ระยะเวลาจัดเก็บ (ปี) <span class="text-rose-500">*</span></label>
                            <select name="retention_period" class="block w-full px-4 py-2.5 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                                <option value="3" <?= ($settings['retention_period'] ?? 5) == 3 ? 'selected' : '' ?>>3 ปี (ข้อมูลทั่วไป)</option>
                                <option value="5" <?= ($settings['retention_period'] ?? 5) == 5 ? 'selected' : '' ?>>5 ปี (ข้อมูลส่วนบุคคล/สุขภาพ)</option>
                                <option value="10" <?= ($settings['retention_period'] ?? 5) == 10 ? 'selected' : '' ?>>10 ปี (ข้อมูลเชิงพาณิชย์/สิทธิบัตร)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">วิธีการทำลายข้อมูล</label>
                            <select name="destruction_method" class="block w-full px-4 py-2.5 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                                <option value="Delete physically & digitally" <?= ($settings['destruction_method'] ?? '') === 'Delete physically & digitally' ? 'selected' : '' ?>>ลบไฟล์ดิจิทัลและทำลายเอกสาร</option>
                                <option value="Anonymization (De-identification)" <?= ($settings['destruction_method'] ?? '') === 'Anonymization (De-identification)' ? 'selected' : '' ?>>ลบการระบุตัวตน (Anonymization)</option>
                            </select>
                        </div>
                    </div>
                    <?php if (true): // All users access ?>
                    <div class="text-right mt-4">
                        <button type="submit" class="px-5 py-2.5 bg-indigo-50 text-indigo-650 rounded-xl text-sm font-semibold hover:bg-indigo-100 transition-colors border border-indigo-200 shadow-sm">
                            บันทึกนโยบาย
                        </button>
                    </div>
                    <?php endif; ?>
                </form>
            </div>

            <!-- รายการชุดข้อมูล -->
            <div class="card p-6 relative overflow-hidden flex-grow">
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center border-b border-slate-100 pb-3">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    แฟ้มข้อมูลของโครงการ (Datasets)
                </h3>

                <?php if (count($archives) > 0): ?>
                    <div class="space-y-4">
                        <?php foreach($archives as $archive): ?>
                            <div class="bg-slate-50 border border-slate-200 p-5 rounded-2xl hover:border-indigo-300 transition-all shadow-sm">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-bold text-slate-800 text-md flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <?= e($archive['dataset_name']) ?>
                                    </h4>
                                    <?php 
                                        $accessColors = [
                                            'public' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'restricted' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'private' => 'bg-rose-50 text-rose-700 border-rose-200'
                                        ];
                                        $accessClass = $accessColors[$archive['access_level']] ?? 'bg-slate-50 text-slate-500 border-slate-200';
                                    ?>
                                    <span class="px-2.5 py-0.5 text-xs font-bold rounded-full border <?= $accessClass ?>">
                                        <?= ucfirst(e($archive['access_level'])) ?>
                                    </span>
                                </div>
                                <p class="text-sm text-slate-600 mb-3 line-clamp-2"><?= e($archive['description']) ?></p>
                                
                                <div class="flex items-center justify-between text-xs text-slate-400 border-t border-slate-200/60 pt-3">
                                    <div class="space-x-4">
                                        <span class="font-medium">ประเภท: <span class="text-slate-700 font-bold"><?= e($archive['data_type']) ?></span></span>
                                        <span class="font-medium">ขนาด: <span class="text-slate-700 font-bold"><?= number_format($archive['file_size'] / 1024 / 1024, 2) ?> MB</span></span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span class="font-medium"><?= date('d M Y', strtotime($archive['created_at'])) ?></span>
                                        <?php if ($archive['file_path']): ?>
                                            <a href="/public/uploads/archives/<?= e($archive['file_path']) ?>" download class="text-indigo-600 hover:text-indigo-800 transition-colors flex items-center font-bold">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                ดาวน์โหลด
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-slate-50 border border-dashed border-slate-200 rounded-2xl p-10 text-center">
                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        <p class="text-slate-500 font-semibold">ยังไม่มีชุดข้อมูลที่จัดเก็บในคลัง (Repository)</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ฝั่งขวา: ฟอร์มอัปโหลดชุดข้อมูล -->
        <div class="lg:col-span-1">
            <div class="card p-6 relative overflow-hidden h-full">
                <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center border-b border-slate-100 pb-3">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    นำข้อมูลเข้าคลัง (Upload Dataset)
                </h3>

                <?php if (true): // All users access ?>
                <form action="/public/archives/store" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="proposal_id" value="<?= $project['id'] ?>">
                    
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">ชื่อชุดข้อมูล (Dataset Name) <span class="text-rose-500">*</span></label>
                        <input type="text" name="dataset_name" required class="block w-full px-4 py-2.5 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">ประเภทข้อมูล <span class="text-rose-500">*</span></label>
                        <select name="data_type" class="block w-full px-4 py-2.5 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                            <option value="Raw Data">ข้อมูลดิบ (Raw Data)</option>
                            <option value="Processed Data">ข้อมูลที่ประมวลผลแล้ว (Processed Data)</option>
                            <option value="Source Code / Scripts">ซอร์สโค้ด / สคริปต์ (Source Code)</option>
                            <option value="Audio/Video/Images">สื่อมัลติมีเดีย (Media)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">ระดับการเข้าถึงข้อมูล (Access Level) <span class="text-rose-500">*</span></label>
                        <select name="access_level" class="block w-full px-4 py-2.5 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm">
                            <option value="private">ความลับ/ส่วนบุคคล (Private)</option>
                            <option value="restricted">จำกัดเฉพาะผู้ร้องขอ (Restricted Request)</option>
                            <option value="public">เปิดเผยเป็นสาธารณะ (Open Data)</option>
                        </select>
                        <p class="text-[10px] text-slate-450 mt-1 font-medium">* ให้พิจารณาตามพ.ร.บ.คุ้มครองข้อมูลส่วนบุคคล (PDPA)</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">คำอธิบาย/เมทาดาตาโดยย่อ</label>
                        <textarea name="description" rows="3" class="block w-full px-4 py-2.5 border border-slate-205 rounded-xl bg-slate-50 text-slate-855 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:bg-white transition-colors sm:text-sm resize-y" placeholder="ระบุตัวแปรสำคัญ หรือวิธีเก็บข้อมูล..."></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">อัปโหลดไฟล์ (.zip, .csv, .xlsx)</label>
                        <input type="file" name="file_path" required class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-650 hover:file:bg-indigo-100 border border-slate-200 bg-white p-1 cursor-pointer">
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full btn-primary px-4 py-3 rounded-xl text-sm font-bold tracking-wide flex justify-center items-center shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            อัปโหลดเข้าคลัง
                        </button>
                    </div>
                </form>
                <?php else: ?>
                    <p class="text-sm text-slate-450 italic text-center mt-10 font-semibold">เฉพาะนักวิจัยในโครงการเท่านั้นที่สามารถอัปโหลดข้อมูลได้</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php'; 
?>
