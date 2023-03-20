// JS Funktion Ajax Daten für Übersicht Lagerbelegung
$(function() {
    const artTable = $('#stockOccupancyTable').DataTable({
        lengthChange: false,

        ajax: {
            url: '/stock_occupancy_ajax',
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
            { data: 'koordinate' },
            { data: 'ln' },
            { data: 'fb' },
            { data: 'sp' },
            { data: 'tf' },
            { data: 'lagereinheit' },
            { data: 'article_nr' },
            { data: 'bezeichnung' },
            { data: 'trans_ein' },
            { data: 'trans_aus' },
            { data: 'lp_bestand' },
            { data: 'letzter_zugang' },
            { data: 'letzter_abgang' },
        ],
        columnDefs: [
            {
                className: 'text-center',
                targets: '_all'
            },
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