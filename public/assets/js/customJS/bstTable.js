// JS Funktion Ajax Daten für Übersicht Bestellungen
$(function() {
    var bstTable = $('#bstTable').DataTable( {
        "lengthChange": false,
        // Ajax-Anfrage via PHP (Json)
        ajax: {
            'url':'/orders_ajax',
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
            { "data": "bst_nr" },
            { "data": "bst_ref" },
            { "data": "lief_nr" },
            { "data": "lief_name" },
            { "data": "bst_bst_dat" },
            { "data": "username" }
        ],
        "columnDefs": [
            { targets : [4], render:function ( data ) {
                    moment.locale("de");
                    return moment(data).format("L");
                }
            }
        ],
    } );
    // JS Funktion Ajax Daten für Bestellungspositionen
    var posTable = $('#posTable').DataTable( {
        "lengthChange": false,
        "searching": false,
        "info": false,
        "language": {
            "url": "./resources/dataTable.German.json",
        },
        // Ajax-Anfrage via PHP (Json)
        ajax: {
            'url':'/order_pos_ajax',

            // Es werden nur die Daten in der Positions-Tabelle geladen,
            // die mit der ID in der Bestellungs-Tabelle übereinstimmen.
            dataSrc: function (data) {
                var selected = bstTable.row( { selected: true } );
                var rows = [];

                if ( selected.any() ) {
                    var bst_id = selected.data().bst_id;
                    for (i=0; i < data.length; i++) {
                        var row = data[i];
                        if (row.bst_id === bst_id) {
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
            {"data": "bst_nr"},
            {"data": "art_nr"},
            {"data": "art_name"},
            {"data": "bst_pos_menge"},
            {"data": "lbw_menge",
                render:function ( data, type, row ) {
                    if (row["lbw_menge"] != null){
                        console.log(row);
                        return row["lbw_menge"];
                    }else{
                        return "0";
                    }
                },
            },
            {"data": null,
                render:function ( data, type, row ) {
                    if (row["lbw_menge"] != null){
                        console.log(row);
                        return parseInt(row["bst_pos_menge"]) - parseInt(row["lbw_menge"]);
                    }else{
                        return row["bst_pos_menge"];
                    }
                },
            }
        ]
    } );

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