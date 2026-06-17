<?php
// main/ajax/projects/GetFilter.php
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');

if (!isLoggedIn()) { http_response_code(401); exit; }

$mode = trim($_POST['mode'] ?? 'my');

// Fetch available statuses for dropdown - only project statuses are needed now
$statuses = [
    '' => 'ทั้งหมด',
    'ongoing' => 'กำลังดำเนินการ',
    'completed' => 'เสร็จสิ้น',
    'closed' => 'ปิดโครงการ',
];
?>
<div class="row g-3 align-items-end">
    <div class="col-md-6">
        <label class="form-label fw-semibold" for="keyword">ค้นหาโครงการ</label>
        <input type="text" class="form-control" id="keyword" placeholder="ชื่อโครงการ, รหัส, นักวิจัย...">
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold" for="filterStatus">สถานะ</label>
        <select class="form-select select2-filter" id="filterStatus">
            <?php foreach ($statuses as $val => $label): ?>
            <option value="<?php echo $val; ?>"><?php echo $label; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <button type="button" class="btn btn-dark w-100 fw-bold" onclick="GetTable()" id="searchBtn">
            <i class="ri-search-line me-1"></i> ค้นหา
        </button>
    </div>
</div>
