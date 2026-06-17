<?php
// main/ajax/proposals/ReviewModal.php - ฟอร์มประเมินข้อเสนอ (Admin AJAX Modal)
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');
if (!isLoggedIn()) { http_response_code(401); exit; }
requireRole('admin');

$proposal_id = (int)($_GET['id'] ?? 0);
if (!$proposal_id) { echo '<div class="alert alert-danger m-3">ไม่พบรหัสข้อเสนอโครงการ</div>'; exit; }

$stmt = $pdo->prepare('SELECT p.*, u.name as leader_name FROM proposals p JOIN users u ON p.user_id = u.id WHERE p.id = ?');
$stmt->execute([$proposal_id]);
$proposal = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$proposal) { echo '<div class="alert alert-danger m-3">ไม่พบข้อมูลข้อเสนอโครงการ</div>'; exit; }

// Fetch existing review if any
$reviewer_id = authUser()['id'];
$reviewStmt = $pdo->prepare('SELECT * FROM project_reviews WHERE proposal_id = ? AND reviewer_id = ?');
$reviewStmt->execute([$proposal_id, $reviewer_id]);
$review = $reviewStmt->fetch(PDO::FETCH_ASSOC);

$score_concept = $review['score_concept'] ?? 0;
$score_team = $review['score_team'] ?? 0;
$score_alignment = $review['score_alignment'] ?? 0;
$score_impact = $review['score_impact'] ?? 0;
$total_score = $score_concept + $score_team + $score_alignment + $score_impact;
?>

<div class="modal-header">
    <h5 class="modal-title fw-bold"><i class="ri-check-double-line me-2 text-primary"></i>ประเมินข้อเสนอโครงการ</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
    <div class="alert alert-light border border-slate-200 small mb-4">
        <strong>โครงการ:</strong> <?php echo htmlspecialchars($proposal['title']); ?><br>
        <strong>หัวหน้าโครงการ:</strong> <?php echo htmlspecialchars($proposal['leader_name']); ?><br>
        <strong>งบประมาณเสนอขอ:</strong> <?php echo number_format($proposal['budget_total'] ?? 0); ?> บาท
    </div>

    <form id="reviewForm">
        <input type="hidden" name="proposal_id" value="<?php echo $proposal_id; ?>">

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-bold">1. ด้านแนวคิดและความสำคัญ (Concept & Significance) <span class="text-muted">(0-25 คะแนน)</span></label>
                <input type="number" name="score_concept" id="score_concept" min="0" max="25" class="form-control score-input" value="<?php echo $score_concept; ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">2. ด้านความพร้อมทีมงานวิจัย (Research Team Readiness) <span class="text-muted">(0-25 คะแนน)</span></label>
                <input type="number" name="score_team" id="score_team" min="0" max="25" class="form-control score-input" value="<?php echo $score_team; ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">3. ด้านความสอดคล้องยุทธศาสตร์ (Strategic Alignment) <span class="text-muted">(0-25 คะแนน)</span></label>
                <input type="number" name="score_alignment" id="score_alignment" min="0" max="25" class="form-control score-input" value="<?php echo $score_alignment; ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">4. ด้านผลกระทบโครงการวิจัย (Impact & Application) <span class="text-muted">(0-25 คะแนน)</span></label>
                <input type="number" name="score_impact" id="score_impact" min="0" max="25" class="form-control score-input" value="<?php echo $score_impact; ?>" required>
            </div>
        </div>

        <div class="p-3 mb-4 bg-light rounded border border-slate-100 text-center">
            <span class="fs-5 fw-bold text-dark">คะแนนรวมทั้งหมด: <span id="lblTotalScore" class="text-success"><?php echo $total_score; ?></span> / 100 คะแนน</span>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">จุดเด่น/จุดแข็งของโครงการ</label>
            <textarea name="comments_strengths" class="form-control" rows="2" placeholder="จุดแข็งของข้อเสนอวิจัยชิ้นนี้..."><?php echo htmlspecialchars($review['comments_strengths'] ?? ''); ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">ข้อเสนอแนะเพื่อปรับปรุง/แนวทางแก้ไข</label>
            <textarea name="comments_suggestions" class="form-control" rows="2" placeholder="ข้อแนะนำจากกรรมการผู้ประเมิน..."><?php echo htmlspecialchars($review['comments_suggestions'] ?? ''); ?></textarea>
        </div>

        <div class="row g-3 align-items-center mb-4">
            <div class="col-md-6">
                <label class="form-label fw-bold">ผลการตัดสินพิจารณา <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="under_review" <?php echo ($review['status'] ?? '') === 'under_review' ? 'selected' : ''; ?>>อยู่ระหว่างการประเมิน (Under Review)</option>
                    <option value="needs_revision" <?php echo ($review['status'] ?? '') === 'needs_revision' ? 'selected' : ''; ?>>ให้กลับไปแก้ไข (Needs Revision)</option>
                    <option value="approved" <?php echo ($review['status'] ?? '') === 'approved' ? 'selected' : ''; ?>>อนุมัติโครงการ (Approved)</option>
                    <option value="rejected" <?php echo ($review['status'] ?? '') === 'rejected' ? 'selected' : ''; ?>>ปฏิเสธ/ไม่อนุมัติ (Rejected)</option>
                </select>
            </div>
            <div class="col-md-6 pt-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_draft" value="1" id="chkDraftReview" <?php echo ($review['status'] ?? 'under_review') === 'under_review' ? 'checked' : ''; ?>>
                    <label class="form-check-label fw-bold text-muted" for="chkDraftReview">
                        บันทึกเป็นแบบร่างการประเมินชั่วคราว
                    </label>
                </div>
            </div>
        </div>

        <div class="mb-3 mt-3" id="revisionCommentGroup" style="display: none;">
            <label class="form-label fw-bold text-danger">สิ่งที่ควรแก้ไข / รายละเอียดการปรับปรุงโครงการ <span class="text-danger">*</span></label>
            <textarea name="revision_comment" id="revision_comment" class="form-control border-danger" rows="3" placeholder="ระบุรายละเอียดสิ่งที่นักวิจัยต้องทำการแก้ไขปรับปรุง..."><?php echo htmlspecialchars($proposal['revision_comment'] ?? ''); ?></textarea>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
            <button type="button" onclick="submitReview()" class="btn btn-dark fw-bold"><i class="ri-save-line me-1"></i>บันทึกผลการประเมิน</button>
        </div>
    </form>
</div>

<script>
$(document).ready(function () {
    function toggleRevisionGroup() {
        const status = $('select[name="status"]').val();
        if (status === 'needs_revision') {
            $('#revisionCommentGroup').show();
            $('#revision_comment').prop('required', true);
        } else {
            $('#revisionCommentGroup').hide();
            $('#revision_comment').prop('required', false);
        }

        if (status !== 'under_review') {
            $('#chkDraftReview').prop('checked', false);
        }
    }

    $('select[name="status"]').on('change', toggleRevisionGroup);
    
    $('#chkDraftReview').on('change', function() {
        if ($(this).is(':checked')) {
            $('select[name="status"]').val('under_review').trigger('change');
        }
    });

    toggleRevisionGroup(); // run on load

    $('.score-input').on('input', function () {
        let total = 0;
        $('.score-input').each(function () {
            let v = parseInt($(this).val()) || 0;
            if (v < 0) v = 0;
            if (v > 25) v = 25;
            $(this).val(v);
            total += v;
        });
        $('#lblTotalScore').text(total);
    });
});

function submitReview() {
    const form = document.getElementById('reviewForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    const formData = new FormData(form);

    $.ajax({
        type: 'POST',
        url: 'ajax/proposals/StoreReview.php',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res) {
            if (res.result === 1) {
                $('#mainModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'บันทึกการประเมินสำเร็จ',
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
