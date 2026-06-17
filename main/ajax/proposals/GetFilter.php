<?php
// main/ajax/proposals/GetFilter.php
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');
if (!isLoggedIn()) { http_response_code(401); exit; }

// Fetch funding sources for dropdown
$stmt = $pdo->query("SELECT id, name FROM funding_sources ORDER BY name");
$fundingSources = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="row g-3 align-items-end">
    <div class="col-md-4">
        <label class="form-label fw-semibold" for="propKeyword">ค้นหา</label>
        <input type="text" class="form-control" id="propKeyword" placeholder="ชื่อข้อเสนอ, นักวิจัย...">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold" for="propStatus">สถานะ</label>
        <select class="form-select select2-filter" id="propStatus">
            <option value="">ทั้งหมด</option>
            <option value="draft">แบบร่าง</option>
            <option value="submitted">รอพิจารณา</option>
            <option value="under_review">อยู่ระหว่างพิจารณา</option>
            <option value="needs_revision">ให้กลับไปแก้ไข</option>
            <option value="approved">อนุมัติแล้ว</option>
            <option value="rejected">ไม่อนุมัติ</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold" for="propFunding">แหล่งทุน</label>
        <select class="form-select select2-filter" id="propFunding">
            <option value="">ทั้งหมด</option>
            <?php foreach ($fundingSources as $fs): ?>
            <option value="<?php echo $fs['id']; ?>"><?php echo htmlspecialchars($fs['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <button type="button" class="btn btn-dark w-100 fw-bold" onclick="GetTable()">
            <i class="ri-search-line me-1"></i> ค้นหา
        </button>
    </div>
</div>
