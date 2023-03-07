(function($){
    // StockLocation table
    const stockLocationTable = $('#stockLocationTable').DataTable({
        "lengthChange": false,
        "bDestroy": true,
        ajax: {
            'url': '/stock_location_ajax',
            'dataSrc': ''
        },
        // Page length max. 10 entries
        pageLength: 10,
        "language": {
            "url": "./resources/dataTable.German.json"
        },
        // Initialisation of the DataTables Select extension
        select: {
            style: 'single'
        },
        columns: [
            {"data": "stock_location_ln"},
            {"data": "stock_location_fb"},
            {"data": "stock_location_sp"},
            {"data": "stock_location_tf"},
            {"data": "stock_location_coordinate"},
            {"data": "stock_location_desc"},
            {"data": "stock_location_width"},
            {"data": "stock_location_depth"},
            {"data": "stock_location_height"},
            {
                "data": null,
                render: function (data, type, row) {
                    if (row.updated_at != null) {
                        return row.updated_at;
                    } else {
                        return row.created_at;
                    }
                },
            }
        ],
        columnDefs: [
            {className: 'text-center', targets: "_all"},
        ],
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
            },
            {
                text: 'Lagerplatz anlegen',
                className: 'btn-add-new',
                action: function (e, dt, node, config) {
                    addStockLocation();
                }
            }
        ]
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
            "edit": {name: "Bearbeiten", icon: "edit"},
            'delete': {name: 'Löschen', icon: 'delete'},
        }
    });

    $(function(){
        // Ändern der Standardbreite des Modals
        $("#modalCenter .modal-dialog").css('max-width', '98%');
    });

    $.ajaxSetup({
        cache: false
    });

    // Modal für Lagerplatz anlegen
    function addStockLocation() {
        const url = '/lagerplatz_anlegen';
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-title').text('Lagerplatz anlegen');
        $('#modal-content-ajax').html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: 'get',
            data: ($('#stock-location-form-new').serialize()),
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
            },
            success: function (data) {
                $('#modal-content-ajax').html(data);
            }
        });

        return false;
    }

    // Modal für Lagerplatz bearbeiten
    function editStockLocation(stockLocationCoordinate) {
        const url = '/lagerplatz_bearbeiten/koordinate/' + stockLocationCoordinate;
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-title').text("Lagerplatz bearbeiten");
        $("#modal-content-ajax").html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: "get",
            data: ($("#stock-location-form-edit").serialize()),
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
            },
            success: function (data) {
                $("#modal-content-ajax").html(data);
            }
        });

        return false;
    }

    // Modal für Lagerplatz löschen
    function deleteStockLocation(stockLocationCoordinate) {
        const url = '/lagerplatz_löschen/koordinate/' + stockLocationCoordinate;
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-dialog').css('max-width', '30%');
        $('#modalCenter .modal-title').text('Lagerplatz löschen');
        $('#modal-content-ajax').html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: 'get',
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
            },
            success: function (data) {
                $('#modal-content-ajax').html(data);
            }
        });

        return false;
    }

    // Neuen Lagerplatz speichern
    $(document).on('click','button#add_stock_location_save',function(event) {
        const $form = $('form#stock-location-form-new');
        const url = '/lagerplatz_anlegen';
        event.preventDefault();


        $.ajax({
            type: 'POST',
            url: url,
            data: $form.serialize(),
            success: function(data) {
                if (data.error) {
                    const errors = [];
                    let i = 0;
                    $.each(data.error, function(key, value) {
                        errors[i++] = value + '</br>';
                    });
                    const arrayString = errors.join();
                    const error = arrayString.replace(/,/g, ' ');
                    $.jAlert({
                        'title': 'Lagerplatz konnte nicht gespeichert werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Lagerplatz erfolgreich gespeichert',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    stockLocationTable.ajax.reload();
                }
            }
        });
    });

    // Geänderten Lagerplatz speichern
    $(document).on('click','button#edit_stock_location_save',function(event) {
        const stockLocationCoordinate = $('#edit_stock_location_stockLocationCoordinate').val();
        const $form = $('form#stock-location-form-edit');
        const url = '/lagerplatz_bearbeiten/koordinate/' + stockLocationCoordinate;
        event.preventDefault();

        $.ajax({
            type: 'POST',
            url: url,
            data: $form.serialize(),
            success: function(data) {
                if (data.error) {
                    const errors = [];
                    let i = 0;
                    $.each(data.error, function(key, value) {
                        errors[i++] = value + '</br>';
                    });
                    const arrayString = errors.join();
                    const error = arrayString.replace(/,/g, ' ');
                    $.jAlert({
                        'title': 'Lagerplatz konnte nicht gespeichert werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Lagerplatz erfolgreich gespeichert',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    stockLocationTable.ajax.reload();
                }
            }
        });
    });

    // Lagerplatz löschen
    $(document).on('click','button#delete_supplier_order_delete',function(event) {
        const supplierOrderId = $('#delete_stock_location_stockLocationCoordinate').val();
        const $form = $('form#supplier-order-modal-delete-ask');
        const url = '/lagerplatz_löschen/koordinate/' + supplierOrderId;
        event.preventDefault();

        $.ajax({
            type: 'POST',
            url: url,
            data: $form.serialize(),
            success: function(data) {
                if (data.error) {
                    const errors = [];
                    let i = 0;
                    $.each(data.error, function(key, value) {
                        errors[i++] = value + '</br>';
                    });
                    const arrayString = errors.join();
                    const error = arrayString.replace(/,/g, ' ');
                    $.jAlert({
                        'title': 'Lagerplatz konnten nicht gelöscht werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Lagerplatz erfolgreich gelöscht',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    stockLocationTable.ajax.reload();
                }
            }
        });
    });

    // Back to stock location overview
    $(document).on('click','#edit_stock_location_back_to_stock_location_overview',function() {
        window.location.href = '/lagerplatz'
    });
})(jQuery);
