<?php
// main/ajax/metrics/GetFilter.php
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');
if (!isLoggedIn()) { http_response_code(401); exit; }

$currentYear = date('Y') + 543;
$years = range($currentYear, $currentYear - 5);
?>
<div class="row g-3 align-items-end">
    <div class="col-md-3">
        <label class="form-label fw-semibold" for="metricYear">ปีงบประมาณ</label>
        <select class="form-select select2-filter" id="metricYear">
            <?php foreach ($years as $y): ?>
            <option value="<?php echo $y; ?>" <?php echo $y === $currentYear ? 'selected' : ''; ?>>
                พ.ศ. <?php echo $y; ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold" for="metricKeyword">ค้นหานักวิจัย</label>
        <input type="text" class="form-control" id="metricKeyword" placeholder="ชื่อ-นามสกุล...">
    </div>
    <div class="col-md-2">
        <button type="button" class="btn btn-dark w-100 fw-bold" onclick="GetTable()">
            <i class="ri-search-line me-1"></i> ค้นหา
        </button>
    </div>
</div>
