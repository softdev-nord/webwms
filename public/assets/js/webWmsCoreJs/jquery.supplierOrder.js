;(function($){
    // Lieferanten Bestellungen Tabelle
    const supplierOrderTable = $('#supplierOrderTable').DataTable({
        createdRow: function (row, data, dataIndex) {
            $(row).attr('data-supplier-order-id', data.supplier_order_id);
        },
        "lengthChange": false,
        // Ajax-Anfrage via PHP (Json)
        ajax: {
            'url': '/supplier_orders_ajax',
            'dataSrc': ''
        },
        // Seitenlänge max. 10 Einträge
        pageLength: 10,
        language: {
            "url": "./resources/dataTable.German.json"
        },
        // Initialisierung der DataTables Select-Erweiterung
        select: {
            style: 'single'
        },
        columns: [
            {"data": "supplier_order_nr"},
            {"data": "supplier_order_reference"},
            {"data": "supplier_nr"},
            {"data": "supplier_name"},
            {"data": "supplier_order_creation_date"},
            {"data": "username"},
            {
                "data": null,
                render: function (data, type, row) {
                    if (row["updated_at"] != null) {
                        return row["updated_at"];
                    } else {
                        return row["created_at"];
                    }
                },
            }
        ],
        columnDefs: [
            {
                className: 'text-center', targets: "_all"
            },
            {
                targets: [4], render: function (data) {
                    moment.locale("de");
                    return moment(data).format("L");
                },
                createdCell:  function (tr, cellData, rowData, row, col) {
                    $(tr).attr('data-supplier-order-id', rowData);
                }
            }
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
                text: 'Bestellung anlegen',
                className: 'btn-add-new',
                action: function ( e, dt, node, config ) {
                    addSupplierOrder();
                }
            }
        ]
    });

    $.contextMenu({
        selector: 'tr',
        trigger: 'right',
        callback: function(key, options, event) {
            const row = supplierOrderTable.row(options.$trigger),
            supplierOrderId = row.data().supplier_order_id;

            switch (key) {
                case 'edit' :
                    editSupplierOrder(supplierOrderId);
                    break;
                case 'delete' :
                    deleteSupplierOrder(supplierOrderId);
                    break;
                default :
                    break
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

    // Modal für Bestellung anlegen
    function addSupplierOrder() {
        const url = '/bestellung_anlegen';
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-title').text('Bestellung anlegen');
        $('#modal-content-ajax').html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: 'get',
            data: ($('#supplier-order-form-new').serialize()),
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
            },
            success: function (data) {
                $('#modal-content-ajax').html(data);
            }
        });

        return false;
    }

    // Modal für Bestellung bearbeiten
    function editSupplierOrder(supplierOrderId) {
        const url = '/bestellung_bearbeiten/supplierOrderId/' + supplierOrderId;
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-title').text("Bestellung bearbeiten");
        $("#modal-content-ajax").html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: "get",
            data: ($("#supplier-order-form-new").serialize()),
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
            },
            success: function (data) {
                $("#modal-content-ajax").html(data);
            }
        });

        return false;
    }

    // Modal für Bestellung löschen
    function deleteSupplierOrder(supplierOrderId) {
        const url = '/bestellung_löschen/supplierOrderId/' + supplierOrderId;
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-dialog').css('max-width', '30%');
        $('#modalCenter .modal-title').text('Bestellung löschen');
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

    // Neue Bestellung speichern
    $(document).on('click','button#supplier_order_save',function(event) {
        const $form = $('form#supplier-order-form-new');
        const url = '/bestellung_anlegen';
        event.preventDefault();


        $.ajax({
            type: 'POST',
            url: url,
            data: $form.serialize(),
            success: function(data) {
                if (data.error) {
                    let errors = [];
                    let i = 0;
                    $.each(data.error, function(key, value) {
                        errors[i++] = value + '</br>';
                    });
                    let arrayString = errors.join();
                    const error = arrayString.replace(/,/g, ' ');
                    $.jAlert({
                        'title': 'Bestellung konnte nicht gespeichert werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Bestellung erfolgreich gespeichert',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    supplierOrderTable.ajax.reload();
                }
            }
        });
    });

    // Geänderte Bestellung speichern
    $(document).on('click','button#edit_supplier_order_save',function(event) {
        const supplierOrderId = $('#edit_supplierOrder_supplierOrderId').val();
        const $form = $('form#supplier-order-form-edit');
        const url = '/bestellung_bearbeiten/supplierOrderId/' + supplierOrderId;
        event.preventDefault();

        $.ajax({
            type: 'POST',
            url: url,
            data: $form.serialize(),
            success: function(data) {
                if (data.error) {
                    let errors = [];
                    let i = 0;
                    $.each(data.error, function(key, value) {
                        errors[i++] = value + '</br>';
                    });
                    let arrayString = errors.join();
                    const error = arrayString.replace(/,/g, ' ');
                    $.jAlert({
                        'title': 'Bestellung konnte nicht gespeichert werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Bestellung erfolgreich gespeichert',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    supplierOrderTable.ajax.reload();
                }
            }
        });
    });

    // Bestellung löschen
    $(document).on('click','button#delete_supplier_order_delete',function(event) {
        const supplierOrderId = $('#delete_supplier_order_supplierOrderId').val();
        const $form = $('form#supplier-order-modal-delete-ask');
        const url = '/bestellung_löschen/supplierOrderId/' + supplierOrderId;
        event.preventDefault();

        $.ajax({
            type: 'POST',
            url: url,
            data: $form.serialize(),
            success: function(data) {
                if (data.error) {
                    let errors = [];
                    let i = 0;
                    $.each(data.error, function(key, value) {
                        errors[i++] = value + '</br>';
                    });
                    let arrayString = errors.join();
                    const error = arrayString.replace(/,/g, ' ');
                    $.jAlert({
                        'title': 'Bestellung konnten nicht gelöscht werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Bestellung erfolgreich gelöscht',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    supplierOrderTable.ajax.reload();
                }
            }
        });
    });

    const posTable = $('#posTable').DataTable({
        "lengthChange": false,
        "searching": false,
        "info": false,
        "language": {
            "url": "./resources/dataTable.German.json",
        },
        // Ajax-Anfrage via PHP (Json)
        ajax: {
            'url': '/supplier_order_pos_ajax',

            // Es werden nur die Daten in der Positions-Tabelle geladen,
            // die mit der ID in der Bestellungs-Tabelle übereinstimmen.
            dataSrc: function (data) {
                const selected = supplierOrderTable.row({selected: true});
                const rows = [];

                if (selected.any()) {
                    const supplier_order_id = selected.data().supplier_order_id;
                    for (i = 0; i < data.length; i++) {
                        const row = data[i];
                        if (row.supplier_order_id === supplier_order_id) {
                            rows.push(row);
                        }
                    }
                }
                return rows;
            }
        },
        // Seitenlänge max. 5 Einträge
        pageLength: 5,
        columns: [
            {"data": "supplier_order_nr"},
            {"data": "article_nr"},
            {"data": "article_name"},
            {"data": "supplier_order_pos_quantity",
                render: $.fn.dataTable.render.number( '.')
            },
            {
                "data": null,
                render: function (data, type, row) {
                    if (row["lbw_menge"] != null) {
                        return numberWithCommas(row["lbw_menge"]);
                    } else {
                        return "0";
                    }
                },
            },
            {
                "data": null,
                render: function (data, type, row) {
                    if (row["lbw_menge"] != null) {
                        return numberWithCommas(parseInt(row["supplier_order_pos_quantity"]) - parseInt(row["lbw_menge"]));
                    } else {
                        return numberWithCommas(row["supplier_order_pos_quantity"]);
                    }
                },
            }
        ],
        columnDefs: [
            {
                className: 'text-center', targets: [0, 1, 3, 4, 5]
            }
        ],
        dom: 'Bfrtip',
        buttons: [
            {
                extend:    'copyHtml5',
                text:      'Kopieren',
                title:     'Export',
                titleAttr: 'Copy'
            }
        ]
    });

    // Durch Auswahl einer Zeile in der Bestellungs-Tabelle wird die Positions-Tabelle mit den entsprechenden Daten geladen.
    supplierOrderTable.on( 'click', function () {
        posTable.ajax.reload();

    } );

    // Beim Abwählen der Zeile in der Bestellungs-Tabelle wird die Positions-Tabelle wieder geleert.
    supplierOrderTable.on( 'deselect', function () {
        posTable.ajax.reload();
    } );

    function numberWithCommas(number) {
        const formatConfig = {
            style: "decimal",
            minimumFractionDigits: 0,
        };
        return new Intl.NumberFormat('de-DE', formatConfig).format(number);
    }

})(jQuery);
