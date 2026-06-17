<?php
// main/ajax/admin/AddModal.php - ฟอร์มเพิ่มข้อมูลหลัก (Admin AJAX Modal)
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');
if (!isLoggedIn()) { http_response_code(401); exit; }
requireRole('admin');

$tab = trim($_GET['tab'] ?? 'funders');
?>

<div class="modal-header">
    <h5 class="modal-title fw-bold">
        <i class="ri-add-circle-line me-2 text-primary"></i>
        <?php echo match($tab) {
            'funders' => 'เพิ่มแหล่งทุนวิจัย',
            'journals' => 'เพิ่มวารสารวิชาการ',
            'tiers' => 'เพิ่มเกณฑ์คะแนน (Tiers)',
            default => 'เพิ่มข้อมูลหลัก'
        }; ?>
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <form id="addMasterDataForm">
        <input type="hidden" name="tab" value="<?php echo htmlspecialchars($tab); ?>">
        
        <?php if ($tab === 'funders'): ?>
            <div class="mb-3">
                <label class="form-label fw-bold">ชื่อแหล่งทุนวิจัย <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required placeholder="เช่น ทุนสนับสนุนจากคณะวิชา">
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">ประเภทแหล่งทุน</label>
                    <select name="type" class="form-select">
                        <option value="Internal">Internal (ภายใน)</option>
                        <option value="External">External (ภายนอก)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">สถานะ</label>
                    <select name="status" class="form-select">
                        <option value="Active">Active (เปิดใช้งาน)</option>
                        <option value="Inactive">Inactive (ปิดใช้งาน)</option>
                    </select>
                </div>
            </div>

        <?php elseif ($tab === 'journals'): ?>
            <div class="mb-3">
                <label class="form-label fw-bold">ชื่อวารสารวิชาการ <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required placeholder="เช่น IEEE Transactions on Smart Grid">
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">ISSN</label>
                    <input type="text" name="issn" class="form-control" placeholder="เช่น 2450-123X">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Quartile</label>
                    <select name="quartile" class="form-select">
                        <option value="N/A">N/A</option>
                        <option value="Q1">Q1</option>
                        <option value="Q2">Q2</option>
                        <option value="Q3">Q3</option>
                        <option value="Q4">Q4</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">ฐานข้อมูลดัชนี (Database Index)</label>
                <select name="database_index" class="form-select">
                    <option value="Scopus">Scopus</option>
                    <option value="WoS">Web of Science (WoS)</option>
                    <option value="TCI">TCI กลุ่ม 1</option>
                    <option value="Scopus Proceeding">Scopus Proceeding</option>
                    <option value="Other">Other</option>
                </select>
            </div>

        <?php elseif ($tab === 'tiers'): ?>
            <div class="mb-3">
                <label class="form-label fw-bold">ประเภทเกณฑ์ประเมิน</label>
                <select name="category" class="form-select">
                    <option value="Journal">Journal (วารสาร)</option>
                    <option value="IP">IP (ทรัพย์สินทางปัญญา)</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">ชื่อระดับคะแนน (Level Name) <span class="text-danger">*</span></label>
                <input type="text" name="level_name" class="form-control" required placeholder="เช่น Q1 / ระดับนานาชาติ">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">คะแนนถ่วงน้ำหนัก (Points) <span class="text-danger">*</span></label>
                <input type="number" name="points" class="form-control" required step="0.1" min="0" value="1.0">
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">คำอธิบายรายละเอียด</label>
                <textarea name="description" class="form-control" rows="3" placeholder="อธิบายเกณฑ์ระดับคะแนนนี้..."></textarea>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
            <button type="button" onclick="submitMasterData()" class="btn btn-dark fw-bold">บันทึกข้อมูล</button>
        </div>
    </form>
</div>

<script>
function submitMasterData() {
    const form = document.getElementById('addMasterDataForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    const formData = new FormData(form);

    $.ajax({
        type: 'POST',
        url: 'ajax/admin/SaveMasterData.php',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res) {
            if (res.result === 1) {
                $('#mainModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'บันทึกสำเร็จ',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    GetTable();
                });
            } else {
                Swal.fire('ผิดพลาด', res.message, 'error');
            }
        },
        error: function() {
            Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์', 'error');
        }
    });
}
</script>
