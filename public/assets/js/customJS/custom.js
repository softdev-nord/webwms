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
	$("#inputBstDat")
		.datepicker({ dateFormat: "dd.mm.yy" }).val();
	$("#inputAftDat")
		.datepicker({ dateFormat: "dd.mm.yy" }).val();

});
$( function(){
	$.datepicker.setDefaults($.datepicker.regional["de"]);
});

// Autovervollständigung für Menüpunkt Suche
$(function autocompleteMenu(){
	var links;
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

// SubMenu Tooltip
$(document).ready(function() {
	new jBox('Tooltip', {
		closeOnMouseleave: true,
		animation: 'zoomIn',
		theme: 'TooltipBorder',
		attach: '#abmelden',
		position: {
			x: 'center',
			y: 'bottom'
		},
		content: 'Abmelden'
	});

	new jBox('Tooltip', {
		closeOnMouseleave: true,
		animation: 'zoomIn',
		theme: 'TooltipBorder',
		attach: '#bestellungen',
		position: {
			x: 'center',
			y: 'bottom'
		},
		content: 'Bestellungen'
	});

	new jBox('Tooltip', {
		closeOnMouseleave: true,
		animation: 'zoomIn',
		theme: 'TooltipBorder',
		attach: '#bestellung_anlegen',
		position: {
			x: 'center',
			y: 'bottom'
		},
		content: 'Bestellung anlegen'
	});

	new jBox('Tooltip', {
		closeOnMouseleave: true,
		animation: 'zoomIn',
		theme: 'TooltipBorder',
		attach: '#auftraege',
		position: {
			x: 'center',
			y: 'bottom'
		},
		content: 'Aufträge'
	});

	new jBox('Tooltip', {
		closeOnMouseleave: true,
		animation: 'zoomIn',
		theme: 'TooltipBorder',
		attach: '#auftrag_anlegen',
		position: {
			x: 'center',
			y: 'bottom'
		},
		content: 'Auftrag anlegen'
	});

	new jBox('Tooltip', {
		closeOnMouseleave: true,
		animation: 'zoomIn',
		theme: 'TooltipBorder',
		attach: '#startseite',
		position: {
			x: 'center',
			y: 'bottom'
		},
		content: 'Startseite'
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
			{"data": "art_nr"},
			{"data": "art_name"},
			{"data": "pos_quantity"},
			{"data": "username"},
			{"data": "access_date"},
			{"data": "dispatch_date"}
		],
		"columnDefs": [
			{
				targets: [10], render: function (data) {
					moment.locale("de");
					return moment(data).format("L");
				}
			}
		],
	});
})

// JS Funktion Ajax Daten für Übersicht Lagerbewegungen
$(function() {
	var artTable = $('#artTable').DataTable({
		"lengthChange": false,
		// Ajax-Anfrage via PHP (Json)
		ajax: {
			'url': '/article_ajax',
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
			{"data": "art_nr"},
			{"data": "art_name"},
			{"data": "art_kat"},
			{"data": "art_gew"},
			{"data": "art_ean"},
			{"data": "art_einh"},
			{"data": "art_tiefe"},
			{"data": "art_breite"},
			{"data": "art_hoehe"},
			{
				"data": "lbw_menge",
			 	"defaultContent": 0
			}
		],
	});
})