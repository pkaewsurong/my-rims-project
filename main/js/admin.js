// main/js/admin.js
$(document).ready(function () { GetFilter(); });

function GetFilter() {
    $.ajax({ type:'POST', url:'ajax/admin/GetFilter.php', dataType:'html',
        success: function(r) {
            $('#filter').html(r);
            $('#filter .select2-filter').select2({ width:'100%', theme:'bootstrap-5' });
            $('#adminTab').on('change', function() { GetTable(); });
            $(document).on('keypress', '#adminKeyword', function(e) { if (e.which===13) GetTable(); });
            GetTable();
        }
    });
}

function GetTable() {
    $('#loadingDiv').show(); $('#dataDiv').hide();
    $.ajax({ type:'POST', url:'ajax/admin/GetTable.php',
        data: { tab: $('#adminTab').val() || 'funders', keyword: $('#adminKeyword').val() || '' },
        dataType:'html',
        success: function(r) {
            if ($.fn.DataTable.isDataTable('#adminTable')) $('#adminTable').DataTable().destroy();
            $('#showTable').html(r);
            if ($('#adminTable').length && $('#adminTable tbody td[colspan]').length === 0) {
                $('#adminTable').DataTable({ ordering:true, pageLength:25, language:{ url:'https://cdn.datatables.net/plug-ins/2.0.8/i18n/th.json' } });
            }
            $('#loadingDiv').hide(); $('#dataDiv').show();
        }
    });
}

function openAddModal() {
    const tab = $('#adminTab').val() || 'funders';
    $('#showModal').html('<div class="d-flex justify-content-center py-5"><div class="spinner-border text-dark"></div></div>');
    $('#mainModal').modal('show');
    $.ajax({ type:'GET', url:'ajax/admin/AddModal.php?tab='+tab, dataType:'html',
        success: function(r) { $('#showModal').html(r); }
    });
}
