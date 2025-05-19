function initDataTable(tableSelector, ajaxRoute, columnConfig, additionalOptions = {}) {
    // Destroy existing DataTable if it exists
    if ($.fn.DataTable.isDataTable(tableSelector)) {
        $(tableSelector).DataTable().destroy();
    }

    // Default column configuration
    const defaultColumns = columnConfig.map(col => ({ ...col }));

    // Default export button configuration (excluding action and status columns)
    const exportColumns = defaultColumns
        .filter(col => col.data !== 'action' && col.data !== 'status')
        .map((col, index) => index);

    // Merge default options with additional options
    const tableOptions = {
        lengthChange: true,
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
            "<'row'<'col-sm-12'B>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        buttons: [
            {
                extend: 'copy',
                text: '<i class="fas fa-copy"></i> Copy',
                className: 'btn-export',
                exportOptions: { columns: exportColumns }
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn-export',
                exportOptions: { columns: exportColumns }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn-export',
                exportOptions: { columns: exportColumns }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn-export',
                exportOptions: { columns: exportColumns }
            }
        ],
        processing: true,
        serverSide: true,
        ajax: ajaxRoute,
        columns: defaultColumns,
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
            emptyTable: '<div class="text-center p-4"><i class="fas fa-box-open fa-3x text-muted"></i><p class="mt-2">No data available</p></div>',
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            search: '<i class="fas fa-search"></i>',
            searchPlaceholder: "Search projects..."
        },
        pageLength: 10,
        order: [[1, 'desc']],
        ...additionalOptions
    };

    // Initialize DataTable
    return $(tableSelector).DataTable(tableOptions);
}
