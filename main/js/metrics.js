// main/js/metrics.js
$(document).ready(function () { GetFilter(); });

function GetFilter() {
    $.ajax({ type:'POST', url:'ajax/metrics/GetFilter.php', dataType:'html',
        success: function(r) {
            $('#filter').html(r);
            $('#filter .select2-filter').select2({ width:'100%', theme:'bootstrap-5' });
            $(document).on('keypress', '#metricKeyword', function(e) { if (e.which===13) GetTable(); });
            GetTable();
        }
    });
}

function GetTable() {
    $('#loadingDiv').show(); $('#dataDiv').hide();
    $.ajax({ type:'POST', url:'ajax/metrics/GetTable.php',
        data: { year: $('#metricYear').val() || '', keyword: $('#metricKeyword').val() || '' },
        dataType:'html',
        success: function(r) {
            if ($.fn.DataTable.isDataTable('#metricsTable')) $('#metricsTable').DataTable().destroy();
            $('#showTable').html(r);
            if ($('#metricsTable').length && $('#metricsTable tbody td[colspan]').length === 0) {
                $('#metricsTable').DataTable({ ordering:true, pageLength:25, language:{ url:'https://cdn.datatables.net/plug-ins/2.0.8/i18n/th.json' } });
            }
            $('#loadingDiv').hide(); $('#dataDiv').show();
        }
    });
}
