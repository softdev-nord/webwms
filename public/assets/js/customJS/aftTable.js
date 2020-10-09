// JS Funktion Ajax Daten für Übersicht Aufträge
$(function() {
	var aftTable = $('#aftTable').dataTable( {
		"lengthChange": false,
		// Ajax-Anfrage via PHP (Json)
		ajax: {
			'url':'../ajax/auftrag.ajax.php',
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
			{ "data": "aft_nr" },
			{ "data": "aft_ref" },
			{ "data": "kd_nr" },
			{ "data": "kd_name" },
			{ "data": "aft_aft_dat" },
			{ "data": "aft_bst_dat" },
			{ "data": "ben_log_name" }
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
	var posTable = $('#posTable').dataTable( {
		"searching": false,
		"lengthChange": false,
		"info": false,
		"language": {
			"url": "./resources/dataTable.German.json",
		},
		// Ajax-Anfrage via PHP (Json)
		ajax: {
			url:'../ajax/auftragPos.ajax.php',

			// Es werden nur die Daten in der Positions-Tabelle geladen,
			// die mit der ID in der Auftrags-Tabelle übereinstimmen.
			dataSrc: function (data) {
				var selected = aftTable.row( { selected: true } );
				var rows = [];

				if ( selected.any() ) {
					var aft_id = selected.data().aft_id;
					for (i=0; i < data.length; i++) {
						var row = data[i];
						if (row.aft_id === aft_id) {
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
			{"data": "aft_nr"},
			{"data": "art_nr"},
			{"data": "art_name"},
			{"data": "aft_pos_menge"},
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
						return parseInt(row["aft_pos_menge"]) + parseInt(row["lbw_menge"]);
					}else{
						return row["aft_pos_menge"];
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