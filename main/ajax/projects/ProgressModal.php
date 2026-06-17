<?php
// main/ajax/projects/ProgressModal.php - ฟอร์มส่งรายงานความก้าวหน้า (AJAX Modal)
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');
if (!isLoggedIn()) { http_response_code(401); exit; }

$project_id = (int)($_GET['project_id'] ?? 0);
if (!$project_id) { echo '<div class="alert alert-danger m-3">ไม่พบรหัสโครงการ</div>'; exit; }

$stmt = $pdo->prepare('SELECT p.*, pr.title FROM projects p JOIN proposals pr ON p.proposal_id = pr.id WHERE p.id = ?');
$stmt->execute([$project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) { echo '<div class="alert alert-danger m-3">ไม่พบข้อมูลโครงการ</div>'; exit; }

// Fetch milestones
$stmt = $pdo->prepare('SELECT * FROM project_milestones WHERE project_id = ? ORDER BY id ASC');
$stmt->execute([$project_id]);
$milestones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate current progress
$stmtProg = $pdo->prepare('SELECT SUM(percentage_complete) FROM progress_reports WHERE project_id = ?');
$stmtProg->execute([$project_id]);
$current_progress = (int)$stmtProg->fetchColumn();
?>

<div class="modal-header">
    <h5 class="modal-title fw-bold"><i class="ri-line-chart-line me-2 text-primary"></i>ส่งรายงานความก้าวหน้า</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <form id="progressReportForm" enctype="multipart/form-data">
        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
        
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">งวด/รอบการรายงานปัจจุบัน <span class="text-danger">*</span></label>
                <input type="text" name="report_period" required class="form-control" placeholder="เช่น งวดที่ 1 / เดือนที่ 3">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">ความก้าวหน้าดำเนินการจริง (%) <span class="text-danger">*</span></label>
                <input type="number" name="percentage_complete" min="0" max="100" required class="form-control" value="0">
                <small class="text-muted">ความก้าวหน้าสะสมก่อนหน้านี้: <?php echo $current_progress; ?>%</small>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">ระดับความเสี่ยง</label>
                <select name="risk_level" class="form-select">
                    <option value="Low">Low - ต่ำ</option>
                    <option value="Medium">Medium - ปานกลาง</option>
                    <option value="High">High - สูง</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">สรุปผลการดำเนินงานตั้งแต่งวดที่แล้ว - ปัจจุบัน <span class="text-danger">*</span></label>
            <textarea name="summary_text" rows="4" required class="form-control" placeholder="อธิบายกิจกรรมที่ดำเนินการ ผลที่ได้รับ และหลักฐานเชิงประจักษ์..."></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">ปัญหา/อุปสรรคที่พบ และแนวทางแก้ไข</label>
            <textarea name="problems_obstacles" rows="2" class="form-control" placeholder="ระบุปัญหาที่อาจทำให้โครงการล่าช้า และการแก้ปัญหาเฉพาะหน้า (ถ้ามี)..."></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">แผนงานถัดไป (Next Milestone) <span class="text-danger">*</span></label>
            <textarea name="next_milestone_plan" rows="2" required class="form-control" placeholder="ระบุแผนงาน/กิจกรรมที่จะดำเนินการต่อในงวดถัดไป..."></textarea>
        </div>

        <?php if (!empty($milestones)): ?>
        <div class="mb-3 bg-light p-3 rounded border border-slate-100">
            <label class="form-label fw-bold mb-2"><i class="ri-checkbox-circle-line me-1"></i> อัปเดตสถานะเป้าหมายย่อย (Milestones)</label>
            <div class="space-y-2">
                <?php foreach ($milestones as $m): ?>
                    <div class="row g-2 align-items-center mb-2">
                        <div class="col-md-8 text-truncate small">
                            <strong><?php echo htmlspecialchars($m['milestone_name']); ?>:</strong>
                            <span class="text-muted"><?php echo htmlspecialchars($m['description'] ?? ''); ?></span>
                        </div>
                        <div class="col-md-4">
                            <select name="milestones[<?php echo $m['id']; ?>]" class="form-select form-select-sm">
                                <option value="pending" <?php echo $m['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="in_progress" <?php echo $m['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                <option value="completed" <?php echo $m['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="mb-3">
            <label class="form-label fw-bold">แนบเอกสารหลักฐาน (เช่น PDF หรือไฟล์รูปภาพ)</label>
            <input type="file" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
            <button type="button" onclick="submitProgressReport()" class="btn btn-dark fw-bold">ส่งรายงาน</button>
        </div>
    </form>
</div>

<script>
function submitProgressReport() {
    const form = document.getElementById('progressReportForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    const formData = new FormData(form);

    $.ajax({
        type: 'POST',
        url: 'ajax/projects/SaveProgress.php',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res) {
            if (res.result === 1) {
                $('#mainModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'ส่งรายงานสำเร็จ',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('ผิดพลาด', res.message, 'error');
            }
        },
        error: function() {
            Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
        }
    });
}
</script>
