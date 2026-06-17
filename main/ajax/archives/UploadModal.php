<?php
// main/ajax/archives/UploadModal.php - ฟอร์มอัปโหลดชุดข้อมูลวิจัย (AJAX Modal)
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');
if (!isLoggedIn()) { http_response_code(401); exit; }

$user_id = authUser()['id'];
$isAdmin = hasRole('admin');

// Fetch user's approved projects/proposals to link the archive to
if ($isAdmin) {
    $stmt = $pdo->query('
        SELECT p.id as project_id, pr.id as proposal_id, pr.title, p.code 
        FROM projects p 
        JOIN proposals pr ON p.proposal_id = pr.id 
        ORDER BY pr.title
    ');
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $pdo->prepare('
        SELECT p.id as project_id, pr.id as proposal_id, pr.title, p.code 
        FROM projects p 
        JOIN proposals pr ON p.proposal_id = pr.id 
        WHERE pr.user_id = ? 
        ORDER BY pr.title
    ');
    $stmt->execute([$user_id]);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="modal-header">
    <h5 class="modal-title fw-bold"><i class="ri-upload-cloud-line me-2 text-primary"></i>อัปโหลดข้อมูลเข้าคลังวิจัย</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <form id="archiveUploadForm" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label fw-bold">เชื่อมโยงกับโครงการวิจัย <span class="text-danger">*</span></label>
            <select name="proposal_id" class="form-select select2-modal" required style="width: 100%;">
                <option value="">-- เลือกโครงการวิจัย --</option>
                <?php foreach ($projects as $proj): ?>
                    <option value="<?php echo $proj['proposal_id']; ?>">
                        [<?php echo htmlspecialchars($proj['code']); ?>] <?php echo htmlspecialchars($proj['title']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">ชื่อชุดข้อมูล (Dataset Name) <span class="text-danger">*</span></label>
            <input type="text" name="dataset_name" class="form-control" required placeholder="ระบุชื่อชุดข้อมูลหรือชื่อไฟล์อธิบาย">
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">ประเภทข้อมูล (Data Type)</label>
                <input type="text" name="data_type" class="form-control" placeholder="เช่น CSV, JSON, ZIP, Images">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">ระดับการเข้าถึงข้อมูล (Access Level)</label>
                <select name="access_level" class="form-select">
                    <option value="private">ส่วนตัว (Private)</option>
                    <option value="restricted">จำกัดเฉพาะในองค์กร (Restricted)</option>
                    <option value="public">สาธารณะ (Public)</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">คำอธิบายรายละเอียดชุดข้อมูล</label>
            <textarea name="description" class="form-control" rows="3" placeholder="ระบุรายละเอียดเกี่ยวกับชุดข้อมูล แหล่งที่มา หรือวิธีการเก็บข้อมูล..."></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">เลือกไฟล์อัปโหลด <span class="text-danger">*</span></label>
            <input type="file" name="file_path" class="form-control" required>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
            <button type="button" onclick="submitArchive()" class="btn btn-dark fw-bold">อัปโหลดไฟล์</button>
        </div>
    </form>
</div>

<script>
$(document).ready(function () {
    $('.select2-modal').select2({
        dropdownParent: $('#mainModal'),
        theme: 'bootstrap-5'
    });
});

function submitArchive() {
    const form = document.getElementById('archiveUploadForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    const formData = new FormData(form);

    $.ajax({
        type: 'POST',
        url: 'ajax/archives/SaveArchive.php',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res) {
            if (res.result === 1) {
                $('#mainModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'อัปโหลดข้อมูลสำเร็จ',
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
