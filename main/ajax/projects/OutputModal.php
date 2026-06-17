<?php
// main/ajax/projects/OutputModal.php - ฟอร์มเพิ่มผลงานตีพิมพ์ (AJAX Modal)
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');
if (!isLoggedIn()) { http_response_code(401); exit; }

$project_id = (int)($_GET['project_id'] ?? 0);
if (!$project_id) { echo '<div class="alert alert-danger m-3">ไม่พบรหัสโครงการ</div>'; exit; }
?>

<div class="modal-header">
    <h5 class="modal-title fw-bold"><i class="ri-article-line me-2 text-primary"></i>เพิ่มผลงานตีพิมพ์ (Research Publication)</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <form id="publicationForm" enctype="multipart/form-data">
        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
        
        <div class="mb-3">
            <label class="form-label fw-bold">ชื่อผลงานตีพิมพ์ (ภาษาไทย) <span class="text-danger">*</span></label>
            <input type="text" name="title_th" required class="form-control" placeholder="ระบุชื่อภาษาไทย">
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">ชื่อผลงานตีพิมพ์ (ภาษาอังกฤษ)</label>
            <input type="text" name="title_en" class="form-control" placeholder="Publication Title in English">
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">ประเภทผลงาน</label>
                <select name="publication_type" class="form-select">
                    <option value="International Journal">International Journal</option>
                    <option value="National Journal">National Journal</option>
                    <option value="International Conference">International Conference</option>
                    <option value="National Conference">National Conference</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">ปีที่ตีพิมพ์ (ค.ศ.) <span class="text-danger">*</span></label>
                <input type="number" name="publish_year" required min="2000" max="2050" class="form-control" value="<?php echo date('Y'); ?>">
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-8">
                <label class="form-label fw-bold">ชื่อวารสาร/งานประชุมวิชาการ <span class="text-danger">*</span></label>
                <input type="text" name="journal_name" required class="form-control" placeholder="ระบุชื่อวารสารหรือชื่องานประชุม">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">ISSN / ISBN</label>
                <input type="text" name="issn" class="form-control" placeholder="เช่น 2450-123X">
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <label class="form-label fw-bold">Volume</label>
                <input type="text" name="volume" class="form-control" placeholder="เช่น 15">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Issue</label>
                <input type="text" name="issue" class="form-control" placeholder="เช่น 2">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">เลขหน้า (Page range)</label>
                <input type="text" name="page_length" class="form-control" placeholder="เช่น 120-135">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">ฐานข้อมูลการสืบค้น</label>
                <select name="indexing_database" class="form-select">
                    <option value="">-- ไม่ระบุ --</option>
                    <option value="Scopus">Scopus</option>
                    <option value="WoS">Web of Science</option>
                    <option value="TCI">TCI กลุ่ม 1</option>
                    <option value="TCI2">TCI กลุ่ม 2</option>
                    <option value="Other">อื่นๆ</option>
                </select>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Quartile (ถ้ามี)</label>
                <select name="quartile" class="form-select">
                    <option value="">-- ไม่ระบุ --</option>
                    <option value="Q1">Q1</option>
                    <option value="Q2">Q2</option>
                    <option value="Q3">Q3</option>
                    <option value="Q4">Q4</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Impact Factor</label>
                <input type="text" name="impact_factor" class="form-control" placeholder="เช่น 2.34">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">DOI / URL</label>
                <input type="url" name="doi_url" class="form-control" placeholder="เช่น https://doi.org/10.1000/xyz123">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">การนำไปใช้ประโยชน์ (Utilization Summary)</label>
            <textarea name="utilization_summary" rows="2" class="form-control" placeholder="อธิบายสั้นๆ ว่างานชิ้นนี้สามารถนำไปใช้ประโยชน์ในเชิงปฏิบัติอย่างไร..."></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">ไฟล์บทความฉบับเต็ม (Full text PDF)</label>
            <input type="file" name="file_full_text" class="form-control" accept=".pdf">
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
            <button type="button" onclick="submitPublication()" class="btn btn-dark fw-bold">บันทึก</button>
        </div>
    </form>
</div>

<script>
function submitPublication() {
    const form = document.getElementById('publicationForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    const formData = new FormData(form);

    $.ajax({
        type: 'POST',
        url: 'ajax/projects/SaveOutput.php',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res) {
            if (res.result === 1) {
                $('#mainModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'บันทึกข้อมูลผลงานสำเร็จ',
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
