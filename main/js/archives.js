// main/js/archives.js
$(document).ready(function () { GetFilter(); });

function GetFilter() {
    $.ajax({ type:'POST', url:'ajax/archives/GetFilter.php', dataType:'html',
        success: function(r) {
            $('#filter').html(r);
            $('#filter .select2-filter').select2({ width:'100%', theme:'bootstrap-5' });
            $(document).on('keypress', '#archKeyword', function(e) { if (e.which===13) GetTable(); });
            GetTable();
        }
    });
}

function GetTable() {
    $('#loadingDiv').show(); $('#dataDiv').hide();
    $.ajax({ type:'POST', url:'ajax/archives/GetTable.php',
        data: {
            keyword:    $('#archKeyword').val() || '',
            project_id: $('#archProject').val() || '',
            access:     $('#archAccess').val() || '',
        },
        dataType:'html',
        success: function(r) {
            if ($.fn.DataTable.isDataTable('#archivesTable')) $('#archivesTable').DataTable().destroy();
            $('#showTable').html(r);
            if ($('#archivesTable').length && $('#archivesTable tbody td[colspan]').length === 0) {
                $('#archivesTable').DataTable({ ordering:false, pageLength:25, language:{ url:'https://cdn.datatables.net/plug-ins/2.0.8/i18n/th.json' } });
            }
            $('#loadingDiv').hide(); $('#dataDiv').show();
        }
    });
}

function openUploadModal() {
    $('#showModal').html('<div class="d-flex justify-content-center py-5"><div class="spinner-border text-dark"></div></div>');
    $('#mainModal').modal('show');
    $.ajax({ type:'GET', url:'ajax/archives/UploadModal.php', dataType:'html',
        success: function(r) { $('#showModal').html(r); }
    });
}
