(function($){
    // Log Tabelle
    $('#loggingTable').DataTable({
        lengthChange: false,
        paging: false,
        retrieve: true,

        ajax: {
            'url': '/logging_ajax',
            'dataSrc': ''
        },
        // Seitenlänge max. 10 Einträge
        pageLength: 10,
        'language': {
            'url': './resources/dataTable.German.json'
        },
        // Initialisierung der DataTables Select-Erweiterung
        select: {
            style: 'single'
        },
        columns: [
            {'data': 'route'},
            {'data': 'message'},
            {'data': 'date'},
            {'data': 'user'},
            {'data': 'ip_address'},
            {'data': 'user_agent'},
        ],
        columnDefs: [
            {
                className: 'text-center', targets: '_all'
            },
        ],
        dom: 'Bfrtip',
        buttons: [
            {
                extend:    'copyHtml5',
                text:      'Kopieren',
                title:     'Export',
            },
            {
                extend:    'csvHtml5',
                text:      'CSV',
                title:     'Export',
            },
            {
                extend:    'pdfHtml5',
                text:      'PDF',
                title:     'Export',
            },
            {
                extend: 'print',
                text: 'Drucken',
                autoPrint: false
            }
        ],
    });
})(jQuery);
