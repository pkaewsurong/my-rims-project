// main/js/projects_all.js - All Projects Page (Admin view, same pattern)
$(document).ready(function () { GetFilter(); });

function GetFilter() {
    $.ajax({ type:'POST', url:'ajax/projects/GetFilter.php', data: { mode: 'all' }, dataType:'html',
        success: function(r) {
            $('#filter').html(r);
            $('#filter .select2-filter').select2({ width:'100%', theme:'bootstrap-5' });
            $(document).on('keypress', '#keyword', function(e) { if (e.which===13) GetTable(); });
            GetTable();
        }
    });
}

function GetTable() {
    $('#loadingDiv').show(); $('#dataDiv').hide();
    $.ajax({ type:'POST', url:'ajax/projects/GetTable.php',
        data: { mode: 'all', keyword: $('#keyword').val()||'', status: $('#filterStatus').val()||'', type: $('#filterType').val()||'' },
        dataType:'html',
        success: function(r) {
            if ($.fn.DataTable.isDataTable('#projectsTable')) $('#projectsTable').DataTable().destroy();
            $('#showTable').html(r);
            if ($('#projectsTable').length && $('#projectsTable tbody td[colspan]').length === 0) {
                $('#projectsTable').DataTable({ ordering:false, pageLength:25, language:{ url:'https://cdn.datatables.net/plug-ins/2.0.8/i18n/th.json' } });
            }
            $('#loadingDiv').hide(); $('#dataDiv').show();
        }
    });
}

async function requestClosure(id, progress, reportSubmitted) {
    if (progress < 100) {
        Swal.fire({ icon:'warning', title:'ความก้าวหน้าไม่ครบ', html:'รายงานยังไม่ครบ 100%<br><small>ปัจจุบัน: '+progress+'%</small>' });
        return;
    }
    if (!reportSubmitted) {
        Swal.fire({ icon:'warning', title:'ยังไม่มีรายงานฉบับสมบูรณ์', text:'กรุณาส่ง Final Report ก่อน' });
        return;
    }
    const res = await Swal.fire({ title:'เสนอขอปิดโครงการ', text:'ยืนยันการส่งคำขอ?', icon:'question',
        showCancelButton:true, confirmButtonText:'ยืนยัน', cancelButtonText:'ยกเลิก', confirmButtonColor:'#191a23' });
    if (res.isConfirmed) window.location.href = 'ajax/projects/RequestClosure.php?id=' + id;
}

async function deleteDraft(id) {
    const res = await Swal.fire({ title:'ลบข้อเสนอ', text:'ลบอย่างถาวร?', icon:'warning',
        showCancelButton:true, confirmButtonText:'ลบ', cancelButtonText:'ยกเลิก', confirmButtonColor:'#ef4444' });
    if (res.isConfirmed) {
        $.post('ajax/projects/DeleteDraft.php', {proposal_id:id}, function(r) {
            if (r.result===1) { Swal.fire({icon:'success',title:'ลบสำเร็จ',timer:1500,showConfirmButton:false}); GetTable(); }
            else { Swal.fire({icon:'error',title:'ผิดพลาด',text:r.message}); }
        }, 'json');
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

