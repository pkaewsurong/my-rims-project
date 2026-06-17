<?php
// main/ajax/proposals/CreateModal.php - ฟอร์มยื่นข้อเสนอ (AJAX Modal)
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');
if (!isLoggedIn()) { http_response_code(401); exit; }

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
    if (!$proposal) { echo '<div class="alert alert-danger m-3">ไม่พบข้อเสนอโครงการ</div>'; exit; }

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
<div class="modal-header">
    <h5 class="modal-title fw-bold">
        <i class="ri-file-add-line me-2"></i>
        <?php echo $mode === 'edit' ? 'แก้ไขข้อเสนอโครงการ' : 'ยื่นข้อเสนอโครงการใหม่'; ?>
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <form id="proposalForm" enctype="multipart/form-data">
        <?php if ($proposal_id): ?>
        <input type="hidden" name="proposal_id" value="<?php echo $proposal_id; ?>">
        <?php endif; ?>

        <!-- Step 1: ข้อมูลทั่วไป -->
        <h6 class="fw-bold mb-3 pb-2 border-bottom" style="color:#191a23;">
            <i class="ri-information-line me-1"></i> ข้อมูลทั่วไป
        </h6>

        <div class="mb-3">
            <label class="form-label fw-semibold">ชื่อโครงการ (ภาษาไทย) <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control"
                   value="<?php echo htmlspecialchars($proposal['title'] ?? ''); ?>"
                   placeholder="ระบุชื่อโครงการวิจัย">
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">ชื่อโครงการ (ภาษาอังกฤษ)</label>
            <input type="text" name="title_en" class="form-control"
                   value="<?php echo htmlspecialchars($proposal['title_en'] ?? ''); ?>"
                   placeholder="Research Project Title in English">
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">ประเภทงานวิจัย</label>
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
                <label class="form-label fw-semibold">แหล่งทุน</label>
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
            <label class="form-label fw-semibold">คำสำคัญ (Keywords)</label>
            <input type="text" name="keywords" class="form-control"
                   value="<?php echo htmlspecialchars($proposal['keywords'] ?? ''); ?>"
                   placeholder="เช่น: AI, Machine Learning, การแพทย์">
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">บทคัดย่อ (Abstract)</label>
            <textarea name="abstract" class="form-control" rows="4"
                      placeholder="สรุปสาระสำคัญของงานวิจัย"><?php echo htmlspecialchars($proposal['abstract'] ?? ''); ?></textarea>
        </div>

        <!-- Step 2: งบประมาณ -->
        <h6 class="fw-bold mb-3 mt-4 pb-2 border-bottom" style="color:#191a23;">
            <i class="ri-money-dollar-circle-line me-1"></i> งบประมาณและระยะเวลา
        </h6>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">งบประมาณรวม (บาท)</label>
                <input type="text" name="budget_total" class="form-control amount-input"
                       value="<?php echo isset($proposal['budget_total']) ? number_format($proposal['budget_total'], 2) : ''; ?>"
                       placeholder="0.00">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">วันที่เริ่ม</label>
                <input type="date" name="start_date" class="form-control"
                       value="<?php echo $proposal['start_date'] ?? ''; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">วันที่สิ้นสุด</label>
                <input type="date" name="end_date" class="form-control"
                       value="<?php echo $proposal['end_date'] ?? ''; ?>">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">รายละเอียดงบประมาณ</label>
            <textarea name="budget_details" class="form-control" rows="3"
                      placeholder="รายละเอียดการใช้งบประมาณ"><?php echo htmlspecialchars($proposal['budget_details'] ?? ''); ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">แผนงานหลัก (Milestones) ตลอดระยะเวลาโครงการ</label>
            <div id="modalMilestonesContainer">
                <?php if (!empty($editMilestones)): foreach ($editMilestones as $mi => $ms): ?>
                <div class="row g-2 mb-2 milestone-row align-items-start" id="modal-milestone-row-<?php echo $mi; ?>">
                    <div class="col-md-5">
                        <input type="text" name="milestone_name[]" class="form-control form-control-sm"
                               placeholder="ชื่อระยะเวลา (เช่น งวดที่ 1)" required value="<?php echo htmlspecialchars($ms['name']); ?>">
                    </div>
                    <div class="col-md-6">
                        <textarea name="milestone_description[]" class="form-control form-control-sm" rows="1"
                                  placeholder="รายละเอียด" required><?php echo htmlspecialchars($ms['description']); ?></textarea>
                    </div>
                    <?php if ($mi > 0): ?>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeModalMilestoneRow(<?php echo $mi; ?>)">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; else: ?>
                <div class="row g-2 mb-2 milestone-row align-items-start" id="modal-milestone-row-0">
                    <div class="col-md-5">
                        <input type="text" name="milestone_name[]" class="form-control form-control-sm"
                               placeholder="ชื่อระยะเวลา (เช่น งวดที่ 1)" required>
                    </div>
                    <div class="col-md-7">
                        <textarea name="milestone_description[]" class="form-control form-control-sm" rows="1"
                                  placeholder="รายละเอียด" required></textarea>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <button type="button" class="btn btn-sm btn-outline-dark mt-2" onclick="addModalMilestoneRow()">
                <i class="ri-add-line me-1"></i> เพิ่ม Milestone
            </button>
        </div>

        <!-- Step 3: ทีมวิจัย -->
        <h6 class="fw-bold mb-3 mt-4 pb-2 border-bottom" style="color:#191a23;">
            <i class="ri-team-line me-1"></i> ทีมวิจัย (Co-Investigators)
        </h6>

        <div id="teamContainer">
            <!-- Proposer Row (Frozen) -->
            <div class="row g-2 mb-2 team-row-proposer">
                <div class="col-md-5">
                    <input type="text" class="form-control form-control-sm bg-light"
                           value="<?php echo htmlspecialchars($proposer_name); ?>" readonly>
                </div>
                <div class="col-md-4">
                    <select class="form-select form-select-sm bg-light" disabled>
                        <option selected>Principal Investigator</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" id="modal_pi_proportion" name="pi_proportion" class="form-control form-control-sm bg-light text-center fw-bold"
                           value="<?php echo $proposal ? $proposal['pi_proportion'] : '100'; ?>" readonly>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-sm btn-outline-secondary w-100" disabled>
                        <i class="ri-lock-line"></i>
                    </button>
                </div>
            </div>

            <?php if (!empty($teams)): foreach ($teams as $t): ?>
            <div class="row g-2 mb-2 team-row">
                <div class="col-md-5">
                    <input type="text" name="team_name[]" class="form-control form-control-sm"
                           placeholder="ชื่อ-นามสกุล" value="<?php echo htmlspecialchars($t['name']); ?>">
                </div>
                <div class="col-md-4">
                    <select name="team_role[]" class="form-select form-select-sm">
                        <option value="Co-Investigator" <?php echo $t['role'] === 'Co-Investigator' ? 'selected' : ''; ?>>Co-Investigator</option>
                        <option value="Research Assistant" <?php echo $t['role'] === 'Research Assistant' ? 'selected' : ''; ?>>Research Assistant</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="team_proportion[]" class="form-control form-control-sm team-proportion-input"
                           placeholder="%" min="0" max="100" value="<?php echo $t['proportion']; ?>" oninput="calculateModalPIProportion()">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeTeam(this)">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>

        <button type="button" class="btn btn-sm btn-outline-dark mt-2" onclick="addTeamRow()">
            <i class="ri-add-line me-1"></i> เพิ่มผู้ร่วมวิจัย
        </button>

        <!-- Step 4: ยุทธศาสตร์ -->
        <h6 class="fw-bold mb-3 mt-4 pb-2 border-bottom" style="color:#191a23;">
            <i class="ri-focus-3-line me-1"></i> ความเชื่อมโยงเชิงยุทธศาสตร์
        </h6>

        <div class="mb-3">
            <label class="form-label fw-semibold">ความเชื่อมโยงกับยุทธศาสตร์</label>
            <textarea name="strategic_link" class="form-control" rows="2"
                      placeholder="อธิบายความสัมพันธ์กับยุทธศาสตร์ขององค์กร"><?php echo htmlspecialchars($proposal['strategic_link'] ?? ''); ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">ตัวชี้วัดผลกระทบ</label>
            <input type="text" name="impact_indicator" class="form-control"
                   value="<?php echo htmlspecialchars($proposal['impact_indicator'] ?? ''); ?>"
                   placeholder="เช่น: จำนวนสิทธิบัตร, จำนวนตีพิมพ์">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">ผลผลิต (Expected Outputs) ที่คาดหวัง <span class="text-danger">*</span></label>
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
                           value="<?php echo $val; ?>" id="modal_out_<?php echo md5($val); ?>"
                           <?php echo in_array($val, $editOutputs) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="modal_out_<?php echo md5($val); ?>">
                        <?php echo $lbl; ?>
                    </label>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ไฟล์แนบ -->
        <h6 class="fw-bold mb-3 mt-4 pb-2 border-bottom" style="color:#191a23;">
            <i class="ri-attachment-2 me-1"></i> ไฟล์แนบ (Optional)
        </h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">เอกสารข้อเสนอ (PDF)</label>
                <input type="file" name="file_proposal" class="form-control" accept=".pdf,.doc,.docx">
                <?php if (!empty($proposal['file_proposal'])): ?>
                <small class="text-muted"><i class="ri-file-line"></i> ไฟล์ปัจจุบัน: <?php echo $proposal['file_proposal']; ?></small>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">เอกสารงบประมาณ</label>
                <input type="file" name="file_budget" class="form-control" accept=".pdf,.xls,.xlsx">
            </div>
        </div>

        <hr class="my-4">

        <div class="d-flex gap-2 justify-content-end">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                ยกเลิก
            </button>
            <button type="button" class="btn btn-secondary" onclick="saveProposal('draft')">
                <i class="ri-save-line me-1"></i> บันทึกแบบร่าง
            </button>
            <button type="button" class="btn btn-dark fw-bold" onclick="saveProposal('submitted')">
                <i class="ri-send-plane-line me-1"></i> ยื่นข้อเสนอ
            </button>
        </div>
    </form>
</div>

<script>
function calculateModalPIProportion() {
    const inputs = document.querySelectorAll('#teamContainer .team-proportion-input');
    let totalCoi = 0;
    inputs.forEach(input => {
        totalCoi += parseInt(input.value) || 0;
    });
    
    const piInput = document.getElementById('modal_pi_proportion');
    if (piInput) {
        let newPi = 100 - totalCoi;
        if (newPi < 0) {
            Swal.fire({
                icon: 'warning',
                title: 'สัดส่วนรวมเกิน 100%',
                text: 'สัดส่วนการทำงานของทีมวิจัยรวมกันต้องไม่เกิน 100%'
            });
            newPi = 0;
        }
        piInput.value = newPi;
    }
}

function addTeamRow() {
    const container = document.getElementById('teamContainer');
    const row = document.createElement('div');
    row.className = 'row g-2 mb-2 team-row';
    row.innerHTML = `
        <div class="col-md-5">
            <input type="text" name="team_name[]" class="form-control form-control-sm" placeholder="ชื่อ-นามสกุล">
        </div>
        <div class="col-md-4">
            <select name="team_role[]" class="form-select form-select-sm">
                <option value="Co-Investigator">Co-Investigator</option>
                <option value="Research Assistant">Research Assistant</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" name="team_proportion[]" class="form-control form-control-sm team-proportion-input" placeholder="%" min="0" max="100" value="0" oninput="calculateModalPIProportion()">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeTeam(this)">
                <i class="ri-delete-bin-line"></i>
            </button>
        </div>
    `;
    container.appendChild(row);
    calculateModalPIProportion();
}

function removeTeam(btn) {
    btn.closest('.team-row').remove();
    calculateModalPIProportion();
}

// Format currency inputs on load and input
$(document).ready(function() {
    const amountInputs = document.querySelectorAll('.amount-input');
    
    amountInputs.forEach(input => {
        if(input.value) {
            let val = input.value.replace(/,/g, '');
            if(!isNaN(val) && val !== '') {
                // Keep decimal points if they exist
                const parts = val.split('.');
                if (parts.length > 1) {
                    input.value = Number(parts[0]).toLocaleString('en-US') + '.' + parts[1];
                } else {
                    input.value = Number(val).toLocaleString('en-US');
                }
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
});

let modalMilestoneIndex = $('#modalMilestonesContainer .milestone-row').length || 1;

function addModalMilestoneRow() {
    const container = document.getElementById('modalMilestonesContainer');
    const row = document.createElement('div');
    row.className = 'row g-2 mb-2 milestone-row align-items-start';
    row.id = `modal-milestone-row-${modalMilestoneIndex}`;
    row.innerHTML = `
        <div class="col-md-5">
            <input type="text" name="milestone_name[]" class="form-control form-control-sm" placeholder="ชื่อระยะเวลา (เช่น งวดที่ 2)" required>
        </div>
        <div class="col-md-6">
            <textarea name="milestone_description[]" class="form-control form-control-sm" rows="1" placeholder="รายละเอียด" required></textarea>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeModalMilestoneRow(${modalMilestoneIndex})">
                <i class="ri-delete-bin-line"></i>
            </button>
        </div>
    `;
    container.appendChild(row);
    modalMilestoneIndex++;
}

function removeModalMilestoneRow(index) {
    const row = document.getElementById(`modal-milestone-row-${index}`);
    if (row) row.remove();
}

function saveProposal(status) {
    const form = document.getElementById('proposalForm');
    const formData = new FormData(form);
    formData.append('status', status);

    $.ajax({
        type: 'POST',
        url: '<?php echo $action; ?>',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res) {
            if (res.result === 1) {
                $('#mainModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: status === 'draft' ? 'บันทึกแบบร่างสำเร็จ' : 'ยื่นข้อเสนอสำเร็จ',
                    timer: 1800,
                    showConfirmButton: false
                });
                GetTable();
            } else {
                Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message });
            }
        },
        error: function() {
            Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ' });
        }
    });
}
</script>
