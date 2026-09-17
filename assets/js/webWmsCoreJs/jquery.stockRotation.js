// JS Funktion Ajax Daten für Übersicht Lagerbewegungen
$(function() {
    // Tabelle für Lagerbewegungen
    const strTable = $('#strTable').DataTable({
        lengthChange: false,
        paging: false,
        retrieve: true,
        ordering: false,

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
            {
                data: null,
                render: function(data, type, row) {
                    if (row.customer_order_nr != null) {
                        return row.customer_order_nr;
                    } else {
                        return row.supplier_order_nr;
                    }
                },
            },
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
        buttons: {
            buttons: [
                {
                    extend:    'copyHtml5',
                    text:      'Kopieren',
                    title:     'Export',
                    titleAttr: 'Copy',
                    className: 'btn btn-primary btn-xm btn3d'
                },
                {
                    extend:    'csvHtml5',
                    text:      'CSV',
                    title:     'Export',
                    titleAttr: 'CSV',
                    className: 'btn btn-primary btn-xm btn3d'
                },
                {
                    text:       'JSON',
                    title:      'Export',
                    action: function (e, dt, button, config) {
                        DataTable.fileSave(
                            new Blob(
                                [
                                    JSON.stringify(convertStockRotationOverviewToJson())
                                ]
                            ),
                            'export_stock_rotation_overview.json'
                        );
                    },
                    className: 'btn btn-primary btn-xm btn3d'
                },
                {
                    extend:    'pdfHtml5',
                    text:      'PDF',
                    title:     'Export',
                    titleAttr: 'PDF',
                    className: 'btn btn-primary btn-xm btn3d'
                },
                {
                    extend: 'print',
                    text: 'Drucken',
                    autoPrint: false,
                    className: 'btn btn-primary btn-xm btn3d'
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

    // Lagerbewegungen als Json exportieren
    function convertStockRotationOverviewToJson() {
        const objects = [];
        const data = strTable.rows().data();

        for (let i = 0; i < data.length; i++) {
            objects.push(data[i]);
        }

        return objects;
    }
});
