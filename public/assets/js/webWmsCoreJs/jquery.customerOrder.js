// JS Funktion Ajax Daten für Übersicht Aufträge

$(function() {
    const aftTable = $('#aftTable').DataTable({
        createdRow: function (row, data, dataIndex) {
            $(row).attr('data-customer-order-id', data.customer_order_id);
        },
        "lengthChange": false,
        // Ajax-Anfrage via PHP (Json)
        ajax: {
            'url': '/customer_order_ajax',
            'dataSrc': ''
        },
        // Seitenlänge max. 10 Einträge
        pageLength: 10,
        "language": {
            "url": "./resources/dataTable.German.json"
        },
        // Initialisierung der DataTables Select-Erweiterung
        select: {
            style: 'single'
        },
        columns: [
            {"data": "customer_order_nr"},
            {"data": "customer_nr"},
            {"data": "customer_name"},
            {"data": "customer_order_reference"},
            {"data": "customer_order_date"},
            {"data": "customer_order_creation_date"},
            {"data": "username"},
            {
                "data": null,
                "rowId": 'staffId',
                "className": "editor-edit text-center",
                "defaultContent": '<i class="mdi mdi-square-edit-outline"/>',
                "orderable": false
            }
        ],
        columnDefs: [
            {className: 'text-center', targets: [0, 1, 4, 5, 6]},
            {
                targets: [4, 5], render: function (data) {
                    moment.locale("de");
                    return moment(data).format("L");
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
            }
        ]
    });

    $.contextMenu({
        selector: 'tr',
        trigger: 'right',
        callback: function(key, options, event) {
            const row = aftTable.row(options.$trigger);

            switch (key) {
                case 'edit' :
                    editCustomerOrder(row.data().customer_order_id);
                    break;
                default :
                    break
            }
        },
        items: {
            "edit": {name: "Bearbeiten", icon: "edit"},
        }
    });

    $(function(){
        // Changed the default modal width
        $("#modalCenter .modal-dialog").css('max-width', '98%');
    });

    $.ajaxSetup({
        cache: false
    });

    function editCustomerOrder(id) {
        const url = '/auftrag_bearbeiten/id/' + id;
        const content = '<div class="modal-body"></div>';

        $('#modalCenter .modal-title').text("Auftrag bearbeiten");
        $("#modal-content-ajax").html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: "get",
            data: ($("#customer-order-form-new").serialize()),
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
            },
            success: function (data) {
                $("#modal-content-ajax").html(data);
            }
        });

        return false;
    }

    // JS Funktion Ajax Daten für Auftragspositionen
    const posTable = $('#posTable').DataTable({
        "searching": false,
        "lengthChange": false,
        "info": false,
        "language": {
            "url": "./resources/dataTable.German.json",
        },

        // Ajax-Anfrage via PHP (Json)
        ajax: {
            'url': '/customer_order_pos_ajax',

            // Es werden nur die Daten in der Positions-Tabelle geladen,
            // die mit der ID in der Auftrags-Tabelle übereinstimmen.
            dataSrc: function (data) {
                const selected = aftTable.row({selected: true});
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
            {"data": "customer_order_nr"},
            {"data": "article_nr"},
            {"data": "article_name"},
            {"data": "quantity",
                render: $.fn.dataTable.render.number( '.')
            },
            {
                "data": "lbw_menge",
                render: function (data, type, row) {
                    if (row["lbw_menge"] != null) {
                        return numberWithCommas(row["lbw_menge"]);
                    } else {
                        return "0";
                    }
                },
            },
            {
                "data": "lbw_menge",
                render: function (data, type, row) {
                    if (row["lbw_menge"] != null) {
                        return numberWithCommas(parseInt(row["quantity"]) - parseInt(row["lbw_menge"]));
                    } else {
                        return numberWithCommas(row["quantity"]);
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
    aftTable.on( 'select', function () {
        posTable.ajax.reload();

    });

    // Beim Abwählen der Zeile in der Auftrags-Tabelle wird die Positions-Tabelle wieder geleert.
    aftTable.on( 'deselect', function () {
        posTable.ajax.reload();
    });

    // Edit record
    $(document).on('click', '.editor-edit', function () {
        $('#exampleModal').modal();
    });

    function numberWithCommas(number) {
        const formatConfig = {
            style: "decimal",
            minimumFractionDigits: 0,
        };
        return new Intl.NumberFormat('de-DE', formatConfig).format(number);
    }
});