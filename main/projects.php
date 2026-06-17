<?php
// main/projects.php - โครงการของฉัน
$pageTitle = 'โครงการของฉัน';
$pageCss   = 'projects';
$pageJs    = 'projects';
require 'header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="ri-folder-line me-2"></i>โครงการของฉัน</h1>
        <nav aria-label="breadcrumb" class="mt-1">
            <ol class="breadcrumb mb-0" style="font-size:13px;">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">หน้าหลัก</a></li>
                <li class="breadcrumb-item active">โครงการของฉัน</li>
            </ol>
        </nav>
    </div>

</div>

<!-- Filter Card -->
<div class="card mb-4" id="filterCard">
    <div class="card-body py-3">
        <div id="filter">
            <?php
            $mode = 'my';
            include 'ajax/projects/GetFilter.php';
            ?>
        </div>
    </div>
</div>

<!-- Loading -->
<div class="card mb-4" id="loadingDiv" style="display:none;">
    <div class="card-body">
        <div class="d-flex flex-column align-items-center justify-content-center py-5 text-muted gap-3">
            <div class="spinner-border text-dark" style="width:2.5rem;height:2.5rem;border-width:3px;"></div>
            <span>กำลังโหลดข้อมูล...</span>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card" id="dataDiv">
    <div class="card-body p-0">
        <div id="showTable">
            <?php
            $mode = 'my';
            $keyword = '';
            $status = '';
            include 'ajax/projects/GetTable.php';
            ?>
        </div>
    </div>
</div>

<!-- Modal สำหรับ AJAX forms -->
<div class="modal fade" id="mainModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" id="showModal"></div>
    </div>
</div>

<?php require 'footer.php'; ?>
