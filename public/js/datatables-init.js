/**
 * DataTables initialization for all report tables
 * Provides export functionality: Copy, Excel, CSV, PDF, Print
 * Handles both static and dynamically loaded tables
 */
$(document).ready(function() {
    // Common button configuration - matching project spendings page
    var commonButtons = [
        {
            extend: 'copy',
            className: 'btn btn-secondary btn-sm',
            text: '<i class="bi bi-clipboard me-1"></i> Copy'
        },
        {
            extend: 'excel',
            className: 'btn btn-success btn-sm',
            text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel'
        },
        {
            extend: 'csv',
            className: 'btn btn-info btn-sm',
            text: '<i class="bi bi-file-earmark-text me-1"></i> CSV'
        },
        {
            extend: 'pdf',
            className: 'btn btn-danger btn-sm',
            text: '<i class="bi bi-file-earmark-pdf me-1"></i> PDF'
        },
        {
            extend: 'print',
            className: 'btn btn-warning btn-sm',
            text: '<i class="bi bi-printer me-1"></i> Print'
        }
    ];

    // Common language configuration
    var commonLanguage = {
        search: '<span class="me-2">Search:</span>',
        lengthMenu: '<span class="me-2">Show:</span> _MENU_ entries',
        info: 'Showing _START_ to _END_ of _TOTAL_ entries',
        paginate: {
            first: '<i class="bi bi-chevron-double-left"></i>',
            last: '<i class="bi bi-chevron-double-right"></i>',
            next: '<i class="bi bi-chevron-right"></i>',
            previous: '<i class="bi bi-chevron-left"></i>'
        }
    };

    // Common initComplete callback
    var commonInitComplete = function() {
        $('.dt-buttons').addClass('mb-2');
        $('.dt-buttons').css({'display': 'inline-block'});
        $('.dataTables_length').addClass('mb-2');
    };

    // Common DataTable options - exposed globally for use in dynamic tables
    window.DataTableCommonOptions = {
        dom: 'Blfrtip',
        buttons: commonButtons,
        language: commonLanguage,
        initComplete: commonInitComplete
    };

    // Function to initialize a DataTable if element exists
    function initDataTable(tableId) {
        if ($(tableId).length && !$.fn.DataTable.isDataTable(tableId)) {
            $(tableId).DataTable(window.DataTableCommonOptions);
        }
    }

    // Initialize static tables (exist on page load)
    initDataTable('#cutoffTable');
    initDataTable('#projectSummaryTable');
    initDataTable('#categorySummaryTable');
});
