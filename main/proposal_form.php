<?php
// main/proposal_form.php - ฟอร์มยื่นข้อเสนอโครงการวิจัย (Standalone Page)
$pageTitle = 'ยื่นข้อเสนอโครงการวิจัย';
$pageCss   = 'proposals';
$pageJs    = 'proposals_form'; // we will create proposals_form.js next
require 'header.php';

$proposal_id = (int)($_GET['id'] ?? 0);
$proposal = null;
$teams = [];
$editMilestones = [];
$editOutputs = [];
$mode = 'create';

if ($proposal_id) {
    $mode = 'edit';
    $user_id = authUser()['id'];
    $stmt = $pdo->prepare('SELECT * FROM proposals WHERE id = ? AND (user_id = ? OR ? = 1)');
    $stmt->execute([$proposal_id, $user_id, (int)hasRole('admin')]);
    $proposal = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$proposal) { 
        echo '<div class="alert alert-danger">ไม่พบข้อเสนอโครงการหรือไม่มีสิทธิ์แก้ไข</div>'; 
        require 'footer.php'; 
        exit; 
    }

    $teamStmt = $pdo->prepare('SELECT * FROM proposal_teams WHERE proposal_id = ?');
    $teamStmt->execute([$proposal_id]);
    $teams = $teamStmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($proposal['milestones'])) {
        $decoded = json_decode($proposal['milestones'], true);
        if (is_array($decoded)) $editMilestones = $decoded;
    }
    if (!empty($proposal['expected_outputs'])) {
        $decoded = json_decode($proposal['expected_outputs'], true);
        if (is_array($decoded)) $editOutputs = $decoded;
    }
}

$proposer_name = authUser()['name'];
if ($proposal) {
    $uStmt = $pdo->prepare('SELECT name FROM users WHERE id = ?');
    $uStmt->execute([$proposal['user_id']]);
    $proposer = $uStmt->fetch(PDO::FETCH_ASSOC);
    if ($proposer) {
        $proposer_name = $proposer['name'];
    }
}

$stmt = $pdo->query('SELECT * FROM funding_sources ORDER BY name');
$fundingSources = $stmt->fetchAll(PDO::FETCH_ASSOC);

$action = $mode === 'edit' ? 'ajax/proposals/SaveProposal.php?id=' . $proposal_id : 'ajax/proposals/SaveProposal.php';
?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-4">
    <div>
        <h1 class="page-title">
            <i class="ri-file-add-line me-2"></i>
            <?php echo $mode === 'edit' ? 'แก้ไขข้อเสนอโครงการ' : 'ยื่นข้อเสนอโครงการใหม่'; ?>
        </h1>
        <nav aria-label="breadcrumb" class="mt-1">
            <ol class="breadcrumb mb-0" style="font-size:13px;">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">หน้าหลัก</a></li>
                <li class="breadcrumb-item"><a href="projects.php" class="text-decoration-none">โครงการ & ข้อเสนอ</a></li>
                <li class="breadcrumb-item active"><?php echo $mode === 'edit' ? 'แก้ไขข้อเสนอ' : 'ยื่นข้อเสนอใหม่'; ?></li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="projects.php" class="btn btn-outline-secondary">
            <i class="ri-arrow-left-line me-1"></i> ย้อนกลับ
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form id="standaloneProposalForm" enctype="multipart/form-data">
            <?php if ($proposal_id): ?>
            <input type="hidden" name="proposal_id" value="<?php echo $proposal_id; ?>">
            <?php endif; ?>

            <!-- Section 1: ข้อมูลทั่วไป -->
            <div class="mb-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                    <i class="ri-information-line me-1 text-primary"></i> 1. ข้อมูลทั่วไปของโครงการ
                </h5>
                <div class="mb-3">
                    <label class="form-label fw-bold">ชื่อโครงการ (ภาษาไทย) <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" required
                           value="<?php echo htmlspecialchars($proposal['title'] ?? ''); ?>"
                           placeholder="ระบุชื่อโครงการวิจัยภาษาไทย">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">ชื่อโครงการ (ภาษาอังกฤษ)</label>
                    <input type="text" name="title_en" class="form-control"
                           value="<?php echo htmlspecialchars($proposal['title_en'] ?? ''); ?>"
                           placeholder="Research Project Title in English">
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">ประเภทงานวิจัย</label>
                        <select name="research_type" class="form-select">
                            <option value="">-- เลือกประเภท --</option>
                            <?php foreach (['พื้นฐาน','ประยุกต์','พัฒนา','นวัตกรรม'] as $t): ?>
                            <option value="<?php echo $t; ?>"
                                <?php echo ($proposal['research_type'] ?? '') === $t ? 'selected' : ''; ?>>
                                <?php echo $t; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">แหล่งทุน</label>
                        <select name="funding_source_id" class="form-select">
                            <option value="">-- ยังไม่ระบุ --</option>
                            <?php foreach ($fundingSources as $fs): ?>
                            <option value="<?php echo $fs['id']; ?>"
                                <?php echo ($proposal['funding_source_id'] ?? '') == $fs['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($fs['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">คำสำคัญ (Keywords)</label>
                    <input type="text" name="keywords" class="form-control"
                           value="<?php echo htmlspecialchars($proposal['keywords'] ?? ''); ?>"
                           placeholder="เช่น: AI, Machine Learning, การเกษตร">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">บทคัดย่อ (Abstract)</label>
                    <textarea name="abstract" class="form-control" rows="5"
                              placeholder="สรุปสาระสำคัญของข้อเสนอโครงการวิจัย..."><?php echo htmlspecialchars($proposal['abstract'] ?? ''); ?></textarea>
                </div>
            </div>

            <!-- Section 2: งบประมาณและระยะเวลา -->
            <div class="mb-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                    <i class="ri-money-dollar-circle-line me-1 text-primary"></i> 2. งบประมาณและระยะเวลาดำเนินงาน
                </h5>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">งบประมาณรวม (บาท)</label>
                        <input type="text" name="budget_total" class="form-control amount-input"
                               value="<?php echo isset($proposal['budget_total']) ? number_format($proposal['budget_total'], 2) : ''; ?>"
                               placeholder="0.00">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">วันที่เริ่มต้นโครงการ</label>
                        <input type="date" name="start_date" class="form-control"
                               value="<?php echo $proposal['start_date'] ?? ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">วันที่สิ้นสุดโครงการ</label>
                        <input type="date" name="end_date" class="form-control"
                               value="<?php echo $proposal['end_date'] ?? ''; ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">รายละเอียดโครงสร้างงบประมาณ</label>
                    <textarea name="budget_details" class="form-control" rows="3"
                              placeholder="เช่น ค่าตอบแทนบุคลากร ค่าวัสดุอุปกรณ์ ค่าใช้สอยในการเดินทาง..."><?php echo htmlspecialchars($proposal['budget_details'] ?? ''); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">แผนงานหลัก (Milestones) ตลอดระยะเวลาโครงการ</label>
                    <div id="standaloneMilestonesContainer">
                        <?php if (!empty($editMilestones)): foreach ($editMilestones as $mi => $ms): ?>
                        <div class="row g-2 mb-2 milestone-row align-items-start" id="milestone-row-<?php echo $mi; ?>">
                            <div class="col-md-5">
                                <input type="text" name="milestone_name[]" class="form-control"
                                       placeholder="ชื่อระยะเวลา (เช่น งวดที่ 1)" required value="<?php echo htmlspecialchars($ms['name']); ?>">
                            </div>
                            <div class="col-md-6">
                                <textarea name="milestone_description[]" class="form-control" rows="1"
                                          placeholder="รายละเอียดของงานที่จะทำ" required><?php echo htmlspecialchars($ms['description']); ?></textarea>
                            </div>
                            <?php if ($mi > 0): ?>
                            <div class="col-md-1 text-center">
                                <button type="button" class="btn btn-outline-danger" onclick="removeMilestoneRow(<?php echo $mi; ?>)">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; else: ?>
                        <div class="row g-2 mb-2 milestone-row align-items-start" id="milestone-row-0">
                            <div class="col-md-5">
                                <input type="text" name="milestone_name[]" class="form-control"
                                       placeholder="ชื่อระยะเวลา (เช่น งวดที่ 1)" required>
                            </div>
                            <div class="col-md-7">
                                <textarea name="milestone_description[]" class="form-control" rows="1"
                                          placeholder="รายละเอียดของงานที่จะทำ" required></textarea>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-dark mt-2" onclick="addStandaloneMilestoneRow()">
                        <i class="ri-add-line me-1"></i> เพิ่ม Milestone
                    </button>
                </div>
            </div>

            <!-- Section 3: ทีมวิจัย -->
            <div class="mb-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                    <i class="ri-team-line me-1 text-primary"></i> 3. ทีมงานวิจัยผู้ร่วมดำเนินการ (Co-Investigators)
                </h5>
                
                <div id="standaloneTeamContainer">
                    <!-- Proposer Row (Frozen) -->
                    <div class="row g-2 mb-2 team-row-proposer align-items-center">
                        <div class="col-md-5">
                            <input type="text" class="form-control bg-light"
                                   value="<?php echo htmlspecialchars($proposer_name); ?>" readonly>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select bg-light" disabled>
                                <option selected>Principal Investigator</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" id="standalone_pi_proportion" name="pi_proportion" class="form-control text-center bg-light fw-bold"
                                   value="<?php echo $proposal ? $proposal['pi_proportion'] : '100'; ?>" readonly>
                        </div>
                        <div class="col-md-1 text-center">
                            <button type="button" class="btn btn-outline-secondary" disabled>
                                <i class="ri-lock-line"></i>
                            </button>
                        </div>
                    </div>

                    <?php if (!empty($teams)): foreach ($teams as $t): ?>
                    <div class="row g-2 mb-2 team-row align-items-center">
                        <div class="col-md-5">
                            <input type="text" name="team_name[]" class="form-control"
                                   placeholder="ชื่อ-นามสกุลผู้ร่วมวิจัย" value="<?php echo htmlspecialchars($t['name']); ?>">
                        </div>
                        <div class="col-md-4">
                            <select name="team_role[]" class="form-select">
                                <option value="Co-Investigator" <?php echo $t['role'] === 'Co-Investigator' ? 'selected' : ''; ?>>Co-Investigator</option>
                                <option value="Research Assistant" <?php echo $t['role'] === 'Research Assistant' ? 'selected' : ''; ?>>Research Assistant</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="team_proportion[]" class="form-control text-center team-proportion-input"
                                   placeholder="สัดส่วน %" min="0" max="100" value="<?php echo $t['proportion']; ?>" oninput="calculateStandalonePIProportion()">
                        </div>
                        <div class="col-md-1 text-center">
                            <button type="button" class="btn btn-outline-danger" onclick="removeTeamRow(this)">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; endif; ?>
                </div>

                <button type="button" class="btn btn-sm btn-outline-dark mt-2" onclick="addStandaloneTeamRow()">
                    <i class="ri-add-line me-1"></i> เพิ่มผู้ร่วมวิจัยย่อย
                </button>
            </div>

            <!-- Section 4: ความสอดคล้องยุทธศาสตร์ -->
            <div class="mb-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                    <i class="ri-focus-3-line me-1 text-primary"></i> 4. ผลกระทบและความสอดคล้องเชิงยุทธศาสตร์
                </h5>
                <div class="mb-3">
                    <label class="form-label fw-bold">ความสอดคล้องเชิงนโยบาย/ยุทธศาสตร์</label>
                    <textarea name="strategic_link" class="form-control" rows="3"
                              placeholder="อธิบายความสอดคล้องกับเป้าหมายการวิจัยระดับมหาวิทยาลัยหรือระดับชาติ..."><?php echo htmlspecialchars($proposal['strategic_link'] ?? ''); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">ตัวชี้วัดความสำเร็จของเป้าหมายโครงการ (KPIs)</label>
                    <input type="text" name="impact_indicator" class="form-control"
                           value="<?php echo htmlspecialchars($proposal['impact_indicator'] ?? ''); ?>"
                           placeholder="เช่น ตีพิมพ์ในวารสารระดับนานาชาติ 1 เรื่อง, สิทธิบัตรการจดทะเบียน 1 รายการ">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">ผลผลิต (Expected Outputs) ที่คาดหวัง <span class="text-danger">*</span></label>
                    <div class="d-flex flex-wrap gap-3 mt-2">
                        <?php 
                        $outputsOptions = [
                            'Journal/Proceeding' => 'บทความตีพิมพ์ (Journal/Proceeding)',
                            'Patent/Petty Patent' => 'สิทธิบัตร/อนุสิทธิบัตร (Patent)',
                            'Creative Work' => 'ผลงานสร้างสรรค์ (Creative Work)',
                            'Policy Impact' => 'การนำไปใช้ประโยชน์ในเชิงนโยบาย'
                        ];
                        foreach ($outputsOptions as $val => $lbl):
                        ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="expected_outputs[]" 
                                   value="<?php echo $val; ?>" id="out_<?php echo md5($val); ?>"
                                   <?php echo in_array($val, $editOutputs) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="out_<?php echo md5($val); ?>">
                                <?php echo $lbl; ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Section 5: ไฟล์แนบ -->
            <div class="mb-4">
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                    <i class="ri-attachment-line me-1 text-primary"></i> 5. ไฟล์เอกสารแนบโครงการวิจัย
                </h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">ไฟล์แบบเสนอโครงการวิจัย (PDF / Word) <span class="text-danger">*</span></label>
                        <input type="file" name="file_proposal" class="form-control" accept=".pdf,.doc,.docx">
                        <?php if (!empty($proposal['file_proposal'])): ?>
                        <small class="text-muted"><i class="ri-file-line"></i> ไฟล์ที่อัปโหลดแล้ว: <?php echo htmlspecialchars($proposal['file_proposal']); ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">ไฟล์รายละเอียดการคำนวณงบประมาณ</label>
                        <input type="file" name="file_budget" class="form-control" accept=".pdf,.xls,.xlsx">
                        <?php if (!empty($proposal['file_budget'])): ?>
                        <small class="text-muted"><i class="ri-file-line"></i> ไฟล์ที่อัปโหลดแล้ว: <?php echo htmlspecialchars($proposal['file_budget']); ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary px-4" onclick="window.location.href='projects.php'">
                    ยกเลิก
                </button>
                <button type="button" class="btn btn-secondary px-4" onclick="saveStandaloneProposal('draft')">
                    <i class="ri-save-line me-1"></i> บันทึกร่างโครงการ
                </button>
                <button type="button" class="btn btn-dark fw-bold px-4" onclick="saveStandaloneProposal('submitted')">
                    <i class="ri-send-plane-line me-1"></i> ยื่นเสนอโครงการ
                </button>
            </div>
        </form>
    </div>
</div>

<?php require 'footer.php'; ?>
