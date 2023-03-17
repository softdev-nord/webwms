$(function() {
    const customerOrderTable = $('#customerOrderTable').DataTable({
        createdRow: function (row, data, dataIndex) {
            $(row).attr('data-customer-order-id', data.customer_order_id);
        },
        lengthChange: false,

        ajax: {
            url: '/customer_order_ajax',
            dataSrc: ''
        },
        // Seitenlänge max. 10 Einträge
        pageLength: 10,
        language: {
            url: "./resources/dataTable.German.json"
        },
        // Initialisierung der DataTables Select-Erweiterung
        select: {
            style: 'single'
        },
        columns: [
            { data: 'customer_order_nr' },
            { data: 'customer_nr' },
            { data: 'customer_name' },
            { data: 'customer_order_reference' },
            { data: 'customer_order_date' },
            { data: 'customer_order_creation_date' },
            { data: 'username' },
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
                className: 'text-center', targets: '_all'
            },
            {
                targets: [4, 5], render: function (data) {
                    moment.locale('de');
                    return moment(data).format('L');
                },
                createdCell:  function (tr, cellData, rowData, row, col) {
                    $(tr).attr('data-customer-order-id', rowData);
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
                text: 'Auftrag anlegen',
                className: 'btn-add-new',
                action: function (e, dt, node, config) {
                    addCustomerOrder();
                }
            }
        ]
    });

    $.contextMenu({
        selector: 'tr',
        trigger: 'right',
        callback: function(key, options, event) {
            const row = customerOrderTable.row(options.$trigger),
                customerOrderId = row.data().customer_order_id;

            switch (key) {
                case 'edit' :
                    editCustomerOrder(customerOrderId);
                    break;
                case 'delete' :
                    deleteCustomerOrder(customerOrderId);
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
        $('#modalCenter .modal-dialog').css('max-width', '98%');
    });

    $.ajaxSetup({
        cache: false
    });

    // Modal für Auftrag anlegen
    function addCustomerOrder() {
        const url = '/auftrag_anlegen';
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-title').text('Auftrag anlegen');
        $('#modal-content-ajax').html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: 'GET',
            data: ($('#customer-order-form-new').serialize()),
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
            },
            success: function (data) {
                $('#modal-content-ajax').html(data);
            }
        });

        return false;
    }

    // Modal für Auftrag bearbeiten
    function editCustomerOrder(customerOrderId) {
        const url = '/auftrag_bearbeiten/customerOrderId/' + customerOrderId;
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-title').text('Auftrag bearbeiten');
        $("#modal-content-ajax").html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: "get",
            data: ($('#customer-order-form-edit').serialize()),
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
            },
            success: function (data) {
                $("#modal-content-ajax").html(data);
            }
        });

        return false;
    }

    // Modal für Auftrag löschen
    function deleteCustomerOrder(customerOrderId) {
        const url = '/auftrag_löschen/customerOrderId/' + customerOrderId;
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-dialog').css('max-width', '30%');
        $('#modalCenter .modal-title').text('Auftrag löschen');
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

    // Neuen Auftrag speichern
    $(document).on('click','button#customer_order_save',function(event) {
        const $form = $('form#customer-order-form-new');
        const url = '/auftrag_anlegen';
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
                        'title': 'Auftrag konnte nicht gespeichert werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Auftrag erfolgreich gespeichert',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    customerOrderTable.ajax.reload();
                }
            }
        });
    });

    // Geänderten Auftrag speichern
    $(document).on('click','button#edit_customer_order_save',function(event) {
        const customerOrderId = $('#edit_customerOrder_customerOrderId').val();
        const $form = $('form#customer-order-form-edit');
        const url = '/auftrag_bearbeiten/customerOrderId/' + customerOrderId;
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
                        'title': 'Auftrag konnte nicht gespeichert werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Auftrag erfolgreich gespeichert',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    customerOrderTable.ajax.reload();
                }
            }
        });
    });

    // Auftrag löschen
    $(document).on('click','button#delete_customer_order_delete',function(event) {
        const customerOrderId = $('#delete_customer_order_customerOrderId').val();
        const $form = $('form#customer-order-modal-delete-ask');
        const url = '/auftrag_löschen/customerOrderId/' + customerOrderId;
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
                        'title': 'Auftrag konnten nicht gelöscht werden',
                        'content': error,
                        'theme': 'red',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                } else {
                    $.jAlert({
                        'title': 'Auftrag erfolgreich gelöscht',
                        'content': data.message,
                        'theme': 'green',
                        'size': 'md',
                        'showAnimation': 'fadeInUp',
                        'hideAnimation': 'fadeOutDown',
                        'autoClose': 5000
                    });
                    $('#modalCenter').modal('hide');
                    customerOrderTable.ajax.reload();
                }
            }
        });
    });

    // JS Funktion Ajax Daten für Auftragspositionen
    const posTable = $('#posTable').DataTable({
        searching: false,
        lengthChange: false,
        info: false,
        language: {
            url: './resources/dataTable.German.json',
        },

        // Ajax-Anfrage via PHP (Json)
        ajax: {
            url: '/customer_order_pos',

            // Es werden nur die Daten in der Positions-Tabelle geladen,
            // die mit der ID in der Auftrags-Tabelle übereinstimmen.
            dataSrc: function (data) {
                const selected = customerOrderTable.row({selected: true});
                const rows = [];

                if (selected.any()) {
                    const customer_order_id = selected.data().customer_order_id;
                    for (i = 0; i < data.length; i++) {
                        const row = data[i];
                        if (row.customer_order_id === customer_order_id) {
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
            { data: 'customer_order_nr'},
            { data: 'article_nr'},
            { data: 'article_name'},
            { data: 'quantity',
                render: $.fn.dataTable.render.number('.')
            },
            {
                "data": "lbw_menge",
                render: function (data, type, row) {
                    if (row.lbw_menge != null) {
                        return numberWithCommas(row.lbw_menge);
                    } else {
                        return '0';
                    }
                },
            },
            {
                data: 'lbw_menge',
                render: function (data, type, row) {
                    if (row.lbw_menge != null) {
                        return numberWithCommas(parseInt(row.quantity) - parseInt(row.lbw_menge));
                    } else {
                        return numberWithCommas(row.quantity);
                    }
                },
            }
        ],
        columnDefs: [
            {
                className: 'text-center', targets: [0, 1, 3, 4, 5]
            },
        ],
    });

    // Bei Auswahl einer Zeile in der Auftrags-Tabelle wird die Positions-Tabelle mit den entsprechenden Daten geladen.
    customerOrderTable.on('select', function () {
        posTable.ajax.reload();
    });

    // Beim Abwählen der Zeile in der Auftrags-Tabelle wird die Positions-Tabelle wieder geleert.
    customerOrderTable.on('deselect', function () {
        posTable.ajax.reload();
    });

    function numberWithCommas(number) {
        const formatConfig = {
            style: 'decimal',
            minimumFractionDigits: 0,
        };
        return new Intl.NumberFormat('de-DE', formatConfig).format(number);
    }

    $(document).on('click','.abort',function() {
        $('#modalCenter').modal('hide');
    });

});