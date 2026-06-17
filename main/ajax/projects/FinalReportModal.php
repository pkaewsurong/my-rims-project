<?php
// main/ajax/projects/FinalReportModal.php - ฟอร์มส่งรายงานฉบับสมบูรณ์ (AJAX Modal)
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');
if (!isLoggedIn()) { http_response_code(401); exit; }

$project_id = (int)($_GET['project_id'] ?? 0);
if (!$project_id) { echo '<div class="alert alert-danger m-3">ไม่พบรหัสโครงการ</div>'; exit; }
?>

<div class="modal-header">
    <h5 class="modal-title fw-bold"><i class="ri-survey-line me-2 text-primary"></i>ส่งรายงานวิจัยฉบับสมบูรณ์ (Final Report)</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <form id="finalReportForm" enctype="multipart/form-data">
        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
        
        <div class="mb-3">
            <label class="form-label fw-bold">บทสรุปผู้บริหาร (Executive Summary) <span class="text-danger">*</span></label>
            <textarea name="executive_summary" rows="4" required class="form-control" placeholder="สรุปหัวข้อวิจัย วัตถุประสงค์ วิธีดำเนินงานวิจัย และผลลัพธ์ที่ได้..."></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">ประโยชน์ของโครงการและการนำไปใช้ประโยชน์ <span class="text-danger">*</span></label>
            <textarea name="utilization_impact" rows="3" required class="form-control" placeholder="การนำไปใช้งานในหน่วยงาน ชุมชน อุตสาหกรรม หรือนโยบาย..."></textarea>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">ข้อเสนอแนะเพื่อการพัฒนาหลักสูตร (ถ้ามี)</label>
                <textarea name="curriculum_suggestions" rows="2" class="form-control" placeholder="ข้อเสนอแนะในการปรับปรุงหลักสูตรการเรียนการสอน..."></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">ข้อเสนอแนะเพื่อการพัฒนาคณะ/หน่วยงาน (ถ้ามี)</label>
                <textarea name="faculty_suggestions" rows="2" class="form-control" placeholder="ข้อเสนอแนะเพื่อเชิงบริหาร..."></textarea>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">รายงานวิจัยฉบับสมบูรณ์ (ไฟล์ PDF) <span class="text-danger">*</span></label>
            <input type="file" name="file_report_pdf" required class="form-control" accept=".pdf">
        </div>

        <div class="mb-3 bg-light p-3 rounded border border-slate-100">
            <label class="form-label fw-bold mb-2">Checklist สำหรับการปิดโครงการ</label>
            <div class="form-check mb-1">
                <input class="form-check-input" type="checkbox" name="checklist_report_sent" value="1" id="chk1" required>
                <label class="form-check-label small" for="chk1">ส่งเล่มรายงานฉบับสมบูรณ์เรียบร้อยแล้ว</label>
            </div>
            <div class="form-check mb-1">
                <input class="form-check-input" type="checkbox" name="checklist_budget_cleared" value="1" id="chk2" required>
                <label class="form-check-label small" for="chk2">เคลียร์งบประมาณ/หลักฐานการจ่ายเงินเสร็จสิ้นแล้ว</label>
            </div>
            <div class="form-check mb-1">
                <input class="form-check-input" type="checkbox" name="checklist_outputs_registered" value="1" id="chk3" required>
                <label class="form-check-label small" for="chk3">จดทะเบียนผลงานตีพิมพ์/IP เรียบร้อยแล้ว</label>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
            <button type="button" onclick="submitFinalReport()" class="btn btn-dark fw-bold">ส่งรายงาน</button>
        </div>
    </form>
</div>

<script>
function submitFinalReport() {
    const form = document.getElementById('finalReportForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    const formData = new FormData(form);

    $.ajax({
        type: 'POST',
        url: 'ajax/projects/SaveFinalReport.php',
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
