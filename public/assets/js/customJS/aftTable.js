// JS Funktion Ajax Daten für Übersicht Aufträge
$(function() {
	var aftTable = $('#aftTable').DataTable( {
		"lengthChange": false,
		// Ajax-Anfrage via PHP (Json)
		ajax: {
			'url':'/customer_order_ajax',
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
			{ "data": "customer_order_nr" },
			{ "data": "customer_order_reference" },
			{ "data": "customer_nr" },
			{ "data": "customer_name" },
			{ "data": "customer_order_date" },
			{ "data": "customer_order_order_date" },
			{ "data": "username" }
		],
        "columnDefs": [
            { targets : [4,5], render:function ( data ) {
                moment.locale("de");
                    return moment(data).format("L");
                }
            }
        ],
	} );
	// JS Funktion Ajax Daten für Auftragspositionen
	var posTable = $('#posTable').DataTable( {
		"searching": false,
		"lengthChange": false,
		"info": false,
		"language": {
			"url": "./resources/dataTable.German.json",
		},
		// Ajax-Anfrage via PHP (Json)
		ajax: {
			'url':'/customer_order_pos_ajax',

			// Es werden nur die Daten in der Positions-Tabelle geladen,
			// die mit der ID in der Auftrags-Tabelle übereinstimmen.
			dataSrc: function (data) {
				var selected = aftTable.row( { selected: true } );
				var rows = [];

				if ( selected.any() ) {
					var customer_order_id = selected.data().customer_order_id;
					for (i=0; i < data.length; i++) {
						var row = data[i];
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
			{"data": "art_nr"},
			{"data": "art_name"},
			{"data": "customer_order_pos_quantity"},
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
			{"data": "lbw_menge",
				render:function ( data, type, row ) {
					if (row["lbw_menge"] != null){
						//console.log(row);
						return parseInt(row["customer_order_pos_quantity"]) + parseInt(row["lbw_menge"]);
					}else{
						return row["customer_order_pos_quantity"];
					}
				},
			}
		]
	} );

// Bei Auswahl einer Zeile in der Auftrags-Tabelle wird die Positions-Tabelle mit den entsprechenden Daten geladen.
	aftTable.on( 'select', function () {
		posTable.ajax.reload();

	} );

// Beim Abwählen der Zeile in der Auftrags-Tabelle wird die Positions-Tabelle wieder geleert.
	aftTable.on( 'deselect', function () {
		//aftTable.rows().deselect();
		posTable.ajax.reload();
	} );

} );