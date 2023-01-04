// JS Funktion Ajax Daten für Übersicht Bestellungen

$(function() {
    const bstTable = $('#bstTable').DataTable({
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
                "rowId": 'staffId',
                "className": "editor-edit text-center",
                "defaultContent": '<i class="mdi mdi-square-edit-outline"/>',
                "orderable": false
            }
        ],
        columnDefs: [
            {className: 'text-center', targets: [0, 2, 4, 5]},
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
            }
        ]
    });

    $.contextMenu({
        selector: 'tr',
        trigger: 'right',
        callback: function(key, options, event) {
            const row = bstTable.row(options.$trigger);

            switch (key) {
                case 'edit' :
                    editSupplierOrder(row.data().supplier_order_id);
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

    function editSupplierOrder(id) {
        const url = '/bestellung_bearbeiten/id/' + id;
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
                const selected = bstTable.row({selected: true});
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
    bstTable.on( 'click', function () {
        posTable.ajax.reload();

    } );

    // Beim Abwählen der Zeile in der Bestellungs-Tabelle wird die Positions-Tabelle wieder geleert.
    bstTable.on( 'deselect', function () {
        posTable.ajax.reload();
    } );

    function numberWithCommas(number) {
        const formatConfig = {
            style: "decimal",
            minimumFractionDigits: 0,
        };
        return new Intl.NumberFormat('de-DE', formatConfig).format(number);
    }

} );
