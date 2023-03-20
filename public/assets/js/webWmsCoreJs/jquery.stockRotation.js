// JS Funktion Ajax Daten für Übersicht Lagerbewegungen
$(function() {
    const strTable = $('#strTable').DataTable({
        lengthChange: false,

        ajax: {
            url: '/stock_rotation_ajax',
            dataSrc: ''
        },
        // Seitenlänge max. 10 Einträge
        pageLength: 10,
        language: {
            url: './resources/dataTable.German.json'
        },
        // Initialisierung der DataTables Select-Erweiterung
        select: {
            style: 'single'
        },
        columns: [
            { data: 'id' },
            { data: 'movement_type' },
            { data: 'description' },
            { data: 'stock_location' },
            { data: 'stock_location_desc' },
            { data: 'article_nr' },
            { data: 'article_name' },
            { data: 'pos_quantity' },
            { data: 'username' },
            { data: 'access_date' },
            { data: 'dispatch_date' }
        ],
        columnDefs: [
            {
                className: 'text-center',
                targets: '_all'
            }
        ],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copyHtml5',
                text: 'Kopieren',
                title: 'Export',
                titleAttr: 'Copy'
            },
            {
                extend: 'excelHtml5',
                text: 'Excel',
                title: 'Export',
                titleAttr: 'Excel'
            },
            {
                extend: 'csvHtml5',
                text: 'CSV',
                title: 'Export',
                titleAttr: 'CSV'
            },
            {
                extend: 'pdfHtml5',
                text: 'PDF',
                title: 'Export',
                titleAttr: 'PDF'
            },
            {
                extend: 'print',
                text: 'Drucken',
                autoPrint: false
            }
        ]
    });
});