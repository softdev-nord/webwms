(function($){
    // Log Tabelle
    $('#loggingTable').DataTable({
        lengthChange: false,

        ajax: {
            url: '/logging_ajax',
            dataSrc: ''
        },
        // Seitenlänge max. 15 Einträge
        pageLength: 15,
        language: {
            url: './resources/dataTable.German.json'
        },
        // Initialisierung der DataTables Select-Erweiterung
        select: {
            style: 'single'
        },
        columns: [
            { data: 'route' },
            { data: 'message' },
            { data: 'date' },
            { data: 'user' },
            { data: 'ip_address' },
            { data: 'user_agent' },
        ],
        columnDefs: [
            {
                className: 'text-center',
                targets: '_all'
            },
        ],
        dom: 'Bfrtip',
        buttons: {
            buttons: [
                {
                    extend:    'copyHtml5',
                    text:      'Kopieren',
                    title:     'Export',
                    titleAttr: 'Copy',
                    className: 'btn-primary btn-xs btn3d'
                },
                {
                    extend:    'csvHtml5',
                    text:      'CSV',
                    title:     'Export',
                    titleAttr: 'CSV',
                    className: 'btn-primary btn-xs btn3d'
                },
                {
                    extend:    'pdfHtml5',
                    text:      'PDF',
                    title:     'Export',
                    titleAttr: 'PDF',
                    className: 'btn-primary btn-xs btn3d'
                },
                {
                    extend: 'print',
                    text: 'Drucken',
                    autoPrint: false,
                    className: 'btn-primary btn-xs btn3d'
                }
            ],
            dom: {
                button: {
                    className: 'btn'
                },
                buttonLiner: {
                    tag: null
                }
            }
        }
    });
})(jQuery);
