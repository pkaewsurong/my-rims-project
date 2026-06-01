<?php 
ob_start(); 
$isReadonly = isset($_GET['readonly']) && $_GET['readonly'] == 1;
?>

<div class="mb-6 flex justify-between items-center bg-white border border-slate-200 rounded-2xl p-4 shadow-sm">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-800 tracking-wide">
            รายละเอียดโครงการ <span class="text-indigo-600 font-mono">#<?= e($project['code']) ?></span>
        </h2>
    </div>
    <div class="text-right flex items-center gap-3">
        <?php if ($isReadonly && hasRole('admin') && ($project['closure_requested'] ?? false)): ?>
        <button onclick="handleApproveClosure(<?= e($project['id']) ?>)" 
           class="px-4 py-2 bg-rose-600 text-white text-sm font-semibold rounded-xl hover:bg-rose-700 transition-all flex items-center shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            อนุมัติปิดโครงการ
        </button>
        <?php endif; ?>

        <?php if ($isReadonly): ?>
        <button onclick="history.back()" class="px-4 py-2 bg-slate-50 hover:bg-slate-105 text-slate-600 rounded-xl text-sm transition-colors border border-slate-200 inline-flex items-center font-medium shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            ย้อนกลับ
        </button>
        <?php else: ?>
        <a href="/public/projects" class="px-4 py-2 bg-slate-50 hover:bg-slate-105 text-slate-600 rounded-xl text-sm transition-colors border border-slate-200 inline-flex items-center font-medium shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            กลับหน้ารวมโครงการ
        </a>
        <?php endif; ?>
    </div>
</div>

<div class="space-y-6">
    <!-- Header Info -->
    <div class="card p-6 relative overflow-hidden">
        <div class="mb-6 flex justify-between items-start">
            <div>
                <h3 class="text-2xl font-bold text-slate-800 mb-2"><?= e($project['title']) ?></h3>
                <p class="text-sm text-slate-555 font-medium">หัวหน้าโครงการ: <span class="text-slate-800 font-semibold"><?= e($project['leader_name']) ?></span></p>
            </div>
            <?php
            $statusColors = [
                'pending' => 'bg-amber-50 text-amber-600 border-amber-200',
                'approved' => 'bg-emerald-50 text-emerald-600 border-emerald-250',
                'ongoing' => 'bg-indigo-50 text-indigo-600 border-indigo-200',
                'completed' => 'bg-indigo-50 text-indigo-700 border-indigo-250',
                'closed' => 'bg-rose-50 text-rose-650 border-rose-200',
                'rejected' => 'bg-rose-50 text-rose-650 border-rose-200'
            ];
            $colorClass = $statusColors[strtolower($project['status'])] ?? 'bg-slate-100 text-slate-500 border-slate-200';
            ?>
            <div class="flex flex-col items-end space-y-2">
                <span class="px-3 py-1 text-sm font-bold rounded-full border <?= $colorClass ?>">
                    <?= ucfirst(e($project['status'])) ?>
                </span>
                
                <?php if (!$isReadonly): ?>
                <a href="/public/uc8/mockup?project_id=<?= e($project['id']) ?>" class="px-3 py-1.5 bg-indigo-50 text-indigo-600 border border-indigo-150 text-xs font-semibold rounded-lg hover:bg-indigo-100 transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    ลงทะเบียนทรัพย์สินทางปัญญา
                </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-slate-50 border border-slate-200/60 p-4 rounded-xl">
                <h4 class="font-bold text-slate-700 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    รายละเอียดเวลา
                </h4>
                <p class="text-slate-600 text-sm mb-2"><strong class="text-slate-700 inline-block w-16 font-semibold">เริ่มต้น:</strong> <?= $project['start_date'] ? date('d M Y', strtotime($project['start_date'])) : '-' ?></p>
                <p class="text-slate-600 text-sm"><strong class="text-slate-700 inline-block w-16 font-semibold">สิ้นสุด:</strong> <?= $project['end_date'] ? date('d M Y', strtotime($project['end_date'])) : '-' ?></p>
            </div>
            <div class="bg-slate-50 border border-slate-200/60 p-4 rounded-xl">
                <h4 class="font-bold text-slate-700 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    งบประมาณ
                </h4>
                <p class="text-slate-600 text-sm mb-2"><strong class="text-slate-700 inline-block w-16 font-semibold">รวม:</strong> <span class="text-emerald-600 font-bold"><?= number_format($project['budget_total'] ?? 0, 2) ?> บาท</span></p>
                <p class="text-slate-600 text-sm"><strong class="text-slate-700 inline-block w-16 font-semibold">แหล่งทุน:</strong> <?= e($project['funding_source_name'] ?? '-') ?></p>
            </div>
        </div>
    </div>

    <!-- Progress Reports -->
    <div class="card p-6">
        <div class="flex justify-between items-center mb-6">
            <h4 class="text-lg font-bold text-slate-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-650" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                รายงานความก้าวหน้า (Progress Reports)
            </h4>
            <?php if (!$isReadonly): ?>
            <div class="flex space-x-2">
                <a href="/public/progress-reports/create?project_id=<?= e($project['id']) ?>" class="px-3 py-1.5 bg-indigo-50 text-indigo-600 border border-indigo-150 text-sm font-semibold rounded-xl hover:bg-indigo-100 transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    ส่งรายงานความก้าวหน้า
                </a>
                <?php 
                $progressReady = ($project['total_progress'] ?? 0) >= 100;
                $reportStatus = $project['final_report_status'] ?? '';
                $reportAlreadySent = in_array($reportStatus, ['submitted', 'approved']);
                ?>
                <?php 
                $onclickAttr = '';
                if (!$progressReady) {
                    $alertMsg = 'รายงานยังไม่สมบูรณ์ ไม่สามารถส่งรายงานฉบับสมบูรณ์ได้<br><span class=\"text-xs text-amber-600 mt-2 block\">ความก้าวหน้าปัจจุบัน: ' . ($project['total_progress'] ?? 0) . '% (ต้องครบ 100%)</span>';
                    $onclickAttr = "event.preventDefault(); showThemeAlert('" . addslashes($alertMsg) . "');";
                }
                $finalBtnClass = $progressReady 
                    ? 'bg-emerald-50 text-emerald-600 border-emerald-200 hover:bg-emerald-100' 
                    : 'bg-slate-50 text-slate-400 border-slate-200 cursor-not-allowed opacity-60';
                $finalBtnTitle = !$progressReady ? 'รายงานความก้าวหน้ายังไม่ครบ 100%' : ($reportAlreadySent ? 'ส่งรายงานแล้ว' : 'ส่งรายงานฉบับสมบูรณ์');
                ?>
                <a href="/public/reports/final/create?id=<?= e($project['id']) ?>" 
                   <?= $onclickAttr ? 'onclick="' . htmlspecialchars($onclickAttr, ENT_QUOTES) . '"' : '' ?>
                   class="px-3 py-1.5 <?= $finalBtnClass ?> text-sm font-semibold rounded-xl transition-colors flex items-center border" 
                   title="<?= e($finalBtnTitle) ?>">
                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <?= $reportAlreadySent ? 'รายงานฉบับสมบูรณ์ (ส่งแล้ว)' : 'ส่งรายงานฉบับสมบูรณ์' ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
        <?php if(count($progressReports) > 0): ?>
            <div class="space-y-4">
                <?php foreach($progressReports as $report): ?>
                    <div class="bg-slate-50 border border-slate-150 p-4 rounded-xl flex justify-between items-center group hover:border-indigo-350 transition-colors">
                        <div class="flex-grow pr-4">
                            <p class="font-bold text-slate-800 mb-2">
                                งวด: <?= e($report['report_period']) ?> 
                                <span class="ml-2 text-xs text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-150 font-bold"><?= e($report['percentage_complete']) ?>%</span>
                            </p>
                            <p class="text-sm text-slate-500"><?= htmlspecialchars(substr($report['summary_text'] ?? '', 0, 100)) ?><?= strlen($report['summary_text'] ?? '') > 100 ? '...' : '' ?></p>
                        </div>
                        <div class="text-right flex flex-col items-end space-y-2">
                            <span class="text-xs text-slate-400 font-medium"><?= date('d M Y', strtotime($report['created_at'])) ?></span>
                            <div class="flex items-center space-x-3">
                                <button type="button" onclick="openReportModal(<?= $report['id'] ?>)" class="text-xs text-indigo-650 hover:text-indigo-850 flex items-center transition-colors px-2 py-1 rounded bg-indigo-50 border border-indigo-100 hover:bg-indigo-100 font-semibold">
                                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    รายละเอียด
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bg-slate-50 border border-dashed border-slate-200 rounded-xl p-8 text-center">
                <svg class="w-8 h-8 text-slate-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <p class="text-slate-400 italic">ยังไม่มีรายงานความก้าวหน้า</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Research Outputs & IP -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Outputs -->
        <div class="card p-6">
            <div class="flex justify-between items-center mb-6">
                <h4 class="text-lg font-bold text-slate-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-505" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                    ผลงานตีพิมพ์ (Outputs)
                </h4>
                <?php if (!$isReadonly): ?>
                <a href="/public/research-outputs/create?project_id=<?= e($project['id']) ?>" class="px-3 py-1.5 bg-indigo-50 text-indigo-650 border border-indigo-150 text-sm font-semibold rounded-xl hover:bg-indigo-100 transition-colors flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    เพิ่ม
                </a>
                <?php endif; ?>
            </div>
            <?php if(count($outputs) > 0): ?>
                <ul class="space-y-3">
                    <?php foreach($outputs as $output): ?>
                        <li class="bg-slate-50 border border-slate-205 p-3 rounded-xl flex flex-col hover:bg-slate-100 transition-colors">
                            <span class="font-bold text-slate-800 block mb-2"><?= e($output['title_th']) ?></span>
                            <div class="flex items-center">
                                <span class="inline-block text-xs text-indigo-650 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-150 font-bold"><?= e($output['publication_type']) ?></span>
                                <span class="text-xs text-slate-400 font-semibold ml-2"><?= e($output['publish_year']) ?></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="bg-slate-50 border border-dashed border-slate-200 rounded-xl p-6 text-center">
                    <p class="text-slate-400 italic text-sm py-2">ยังไม่มีข้อมูลผลงาน</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- IP Assets -->
        <div class="card p-6">
            <div class="flex justify-between items-center mb-6">
                <h4 class="text-lg font-bold text-slate-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-505" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                    ทรัพย์สินทางปัญญา (IP)
                </h4>
                <?php if (!$isReadonly): ?>
                <a href="/public/ip-assets/create?project_id=<?= e($project['id']) ?>" class="px-3 py-1.5 bg-indigo-50 text-indigo-650 border border-indigo-150 text-sm font-semibold rounded-xl hover:bg-indigo-100 transition-colors flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    เพิ่ม
                </a>
                <?php endif; ?>
            </div>
            <?php if(count($ipAssets) > 0): ?>
                <ul class="space-y-3">
                    <?php foreach($ipAssets as $ip): ?>
                        <li class="bg-slate-50 border border-slate-205 p-3 rounded-xl flex flex-col hover:bg-slate-100 transition-colors">
                            <span class="font-bold text-slate-800 block mb-2"><?= e($ip['name_th'] ?? $ip['registration_number']) ?></span>
                            <div class="flex items-center">
                                <span class="inline-block text-xs text-indigo-650 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-150 font-bold"><?= e($ip['ip_type']) ?></span>
                                <span class="text-xs text-slate-400 font-semibold ml-2"><?= e($ip['legal_status'] ?? 'Registered') ?></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="bg-slate-50 border border-dashed border-slate-200 rounded-xl p-6 text-center">
                    <p class="text-slate-400 italic text-sm py-2">ยังไม่มีข้อมูลทรัพย์สินทางปัญญา</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Files -->
    <div class="card p-6 mb-6">
        <h4 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
            ไฟล์เอกสารโครงการ (Project Files)
        </h4>
        
        <!-- Upload Form -->
        <?php if (!$isReadonly): ?>
        <form action="/public/project-files/store" method="POST" class="mb-8 bg-slate-50 border border-slate-200 p-5 rounded-xl flex flex-col md:flex-row items-end gap-4" enctype="multipart/form-data">
            <input type="hidden" name="project_id" value="<?= e($project['id']) ?>">
            <div class="flex-grow w-full md:w-auto">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">ชื่อไฟล์/คำอธิบาย</label>
                <input type="text" name="file_type" class="block w-full px-4 py-2 bg-white border border-slate-200 rounded-lg text-slate-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm" required placeholder="เช่น สัญญาฉบับสมบูรณ์, แบบฟอร์มขอจริยธรรม">
            </div>
            <div class="flex-grow w-full md:w-auto">
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">เลือกไฟล์</label>
                <input type="file" name="file_path" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-650 hover:file:bg-indigo-100 border border-slate-200 rounded-lg p-1 bg-white cursor-pointer" required>
            </div>
            <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition-colors flex items-center justify-center shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                อัปโหลดไฟล์
            </button>
        </form>
        <?php endif; ?>

        <!-- File List -->
        <?php 
        $stmt = $pdo->prepare('SELECT * FROM project_files WHERE project_id = ? ORDER BY upload_date DESC');
        $stmt->execute([$project['id']]);
        $files = $stmt->fetchAll();
        ?>
        <?php if(count($files) > 0): ?>
            <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 bg-slate-50">
                            <th class="px-5 py-4 font-semibold">ชื่อไฟล์</th>
                            <th class="px-5 py-4 font-semibold">วันที่อัปโหลด</th>
                            <th class="px-5 py-4 text-right font-semibold">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-105">
                        <?php foreach($files as $file): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-4">
                                <a href="/public/<?= e($file['file_path']) ?>" target="_blank" class="text-indigo-600 hover:text-indigo-850 font-bold flex items-center w-max">
                                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    <?= e($file['file_type']) ?>
                                </a>
                            </td>
                            <td class="px-5 py-4 text-sm text-slate-500"><?= date('d M Y H:i', strtotime($file['upload_date'])) ?></td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <button type="button" onclick="handleDownload('<?= e($file['file_type']) ?>', '<?= e($file['file_path']) ?>')" class="inline-block text-emerald-600 hover:text-emerald-700 px-3 py-1.5 bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-bold transition-colors mr-2">ดาวน์โหลด</button>
                                <?php if (!$isReadonly): ?>
                                <form action="/public/project-files/<?= e($file['id']) ?>/destroy" method="POST" class="inline" onsubmit="return confirm('ยืนยันการลบไฟล์?');">
                                    <button type="submit" class="text-rose-600 hover:text-rose-700 px-3 py-1.5 bg-rose-50 border border-rose-200 rounded-lg text-xs font-bold transition-colors">ลบไฟล์</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="bg-slate-50 border border-dashed border-slate-200 rounded-xl p-10 text-center">
                <svg class="w-10 h-10 text-slate-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <p class="text-slate-500 text-sm">ยังไม่มีไฟล์เอกสารแนบ</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if(count($progressReports) > 0): ?>
    <?php foreach($progressReports as $report): ?>
        <!-- Modal for Progress Report Details -->
        <div id="reportModal_<?= $report['id'] ?>" class="fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-200">
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeReportModal(<?= $report['id'] ?>)"></div>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="relative bg-white rounded-2xl border border-slate-200 text-left overflow-hidden shadow-xl transform transition-all sm:max-w-xl sm:w-full scale-95 opacity-0 duration-200" id="reportModalContent_<?= $report['id'] ?>">
                    <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                        <h3 class="text-lg leading-6 font-bold text-slate-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            รายละเอียดรายงานความก้าวหน้า
                        </h3>
                        <button type="button" class="text-slate-400 hover:text-slate-650 transition-colors" onclick="closeReportModal(<?= $report['id'] ?>)">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div class="px-6 py-6 space-y-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-500 mb-1">งวดที่ส่งรายงาน</p>
                                <p class="text-slate-800 font-bold text-lg"><?= e($report['report_period']) ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-slate-500 mb-1">ความคืบหน้า</p>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-indigo-50 text-indigo-650 border border-indigo-150">
                                    <?= e($report['percentage_complete']) ?>%
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <div>
                                <p class="text-xs font-semibold text-slate-500 mb-1.5">สถานะการใช้จ่ายงบประมาณ</p>
                                <?php 
                                $budget_status = $report['budget_spending_status'] ?? 'เป็นไปตามแผน';
                                $bg_color = 'bg-slate-100 text-slate-500 border-slate-200';
                                if(strpos($budget_status, 'ตามแผน') !== false) {
                                    $bg_color = 'bg-emerald-50 text-emerald-600 border-emerald-200';
                                } elseif(strpos($budget_status, 'ล่าช้า') !== false) {
                                    $bg_color = 'bg-amber-50 text-amber-600 border-amber-200';
                                } elseif(strpos($budget_status, 'สูงกว่า') !== false) {
                                    $bg_color = 'bg-rose-50 text-rose-600 border-rose-200';
                                }
                                ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-bold border <?= $bg_color ?>">
                                    <?= e($budget_status) ?>
                                </span>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-500 mb-1.5">ระดับความเสี่ยง</p>
                                <?php 
                                $risk = $report['risk_level'] ?? 'Low';
                                $risk_color = 'bg-emerald-50 text-emerald-600 border-emerald-200';
                                if($risk == 'High') $risk_color = 'bg-rose-50 text-rose-600 border-rose-200';
                                elseif($risk == 'Medium') $risk_color = 'bg-amber-50 text-amber-600 border-amber-200';
                                ?>
                                <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-bold border <?= $risk_color ?>">
                                    <?= e($risk) ?>
                                </span>
                            </div>
                            
                            <!-- Budget Usage Breakdown -->
                            <div class="col-span-2 mt-2 pt-4 border-t border-slate-150">
                                <div class="grid grid-cols-3 gap-4 text-center divide-x divide-slate-200">
                                    <div>
                                        <p class="text-[10px] text-slate-400 mb-1">งบประมาณทั้งหมด</p>
                                        <p class="text-sm font-bold text-slate-800"><?= number_format($project['budget_total'] ?? 0) ?> ฿</p>
                                    </div>
                                    <?php 
                                    $budget_total = $project['budget_total'] ?? 0;
                                    $budget_used = $project['budget_used'] ?? 0;
                                    $budget_remaining = max(0, $budget_total - $budget_used);
                                    ?>
                                    <div>
                                        <p class="text-[10px] text-slate-400 mb-1">งบประมาณที่ใช้ไป</p>
                                        <p class="text-sm font-bold text-amber-600"><?= number_format($budget_used) ?> ฿</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 mb-1">งบประมาณคงเหลือ</p>
                                        <p class="text-sm font-bold text-emerald-650"><?= number_format($budget_remaining) ?> ฿</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <p class="text-sm font-semibold text-slate-500 mb-2">สรุปผลการดำเนินงาน (Summary)</p>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 text-slate-700 text-sm whitespace-pre-wrap leading-relaxed max-h-60 overflow-y-auto custom-scrollbar">
                                <?= htmlspecialchars($report['summary_text'] ?? 'ไม่มีข้อมูลผลการดำเนินงาน') ?>
                            </div>
                        </div>
                        
                        <?php 
                        $attachments = [];
                        if ($report['attachment_path']) {
                            $decoded = json_decode($report['attachment_path'], true);
                            $attachments = is_array($decoded) ? $decoded : [['path' => $report['attachment_path'], 'name' => basename($report['attachment_path'])]];
                        }
                        ?>
                        <?php if (!empty($attachments)): ?>
                        <div>
                            <p class="text-sm font-semibold text-slate-500 mb-2">เอกสารหลักฐาน (ไฟล์แนบ)</p>
                            <div class="space-y-3">
                                <?php foreach($attachments as $file): ?>
                                <button type="button" onclick="handleDownload('<?= e($file['name']) ?>', '<?= e($file['path']) ?>')" class="w-full flex items-center p-3 bg-indigo-50 border border-indigo-150 rounded-xl hover:bg-indigo-100 transition-colors group">
                                    <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center mr-4 text-indigo-650 shadow-sm transition-all">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div class="flex-grow">
                                        <p class="text-indigo-650 font-bold text-sm mb-0.5 text-left truncate max-w-[250px]"><?= e($file['name']) ?></p>
                                        <p class="text-[11px] text-indigo-500 text-left font-medium">คลิกเพื่อดาวน์โหลดไฟล์นี้</p>
                                    </div>
                                    <svg class="w-5 h-5 text-indigo-550 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php else: ?>
                        <div>
                            <p class="text-sm font-semibold text-slate-500 mb-2">เอกสารหลักฐาน (ไฟล์แนบ)</p>
                            <div class="flex items-center p-3 bg-slate-50 border border-slate-100 rounded-xl">
                                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center mr-4 text-slate-400">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div class="flex-grow">
                                    <p class="text-slate-500 font-bold text-sm mb-0.5">ไม่มีเอกสารแนบ</p>
                                    <p class="text-[11px] text-slate-400">ไม่ได้มีการอัปโหลดไฟล์ในงวดนี้</p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="flex justify-between items-center text-sm pt-4 border-t border-slate-150">
                            <span class="text-slate-400">วันที่ส่งรายงาน: <span class="text-slate-700 font-bold"><?= date('d M Y, H:i', strtotime($report['created_at'])) ?></span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<script>
    function openReportModal(id) {
        const modal = document.getElementById('reportModal_' + id);
        const modalContent = document.getElementById('reportModalContent_' + id);
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeReportModal(id) {
        const modal = document.getElementById('reportModal_' + id);
        const modalContent = document.getElementById('reportModalContent_' + id);
        
        modal.classList.add('opacity-0');
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }
</script>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

<?php 
$content = ob_get_clean();
?>

<script>
async function handleApproveClosure(id) {
    const confirmed = await showThemeConfirm(
        'ยืนยันการปิดโครงการ',
        'คุณแน่ใจหรือไม่ที่จะอนุมัติการปิดโครงการนี้? การดำเนินการนี้ไม่สามารถย้อนกลับได้และจะส่งผลต่อสถิติการวิจัย',
        true
    );
    if (confirmed) {
        window.location.href = `/public/projects/approve-closure?id=${id}`;
    }
}

async function handleDownload(name, relativePath) {
    try {
        const confirmed = await showThemeConfirm(
            'ยืนยันการดาวน์โหลด',
            `ต้องการที่จะดาวน์โหลดไฟล์ (${name}) ใช่หรือไม่?`,
            false
        );
        if (confirmed) {
            let basePath = '';
            const pathParts = window.location.pathname.split('/');
            const publicIndex = pathParts.indexOf('public');
            
            if (publicIndex !== -1) {
                basePath = pathParts.slice(0, publicIndex + 1).join('/');
            } else {
                basePath = window.location.pathname.split('/projects/')[0] || '';
            }
            
            if (basePath.endsWith('/')) basePath = basePath.slice(0, -1);
            let p = relativePath;
            if (p.startsWith('/')) p = p.slice(1);
            
            const fullUrl = window.location.origin + (basePath.startsWith('/') ? '' : '/') + basePath + '/' + p;
            
            const link = document.createElement('a');
            link.href = fullUrl;
            link.download = name;
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            setTimeout(() => {
                if (link.parentNode) document.body.removeChild(link);
            }, 100);
        }
    } catch (err) {
        console.error('Download failed:', err);
    }
}
</script>

<?php
require __DIR__ . '/../layouts/app.php'; 
?>
