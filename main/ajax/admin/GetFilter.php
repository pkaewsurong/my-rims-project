<?php
// main/ajax/admin/GetFilter.php
session_start();
require_once('../../../config/database.php');
require_once('../../../includes/functions.php');
if (!isLoggedIn() || (!hasRole('admin') && !hasRole('research_admin'))) { http_response_code(403); exit; }
?>
<div class="row g-3 align-items-end">
    <div class="col-md-3">
        <label class="form-label fw-semibold" for="adminTab">หมวดหมู่</label>
        <select class="form-select select2-filter" id="adminTab">
            <option value="funders">แหล่งทุน (Funders)</option>
            <option value="journals">ฐานวารสาร (Journals)</option>
            <option value="tiers">เกณฑ์การให้คะแนน (Tiers)</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold" for="adminKeyword">ค้นหา</label>
        <input type="text" class="form-control" id="adminKeyword" placeholder="ค้นหา...">
    </div>
    <div class="col-md-2">
        <button type="button" class="btn btn-dark w-100 fw-bold" onclick="GetTable()">
            <i class="ri-search-line me-1"></i> ค้นหา
        </button>
    </div>
    <div class="col-md-3 text-end">
        <button type="button" class="btn btn-success fw-bold" onclick="openAddModal()">
            <i class="ri-add-line me-1"></i> เพิ่มรายการ
        </button>
    </div>
</div>
