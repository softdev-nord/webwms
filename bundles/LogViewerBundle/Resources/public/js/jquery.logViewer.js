(function($){
    // Log Tabelle
    const logViewerTable = $('#logViewerTable').DataTable({
        searchPanes: {
            cascadePanes: true,
            viewTotal: true
        },
        lengthChange: false,
        searching: false,
        // Seitenlänge max. 10 Einträge
        pageLength: 10,
        language: {
            url: "./../resources/dataTable.German.json"
        },
        // Initialisierung der DataTables Select-Erweiterung
        select: {
            style: 'single'
        },
        // columns: [
        //     {"data": null},
        //     {"data": null},
        //     {"data": null},
        //     {"data": null},
        //     {
        //         data: null,
        //         className: "log-action",
        //         defaultContent: '<i class="mdi mdi-menu" onclick="document.getElementById(\'logContext{{ loop.index }}\').classList.toggle(\'d-none\');"></i>',
        //         orderable: false,
        //     },
        // ],
        dom: 'Bfrtip',
        buttons: [
            {
                extend:    'copyHtml5',
                text:      'Kopieren',
                title:     'Export',
                titleAttr: 'Copy'
            },
            {
                extend:    'excelHtml5',
                text:      'Excel',
                title:     'Export',
                titleAttr: 'Excel'
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
        ]

    });

    // var data = table.rows().data();
    // data.each(function (value, index) {
    //     console.log('Data in index: ' + index + ' is: ' + value);
    // });

    console.log(logViewerTable.rows().data())

    logViewerTable.rows().every(function () {
        var pos = logViewerTable.row().index();
        var row = logViewerTable.row(pos).data();
        console.log(row);
    });

})(jQuery);