// JS Funktion Ajax Daten für Übersicht Bestellungen
$(function() {
    const bstTable = $('#bstTable').DataTable({
        "lengthChange": false,
        // Ajax-Anfrage via PHP (Json)
        ajax: {
            'url': '/supplier_orders_ajax',
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
            {"data": "supplier_order_nr"},
            {"data": "supplier_order_reference"},
            {"data": "supplier_nr"},
            {"data": "supplier_name"},
            {"data": "supplier_order_order_date"},
            {"data": "username"}
        ],
        "columnDefs": [
            {className: 'text-center', targets: [0, 2, 4, 5]},
            {
                targets: [4], render: function (data) {
                    moment.locale("de");
                    return moment(data).format("L");
                }
            }
        ],
    });
    // JS Funktion Ajax Daten für Bestellungspositionen
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
                        var row = data[i];
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
            {"data": "supplier_order_pos_quantity"},
            {
                "data": "lbw_menge",
                render: function (data, type, row) {
                    if (row["lbw_menge"] != null) {
                        return row["lbw_menge"];
                    } else {
                        return "0.000";
                    }
                },
            },
            {
                "data": null,
                render: function (data, type, row) {
                    if (row["lbw_menge"] != null) {
                        return parseInt(row["supplier_order_pos_quantity"]) - parseInt(row["lbw_menge"]);
                    } else {
                        return row["supplier_order_pos_quantity"];
                    }
                },
            }
        ],
        columnDefs: [
            {className: 'text-center', targets: [0, 1, 3, 4, 5]},
        ],
    });

// Durch Auswahl einer Zeile in der Bestellungs-Tabelle wird die Positions-Tabelle mit den entsprechenden Daten geladen.
    bstTable.on( 'click', function () {
        posTable.ajax.reload();

    } );

// Beim Abwählen der Zeile in der Bestellungs-Tabelle wird die Positions-Tabelle wieder geleert.
    bstTable.on( 'deselect', function () {
        //aftTable.rows().deselect();
        posTable.ajax.reload();
    } );

} );

// https://datatables.net/forums/discussion/53548/parent-child-datatables