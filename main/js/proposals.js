// main/js/proposals.js

$(document).ready(function () {
    // Init Select2 for existing filter selects
    $('#filter .select2-filter').select2({
        width: '100%',
        theme: 'bootstrap-5',
    });

    // Enter key triggers search
    $(document).on('keypress', '#propKeyword', function (e) {
        if (e.which === 13) GetTable();
    });

    // Initialize DataTable on the pre-rendered table
    initDataTable();
});

function initDataTable() {
    if ($.fn.DataTable.isDataTable('#proposalsTable')) {
        $('#proposalsTable').DataTable().destroy();
    }
    if ($('#proposalsTable').length && $('#proposalsTable tbody td[colspan]').length === 0) {
        $('#proposalsTable').DataTable({
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
        url: 'ajax/proposals/GetTable.php',
        data: {
            keyword:    $('#propKeyword').val() || '',
            status:     $('#propStatus').val() || '',
            funding_id: $('#propFunding').val() || '',
        },
        dataType: 'html',
        success: function (response) {
            $('#showTable').html(response);
            initDataTable();

            $('#loadingDiv').hide();
            $('#dataDiv').show();
        }
    });
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

// Review Proposal (Admin)
function reviewProposal(id) {
    $.ajax({
        type: 'GET',
        url: 'ajax/proposals/ReviewModal.php?id=' + id,
        dataType: 'html',
        success: function(response) {
            $('#showModal').html(response);
            $('#mainModal').modal('show');
        }
    });
}

// Delete Draft
async function deleteDraft(id) {
    const result = await Swal.fire({
        title: 'ลบข้อเสนอโครงการ',
        text: 'ข้อมูลทั้งหมดจะถูกลบอย่างถาวร',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ใช่, ลบเลย',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#ef4444',
    });

    if (result.isConfirmed) {
        $.ajax({
            type: 'POST',
            url: 'ajax/proposals/DeleteDraft.php',
            data: { id: id },
            dataType: 'json',
            success: function(res) {
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

// Approve Closure (Admin)
async function approveClosure(projectId) {
    const result = await Swal.fire({
        title: 'อนุมัติปิดโครงการ',
        text: 'คุณยืนยันที่จะอนุมัติการปิดโครงการนี้?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'อนุมัติ',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#191a23',
    });

    if (result.isConfirmed) {
        window.location.href = 'ajax/projects/ApproveClosure.php?id=' + projectId;
    }
}
