<?php
// main/metrics.php
$pageTitle = 'ตัวชี้วัด (Metrics)';
$pageCss   = 'metrics';
$pageJs    = 'metrics';
require 'header.php';
?>
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="ri-bar-chart-box-line me-2"></i>ตัวชี้วัด (Metrics)</h1>
        <nav aria-label="breadcrumb" class="mt-1">
            <ol class="breadcrumb mb-0" style="font-size:13px;">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">หน้าหลัก</a></li>
                <li class="breadcrumb-item active">ตัวชี้วัด</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card mb-4" style="border-left:4px solid #b9ff66;">
    <div class="card-body py-3">
        <div id="filter">
            <div class="text-center py-3 text-muted"><div class="spinner-border spinner-border-sm me-2"></div> กำลังโหลด...</div>
        </div>
    </div>
</div>

<div class="card mb-4" id="loadingDiv" style="display:none;">
    <div class="card-body">
        <div class="d-flex flex-column align-items-center justify-content-center py-5 text-muted gap-3">
            <div class="spinner-border text-dark" style="width:2.5rem;height:2.5rem;border-width:3px;"></div>
            <span>กำลังโหลดข้อมูล...</span>
        </div>
    </div>
</div>

<div class="card" id="dataDiv" style="display:none;">
    <div class="card-body p-0"><div id="showTable"></div></div>
</div>

<?php require 'footer.php'; ?>
