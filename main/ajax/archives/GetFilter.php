<?php
// main/ajax/archives/GetFilter.php
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');
if (!isLoggedIn()) { http_response_code(401); exit; }

// Fetch projects for dropdown
$user_id = authUser()['id'];
$stmt = $pdo->prepare("
    SELECT p.id, pr.title
    FROM projects p JOIN proposals pr ON p.proposal_id = pr.id
    WHERE pr.user_id = ? ORDER BY pr.title
");
$stmt->execute([$user_id]);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="row g-3 align-items-end">
    <div class="col-md-4">
        <label class="form-label fw-semibold" for="archKeyword">ค้นหา</label>
        <input type="text" class="form-control" id="archKeyword" placeholder="ชื่อชุดข้อมูล...">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold" for="archProject">โครงการ</label>
        <select class="form-select select2-filter" id="archProject">
            <option value="">ทั้งหมด</option>
            <?php foreach ($projects as $p): ?>
            <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['title']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold" for="archAccess">ระดับการเข้าถึง</label>
        <select class="form-select select2-filter" id="archAccess">
            <option value="">ทั้งหมด</option>
            <option value="public">สาธารณะ</option>
            <option value="restricted">จำกัด</option>
            <option value="private">ส่วนตัว</option>
        </select>
    </div>
    <div class="col-md-2 ms-auto">
        <button type="button" class="btn btn-dark w-100 fw-bold" onclick="GetTable()">
            <i class="ri-search-line me-1"></i> ค้นหา
        </button>
    </div>
</div>
