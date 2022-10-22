(function($){
    // StockLayout table
    $('#stockLayout').dataTable({
        "pagingType": "full_numbers",
        "pageLength": 10,
        "lengthChange": false,
        "language": {
            "url": "./resources/dataTable.German.json"
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend:    'copyHtml5',
                text:      'Kopieren',
                title:     'Export',
                titleAttr: 'Copy'
            },
            {
                extend:    'csvHtml5',
                text:      'CSV',
                title:     'Export',
                titleAttr: 'CSV'
            },
            {
                extend:    'pdfHtml5',
                text:      'PDF',
                title:     'Export',
                titleAttr: 'PDF'
            },
            {
                extend: 'print',
                text: 'Drucken',
                autoPrint: false
            }
        ],
        columnDefs: [
            {
                className: 'text-center',
                targets: '_all'
            }
        ],
    });
})(jQuery);