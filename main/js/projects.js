// main/js/projects.js

$(document).ready(function () {
    // Init Select2 for existing filter selects
    $('#filter .select2-filter').select2({
        width: '100%',
        theme: 'bootstrap-5',
    });

    // Enter key triggers search
    $(document).on('keypress', '#keyword', function (e) {
        if (e.which === 13) GetTable();
    });

    // Initialize DataTable on the pre-rendered table
    initDataTable();
});

function initDataTable() {
    if ($.fn.DataTable.isDataTable('#projectsTable')) {
        $('#projectsTable').DataTable().destroy();
    }
    if ($('#projectsTable').length && $('#projectsTable tbody td[colspan]').length === 0) {
        $('#projectsTable').DataTable({
            ordering: false,
            pageLength: 25,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/th.json'
            },
            responsive: true,
        });
    }
}

function GetTable() {
    $('#loadingDiv').show();
    $('#dataDiv').hide();

    $.ajax({
        type: 'POST',
        url: 'ajax/projects/GetTable.php',
        data: {
            mode:    'my',
            keyword: $('#keyword').val() || '',
            status:  $('#filterStatus').val() || '',
            type:    'project',
        },
        dataType: 'html',
        success: function (response) {
            $('#showTable').html(response);
            initDataTable();

            $('#loadingDiv').hide();
            $('#dataDiv').show();
        },
        error: function (xhr, status, error) {
            $('#loadingDiv').hide();
            $('#dataDiv').show();
            $('#showTable').html(
                '<div class="alert alert-danger d-flex align-items-center gap-2">' +
                '<i class="ri-error-warning-line fs-5"></i>' +
                'เกิดข้อผิดพลาดในการโหลดข้อมูล: ' + error +
                '</div>'
            );
        }
    });
}

// Request project closure
async function requestClosure(id, progress, reportSubmitted) {
    if (progress < 100) {
        Swal.fire({
            icon: 'warning',
            title: 'ความก้าวหน้าไม่ครบ',
            html: 'รายงานความก้าวหน้ายังไม่ครบ 100%<br><small class="text-muted">ปัจจุบัน: ' + progress + '%</small>',
        });
        return;
    }
    if (!reportSubmitted) {
        Swal.fire({
            icon: 'warning',
            title: 'ยังไม่มีรายงานฉบับสมบูรณ์',
            text: 'กรุณาส่งรายงานฉบับสมบูรณ์ (Final Report) ก่อนขอปิดโครงการ',
        });
        return;
    }

    const result = await Swal.fire({
        title: 'เสนอขอปิดโครงการ',
        text: 'คุณแน่ใจหรือไม่? ระบบจะส่งคำขอให้ผู้ดูแลอนุมัติ',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#191a23',
        cancelButtonColor: '#6c757d',
    });

    if (result.isConfirmed) {
        window.location.href = 'ajax/projects/RequestClosure.php?id=' + id;
    }
}

// Delete draft proposal
async function deleteDraft(id) {
    const result = await Swal.fire({
        title: 'ลบโครงการแบบร่าง',
        text: 'ข้อมูลทั้งหมดจะถูกลบอย่างถาวร ไม่สามารถเรียกคืนได้',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ใช่, ลบเลย',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6c757d',
    });

    if (result.isConfirmed) {
        $.ajax({
            type: 'POST',
            url: 'ajax/projects/DeleteDraft.php',
            data: { proposal_id: id },
            dataType: 'json',
            success: function (res) {
                if (res.result === 1) {
                    Swal.fire({ icon: 'success', title: 'ลบสำเร็จ', timer: 1500, showConfirmButton: false });
                    GetTable();
                } else {
                    Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: res.message });
                }
            }
        });
    }
}

// Open Create/Edit Modal
function openProposalModal(id = 0) {
    const url = id ? 'ajax/proposals/CreateModal.php?id=' + id : 'ajax/proposals/CreateModal.php';
    $('#showModal').html(
        '<div class="d-flex justify-content-center align-items-center py-5">' +
        '<div class="spinner-border text-dark"></div>' +
        '</div>'
    );
    $('#mainModal').modal('show');

    $.ajax({
        type: 'GET',
        url: url,
        dataType: 'html',
        success: function(response) {
            $('#showModal').html(response);
        }
    });
}

function showProjectDetail(id) {
    $('#showModal').html(
        '<div class="d-flex justify-content-center align-items-center py-5">' +
        '<div class="spinner-border text-dark"></div>' +
        '</div>'
    );
    $('#mainModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'ajax/projects/DetailModal.php?id=' + id,
        dataType: 'html',
        success: function(response) {
            $('#showModal').html(response);
        },
        error: function() {
            $('#showModal').html('<div class="alert alert-danger m-3">เกิดข้อผิดพลาดในการโหลดรายละเอียดโครงการ</div>');
        }
    });
}

function showProposalDetail(id) {
    $('#showModal').html(
        '<div class="d-flex justify-content-center align-items-center py-5">' +
        '<div class="spinner-border text-dark"></div>' +
        '</div>'
    );
    $('#mainModal').modal('show');
    $.ajax({
        type: 'GET',
        url: 'ajax/proposals/DetailModal.php?id=' + id,
        dataType: 'html',
        success: function(response) {
            $('#showModal').html(response);
        },
        error: function() {
            $('#showModal').html('<div class="alert alert-danger m-3">เกิดข้อผิดพลาดในการโหลดรายละเอียดข้อเสนอโครงการ</div>');
        }
    });
}

