(function($){
    // StockLocation table
    const stockLocationTable = $('#stockLocationTable').DataTable({
        lengthChange: false,
        bDestroy: true,

        ajax: {
            url: '/stock_location_ajax',
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
            { data: 'stock_location_ln' },
            { data: 'stock_location_fb' },
            { data: 'stock_location_sp' },
            { data: 'stock_location_tf' },
            { data: 'stock_location_coordinate' },
            { data: 'stock_location_desc' },
            { data: 'stock_location_width' },
            { data: 'stock_location_depth' },
            { data: 'stock_location_height' },
            {
                data: null,
                render: function(data, type, row) {
                    if (row.updated_at != null) {
                        return row.updated_at;
                    } else {
                        return row.created_at;
                    }
                },
            }
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
                                    JSON.stringify(convertStockLocationOverviewToJson())
                                ]
                            ),
                            'export_stock_location_overview.json'
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
                },
                {
                    text: 'Lagerplatz anlegen',
                    className: 'btn btn-primary btn-xm btn3d float-start',
                    action: function(e, dt, node, config) {
                        addStockLocation();
                    }
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
            const row = stockLocationTable.row(options.$trigger),
                stockLocationCoordinate = row.data().stock_location_coordinate;

            switch (key) {
                case 'edit' :
                    editStockLocation(stockLocationCoordinate);
                    break;
                case 'delete' :
                    deleteStockLocation(stockLocationCoordinate);
                    break;
                default :
                    break;
            }
        },
        items: {
            edit: {
                name: 'Bearbeiten',
                icon: 'edit'
            },
            delete: {
                name: 'Löschen',
                icon: 'delete'
            },
        }
    });

    // Lagerplätze als Json exportieren
    function convertStockLocationOverviewToJson() {
        const objects = [];
        const data = stockLocationTable.rows().data();

        for (let i = 0; i < data.length; i++) {
            objects.push(data[i]);
        }

        return objects;
    }

    $(function(){
        // Ändern der Standardbreite des Modals
        $('#modalCenter .modal-dialog').css('max-width', '98%');
    });

    $.ajaxSetup({
        cache: false
    });

    // Modal für Lagerplatz anlegen
    function addStockLocation() {
        const url = '/lagerplatz_anlegen',
            $form = $('form#stock-location-form-new'),
            title = 'Lagerplatz anlegen';

        getContentForModal(url, title, $form);
    }

    // Modal für Lagerplatz bearbeiten
    function editStockLocation(stockLocationCoordinate) {
        const url = '/lagerplatz_bearbeiten/koordinate/' + stockLocationCoordinate,
            $form = $('form#stock-location-form-edit'),
            title = 'Lagerplatz bearbeiten';

        getContentForModal(url, title, $form);
    }

    // Modal für Lagerplatz löschen
    function deleteStockLocation(stockLocationCoordinate) {
        const url = '/lagerplatz_löschen/koordinate/' + stockLocationCoordinate,
            $form = $('form#stock-location-modal-delete-ask'),
            title = 'Lagerplatz löschen';

        $('#modalCenter .modal-dialog').css('max-width', '30%');

        getContentForModal(url, title, $form);
    }

    // Neuen Lagerplatz speichern
    $(document).on('click', 'button#add_stock_location_save', function(event) {
        const $form = $('form#stock-location-form-new'),
            url = '/lagerplatz_anlegen',
            errorMessage = 'Lagerplatz konnte nicht gespeichert werden',
            successMessage = 'Lagerplatz erfolgreich gespeichert';
        event.preventDefault();

        _doRequest('POST', url, $form, errorMessage, successMessage, stockLocationTable, false);
    });

    // Geänderten Lagerplatz speichern
    $(document).on('click', 'button#edit_stock_location_save', function(event) {
        const stockLocationCoordinate = $('#edit_stock_location_stockLocationCoordinate').val(),
            $form = $('form#stock-location-form-edit'),
            url = 'lagerplatz_bearbeiten/koordinate/' + stockLocationCoordinate,
            errorMessage = 'Lagerplatz konnte nicht gespeichert werden',
            successMessage = 'Lagerplatz erfolgreich gespeichert';
        event.preventDefault();

        _doRequest('POST', url, $form, errorMessage, successMessage, stockLocationTable, false);
    });

    // Lagerplatz löschen
    $(document).on('click', 'button#delete_stock_location_delete', function(event) {
        const stockLocationCoordinate = $('#delete_stock_location_stockLocationCoordinate').val(),
            $form = $('form#stock-location-modal-delete-ask'),
            url = '/lagerplatz_löschen/koordinate/' + stockLocationCoordinate,
            successMessage = 'Lagerplatz erfolgreich gelöscht',
            errorMessage = 'Lagerplatz konnten nicht gelöscht werden';
        event.preventDefault();

        _doRequest('POST', url, $form, errorMessage, successMessage, stockLocationTable, false);
    });

    $(document).on('click', '.abort', function() {
        $('#modalCenter').modal('hide');
    });

})(jQuery);
