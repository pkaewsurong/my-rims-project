<?php
// main/proposals.php
$pageTitle = 'ข้อเสนอโครงการ';
$pageCss   = 'proposals';
$pageJs    = 'proposals';
require 'header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="ri-file-text-line me-2"></i>ข้อเสนอโครงการ</h1>
        <nav aria-label="breadcrumb" class="mt-1">
            <ol class="breadcrumb mb-0" style="font-size:13px;">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">หน้าหลัก</a></li>
                <li class="breadcrumb-item active">ข้อเสนอโครงการ</li>
            </ol>
        </nav>
    </div>
    <div>
        <button class="btn btn-dark fw-bold" onclick="openProposalModal()">
            <i class="ri-add-line me-1"></i> ยื่นข้อเสนอใหม่
        </button>
    </div>
</div>

<!-- Filter Card -->
<div class="card mb-4" style="border-left: 4px solid #b9ff66;">
    <div class="card-body py-3">
        <div id="filter">
            <?php
            include 'ajax/proposals/GetFilter.php';
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
            $keyword = '';
            $status = '';
            $fundingId = '';
            include 'ajax/proposals/GetTable.php';
            ?>
        </div>
    </div>
</div>

<!-- AJAX Modal -->
<div class="modal fade" id="mainModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" id="showModal"></div>
    </div>
</div>

<?php require 'footer.php'; ?>
