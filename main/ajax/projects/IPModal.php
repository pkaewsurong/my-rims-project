<?php
// main/ajax/projects/IPModal.php - ฟอร์มเพิ่ม IP (AJAX Modal)
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');
if (!isLoggedIn()) { http_response_code(401); exit; }

$project_id = (int)($_GET['project_id'] ?? 0);
if (!$project_id) { echo '<div class="alert alert-danger m-3">ไม่พบรหัสโครงการ</div>'; exit; }
?>

<div class="modal-header">
    <h5 class="modal-title fw-bold"><i class="ri-copyright-line me-2 text-primary"></i>จดทะเบียนทรัพย์สินทางปัญญา (IP Asset)</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <form id="ipForm" enctype="multipart/form-data">
        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
        
        <div class="mb-3">
            <label class="form-label fw-bold">ชื่อผลงานทรัพย์สินทางปัญญา (ภาษาไทย) <span class="text-danger">*</span></label>
            <input type="text" name="name_th" required class="form-control" placeholder="ระบุชื่อภาษาไทย">
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">ชื่อผลงานทรัพย์สินทางปัญญา (ภาษาอังกฤษ)</label>
            <input type="text" name="name_en" class="form-control" placeholder="IP Asset Name in English">
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">ประเภททรัพย์สินทางปัญญา</label>
                <select name="ip_type" class="form-select">
                    <option value="Patent">สิทธิบัตร (Patent)</option>
                    <option value="Copyright">ลิขสิทธิ์ (Copyright)</option>
                    <option value="Creative">ผลงานสร้างสรรค์อื่นๆ (Creative)</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">วันที่สร้างสรรค์เสร็จ <span class="text-danger">*</span></label>
                <input type="date" name="completion_date" required class="form-control" value="<?php echo date('Y-m-d'); ?>">
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">สถานะทางกฎหมาย</label>
                <input type="text" name="legal_status" class="form-control" placeholder="เช่น ยื่นคำขอจดทะเบียนแล้ว, ได้รับความคุ้มครองแล้ว" value="ยื่นคำขอจดทะเบียนแล้ว">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">หมายเลขคำขอ/เลขทะเบียน</label>
                <input type="text" name="registration_number" class="form-control" placeholder="เช่น 2101004562">
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">หน่วยงานผู้รับจดทะเบียน</label>
                <input type="text" name="registration_agency" class="form-control" placeholder="เช่น กรมทรัพย์สินทางปัญญา" value="กรมทรัพย์สินทางปัญญา">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">วันที่ได้รับอนุมัติ (ถ้ามี)</label>
                <input type="date" name="approval_date" class="form-control">
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">มูลค่าเชิงพาณิชย์/มูลค่าทางเศรษฐกิจ (บาท)</label>
                <input type="number" name="economic_value" class="form-control" placeholder="0.00" value="0" min="0" step="0.01">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">คำสำคัญ (Keywords)</label>
                <input type="text" name="keywords" class="form-control" placeholder="เช่น IoT, เกษตรอัจฉริยะ">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">บทสรุปรายละเอียดผลงาน (Abstract details)</label>
            <textarea name="abstract_details" rows="3" class="form-control" placeholder="ระบุรายละเอียดทางเทคนิคของงานประดิษฐ์หรือคำอธิบายอย่างย่อ..."></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">ไฟล์ใบรับรอง/เอกสารสำคัญ (Certificate PDF)</label>
            <input type="file" name="file_certificate" class="form-control" accept=".pdf">
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
            <button type="button" onclick="submitIP()" class="btn btn-dark fw-bold">บันทึก</button>
        </div>
    </form>
</div>

<script>
function submitIP() {
    const form = document.getElementById('ipForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    const formData = new FormData(form);

    $.ajax({
        type: 'POST',
        url: 'ajax/projects/SaveIP.php',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res) {
            if (res.result === 1) {
                $('#mainModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'บันทึกข้อมูล IP สำเร็จ',
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
