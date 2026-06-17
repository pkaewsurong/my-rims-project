<?php
// main/ajax/projects/ProgressDetailModal.php - รายละเอียดรายงานความก้าวหน้า
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');
if (!isLoggedIn()) { http_response_code(401); exit; }

$id = (int)($_GET['id'] ?? 0);
if (!$id) { echo '<div class="alert alert-danger m-3">ไม่พบไอดีรายงาน</div>'; exit; }

$stmt = $pdo->prepare('SELECT pr.*, p.code as project_code FROM progress_reports pr JOIN projects p ON pr.project_id = p.id WHERE pr.id = ?');
$stmt->execute([$id]);
$report = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$report) { echo '<div class="alert alert-danger m-3">ไม่พบข้อมูลรายงาน</div>'; exit; }
?>

<div class="modal-header">
    <h5 class="modal-title fw-bold"><i class="ri-eye-line me-2 text-primary"></i>รายละเอียดรายงานความก้าวหน้า</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label class="text-muted small d-block">งวดการรายงาน</label>
            <span class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($report['report_period']); ?></span>
        </div>
        <div class="col-md-6">
            <label class="text-muted small d-block">ระดับความเสี่ยง</label>
            <span class="badge bg-<?php echo $report['risk_level'] === 'High' ? 'danger' : ($report['risk_level'] === 'Medium' ? 'warning text-dark' : 'success'); ?> fs-6 mt-1">
                <?php echo $report['risk_level']; ?>
            </span>
        </div>
        <div class="col-md-6">
            <label class="text-muted small d-block">ความก้าวหน้าดำเนินการจริง (%)</label>
            <span class="fw-bold text-success fs-6"><?php echo $report['percentage_complete']; ?>%</span>
        </div>
        <div class="col-md-6">
            <label class="text-muted small d-block">วันที่รายงาน</label>
            <span class="fw-bold text-dark fs-6"><?php echo date('d M Y H:i', strtotime($report['created_at'])); ?></span>
        </div>
    </div>

    <div class="mb-3">
        <label class="text-muted small d-block mb-1">สรุปผลการดำเนินงาน</label>
        <div class="bg-light p-3 rounded border whitespace-pre-wrap"><?php echo htmlspecialchars($report['summary_text']); ?></div>
    </div>

    <?php if ($report['problems_obstacles']): ?>
    <div class="mb-3">
        <label class="text-muted d-block small mb-1 text-danger">ปัญหา/อุปสรรค</label>
        <div class="bg-light p-3 rounded border whitespace-pre-wrap text-danger border-danger-subtle"><?php echo htmlspecialchars($report['problems_obstacles']); ?></div>
    </div>
    <?php endif; ?>

    <div class="mb-3">
        <label class="text-muted small d-block mb-1">แผนการทำงานงวดถัดไป</label>
        <div class="bg-light p-3 rounded border whitespace-pre-wrap"><?php echo htmlspecialchars($report['next_milestone_plan']); ?></div>
    </div>

    <?php if ($report['attachment_path']): ?>
    <div class="mb-3">
        <label class="text-muted small d-block mb-1">เอกสารแนบ</label>
        <a href="../public/uploads/progress/<?php echo htmlspecialchars($report['attachment_path']); ?>" target="_blank" class="btn btn-sm btn-outline-success">
            <i class="ri-file-download-line me-1"></i> ดาวน์โหลดไฟล์เอกสารแนบ
        </a>
    </div>
    <?php endif; ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
</div>
