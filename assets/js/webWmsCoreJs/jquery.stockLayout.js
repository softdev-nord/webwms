(function($){
    // StockLayout Tabelle
    const stockLayoutTable = $('#stockLayoutTable').DataTable({
        lengthChange: false,

        ajax: {
            url: '/stock_layout_ajax',
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
            { data: 'stock_nr' },
            { data: 'stock_description' },
            { data: 'stock_level1' },
            { data: 'stock_level2' },
            { data: 'stock_level3' },
            { data: 'stock_model' },
            { data: 'stock_typ' },
            { data: 'stock_long_description' },
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
                                    JSON.stringify(convertStockLayoutOverviewToJson())
                                ]
                            ),
                            'export_stock_layout_overview.json'
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
                    text: 'Lagerlayout anlegen',
                    className: 'btn btn-primary btn-xm btn3d float-start',
                    action: function (e, dt, node, config) {
                        addStockLayout();
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
            const row = stockLayoutTable.row(options.$trigger),
                stockLayoutId = row.data().id;

            switch (key) {
                case 'edit' :
                    editStockLayout(stockLayoutId);
                    break;
                case 'delete' :
                    deleteStockLayout(stockLayoutId);
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

    // Lagerlayout als Json exportieren
    function convertStockLayoutOverviewToJson() {
        const objects = [];
        const data = stockLayoutTable.rows().data();

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

    // Modal für Lagerlayout anlegen
    function addStockLayout() {
        const url = '/lagerlayout_anlegen';
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-title').text('Lagerlayout anlegen');
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

    // Modal für Lagerlayout bearbeiten
    function editStockLayout(stockLayoutId) {
        const url = '/lagerlayout_bearbeiten/stockLayoutId/' + stockLayoutId;
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-title').text('Lagerlayout bearbeiten');
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

    // Modal für Lagerlayout löschen
    function deleteStockLayout(stockLayoutId) {
        const url = '/lagerlayout_löschen/stockLayoutId/' + stockLayoutId;
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-dialog').css('max-width', '30%');
        $('#modalCenter .modal-title').text('Lagerlayout löschen');
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
    $(document).on('click','button#add_stock_layout_save',function(event) {
        const $form = $('form#stock-layout-form-new');
        const url = '/lagerlayout_anlegen';
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
                        'title': 'Lagerlayout konnte nicht gespeichert werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Lagerlayout erfolgreich gespeichert',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    stockLayoutTable.ajax.reload();
                }
            }
        });
    });

    // Geändertes Lagerlayout speichern
    $(document).on('click','button#edit_stock_layout_save',function(event) {
        const stockLayoutId = $('#edit_stock_layout_id').val();
        const $form = $('form#stock-layout-form-edit');
        const url = '/lagerlayout_bearbeiten/stockLayoutId/' + stockLayoutId;
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
                        'title': 'Lagerlayout konnte nicht gespeichert werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Lagerlayout erfolgreich gespeichert',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    stockLayoutTable.ajax.reload();
                }
            }
        });
    });

    // Lagerlayout löschen
    $(document).on('click','button#delete_stock_layout_delete',function(event) {
        const stockLayoutId = $('#delete_stock_layout_id').val();
        const $form = $('form#stock-layout-modal-delete-ask');
        const url = '/lagerlayout_löschen/stockLayoutId/' + stockLayoutId;
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
                        'title': 'Lagerlayout konnten nicht gelöscht werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Lagerlayout erfolgreich gelöscht',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    stockLayoutTable.ajax.reload();
                }
            }
        });
    });

    $(document).on('click','.abort',function() {
        $('#modalCenter').modal('hide');
    });

})(jQuery);
