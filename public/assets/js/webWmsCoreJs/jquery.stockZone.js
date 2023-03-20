(function($){
    // StockZone Tabelle
    const stockZoneTable = $('#stockZoneTable').DataTable({
        lengthChange: false,

        ajax: {
            url: '/stock_zone_ajax',
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
            { data: 'stock_zone_short_desc' },
            { data: 'stock_zone_description' },
            {
                data: null,
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
            {
                className: 'text-center',
                targets: '_all'
            },
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
                text: 'Lagerzone anlegen',
                className: 'btn-add-new',
                action: function (e, dt, node, config) {
                    addStockZone();
                }
            }
        ]
    });

    $.contextMenu({
        selector: 'tr',
        trigger: 'right',
        callback: function(key, options, event) {
            const row = stockZoneTable.row(options.$trigger),
                stockZoneId = row.data().id;

            switch (key) {
                case 'edit' :
                    editStockZone(stockZoneId);
                    break;
                case 'delete' :
                    deleteStockZone(stockZoneId);
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

    $(function(){
        // Ändern der Standardbreite des Modals
        $('#modalCenter .modal-dialog').css('max-width', '30%');
    });

    $.ajaxSetup({
        cache: false
    });

    // Modal für Lagerzone anlegen
    function addStockZone() {
        const url = '/lagerzone_anlegen';
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-title').text('Lagerzone anlegen');
        $('#modal-content-ajax').html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: 'GET',
            data: ($('#stock-layout-form-new').serialize()),
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
            },
            success: function (data) {
                $('#modal-content-ajax').html(data);
            }
        });

        return false;
    }

    // Modal für Lagerzone bearbeiten
    function editStockZone(stockZoneId) {
        const url = '/lagerzone_bearbeiten/stockZoneId/' + stockZoneId;
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-title').text('Lagerzone bearbeiten');
        $('#modal-content-ajax').html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: 'GET',
            data: ($("#stock-layout-form-edit").serialize()),
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
            },
            success: function (data) {
                $('#modal-content-ajax').html(data);
            }
        });

        return false;
    }

    // Modal für Lagerzone löschen
    function deleteStockZone(stockZoneId) {
        const url = '/lagerzone_löschen/stockZoneId/' + stockZoneId;
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-dialog').css('max-width', '30%');
        $('#modalCenter .modal-title').text('Lagerzone löschen');
        $('#modal-content-ajax').html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: 'GET',
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
    $(document).on('click','button#add_stock_zone_save',function(event) {
        const $form = $('form#stock-zone-form-new');
        const url = '/lagerzone_anlegen';
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
                        'title': 'Lagerzone konnte nicht gespeichert werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Lagerzone erfolgreich gespeichert',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    stockZoneTable.ajax.reload();
                }
            }
        });
    });

    // Geändertes Lagerlayout speichern
    $(document).on('click','button#edit_stock_zone_save',function(event) {
        const stockZoneId = $('#edit_stock_zone_id').val();
        const $form = $('form#stock-zone-form-edit');
        const url = '/lagerzone_bearbeiten/stockZoneId/' + stockZoneId;
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
                        'title': 'Lagerzone konnte nicht gespeichert werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Lagerzone erfolgreich gespeichert',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    stockZoneTable.ajax.reload();
                }
            }
        });
    });

    // Lagerlayout löschen
    $(document).on('click','button#delete_stock_zone_delete',function(event) {
        const stockZoneId = $('#delete_stock_zone_id').val();
        const $form = $('form#stock-layout-modal-delete-ask');
        const url = '/lagerzone_löschen/stockZoneId/' + stockZoneId;
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
                        'title': 'Lagerzone konnten nicht gelöscht werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Lagerzone erfolgreich gelöscht',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    stockZoneTable.ajax.reload();
                }
            }
        });
    });

    $(document).on('click','.abort',function() {
        $('#modalCenter').modal('hide');
    });

})(jQuery);