// Hervorhebung der zweite Ebene im Menü, wenn die dritte ausgewählt (mouseover) wurde
$(function(){
	$(".dropdown-menu")
		.mouseenter(function(){
		$(this).parent('li').addClass('active');
		})
		.mouseleave(function(){
			$(this).parent('li').removeClass('active');
		});
});

// Datepicker für Auftrag anlegen
$( function() {
	$("#customer_order_customer_order_creation_date")
		.datepicker({ dateFormat: "dd.mm.yy" }).val();
	$("#customer_order_customer_order_date")
		.datepicker({ dateFormat: "dd.mm.yy" }).val();

});

// Datepicker für Bestellung anlegen
$( function() {
	$("#supplier_order_supplier_order_date")
		.datepicker({ dateFormat: "dd.mm.yy" }).val();
	$("#supplier_order_supplier_order_creation_date")
		.datepicker({ dateFormat: "dd.mm.yy" }).val();
});

$( function(){
	$.datepicker.setDefaults($.datepicker.regional["de"]);
});

// Autovervollständigung für Menüpunkt Suche
$(function autocompleteMenu(){
	let links;
	$( "#menu-links" ).autocomplete({
		minlength:3,
		source: function( request, response ){
			var reg = new RegExp(request.term, 'i');
			links = $.ajax({
				url: "./resources/page-navigation.json",
				dataType: "json",
				success: function(data){
					response($.map(data.list, function(item){
						if(reg.exec(item.label)){
							return {
								label: item.label,
								value: item.value
							};
						}
					}));
				}
			});
		},
		select: function( event, ui ) {
			window.location.href = ui.item.value
		}
	});
});

//DataTables
$(function(){
	$('#Table').dataTable({
		"pagingType": "full_numbers",
		"pageLength": 10,
		"lengthChange": false,
		"language": {
			"url": "./resources/dataTable.German.json"
		},
		dom: 'Bfrtip',
		buttons: [
			{
				extend:    'copyHtml5',
				text:      'Kopieren',
				title:     'Export',
				titleAttr: 'Copy'
			},
			{
				extend:    'excelHtml5',
				text:      'Excel',
				title:     'Export',
				titleAttr: 'Excel'
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
	$('#TablePos').dataTable({
		"pagingType": "full_numbers",
		"pageLength": 5,
		"lengthChange": false,
		"language": {
			"url": "./resources/dataTable.German.json"
		}
	}); 
});

// JS Funktion Ajax Daten für Übersicht Lagerbewegungen
$(function() {
	var strTable = $('#strTable').DataTable({
		"lengthChange": false,
		// Ajax-Anfrage via PHP (Json)
		ajax: {
			'url': '/stock_rotation_ajax',
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
			{"data": "id"},
			{"data": "bm_short"},
			{"data": "bm_desc"},
			{"data": "stock_location"},
			{"data": "stock_location_desc"},
			{"data": "article_nr"},
			{"data": "article_name"},
			{"data": "pos_quantity"},
			{"data": "username"},
			{"data": "access_date"},
			{"data": "dispatch_date"}
		],
		"columnDefs": [
			{ className: 'text-center', targets: "_all" },
			{
				targets: [10], render: function (data) {
					moment.locale("de");
					return moment(data).format("L");
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
				extend:    'excelHtml5',
				text:      'Excel',
				title:     'Export',
				titleAttr: 'Excel'
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
})

// JS Funktion Ajax Daten für Übersicht Artikel



// JS Funktion Ajax Daten für Übersicht Lagerbelegung
$(function() {
	const artTable = $('#stoTable').DataTable({
		"lengthChange": false,
		// Ajax-Anfrage via PHP (Json)
		ajax: {
			'url': '/stock_occupancy_ajax',
			'dataSrc': ''
		},
		// Seitenlänge max. 15 Einträge
		pageLength: 15,
		"language": {
			"url": "./resources/dataTable.German.json"
		},
		// Initialisierung der DataTables Select-Erweiterung
		select: {
			style: 'single'
		},
		columns: [
			{"data": "koordinate"},
			{"data": "ln"},
			{"data": "fb"},
			{"data": "sp"},
			{"data": "tf"},
			{"data": "lagereinheit"},
			{"data": "art_nr"},
			{"data": "bezeichnung"},
			{"data": "trans_ein"},
			{"data": "trans_aus"},
			{"data": "lp_bestand"},
			{"data": "letzter_zugang"},
			{"data": "letzter_abgang"},
		],
		columnDefs: [
			{className: 'text-center', targets: "_all"},
		],
	});
})