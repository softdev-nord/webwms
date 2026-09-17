// JS Funktion Ajax Daten für Übersicht Lagerbelegung
$(function() {
    const stockOccupancyTable = $('#stockOccupancyTable').DataTable({
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
            { data: 'stock_location_coordinate' },
            { data: 'stock_location_ln' },
            { data: 'stock_location_fb' },
            { data: 'stock_location_sp' },
            { data: 'stock_location_tf' },
            { data: 'article_nr' },
            { data: 'article_name' },
            { data: 'in_stock' },
            { data: 'incoming_stock' },
            { data: 'reserved_stock' },
            { data: 'last_incoming' },
            { data: 'last_outgoing' },
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
                                    JSON.stringify(convertStockOccupancyOverviewToJson())
                                ]
                            ),
                            'export_stock_occupancy_overview.json'
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

    $.contextMenu({
        selector: 'tr',
        trigger: 'right',
        callback: function(key, options, event) {
            const row = stockOccupancyTable.row(options.$trigger),
                stockLocationCoordinate = row.data().stock_location_coordinate;

            console.log(stockLocationCoordinate);

            switch (key) {
                case 'show' :
                    getStockOccupancyByCoordinate(stockLocationCoordinate);
                    break;
                default :
                    break;
            }
        },
        items: {
            show: {
                name: 'Lagerplatz Details',
                icon: 'paste'
            },
        }
    });

    function getStockOccupancyByCoordinate(stock_location_coordinate) {
        const url = '/stock_occupancy_ajax/' + stock_location_coordinate;
        const content = '<div class="modal-body"></div>';
        $("#modalCenter .modal-dialog").css('max-width', '70%');

        $('#modalCenter .modal-title').text('Lagerplatz Details');
        $("#modal-content-ajax").html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            method: 'GET',
            url: url,
            dataType: 'html',
            success: function (data) {
                $('#modal-content-ajax').html(data);
            }
        });
    }

    // Lagerbelegung als Json exportieren
    function convertStockOccupancyOverviewToJson() {
        const objects = [];
        const data = stockOccupancyTable.rows().data();

        for (let i = 0; i < data.length; i++) {
            objects.push(data[i]);
        }

        return objects;
    }
});
