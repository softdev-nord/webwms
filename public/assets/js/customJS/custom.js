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
	$("#customer_order_customer_order_order_date")
		.datepicker({ dateFormat: "dd.mm.yy" }).val();
	$("#customer_order_customer_order_date")
		.datepicker({ dateFormat: "dd.mm.yy" }).val();

});

// Datepicker für Bestellung anlegen
$( function() {
	$("#order_order_order_date")
		.datepicker({ dateFormat: "dd.mm.yy" }).val();
	$("#order_order_date")
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
		attach: '#startseite',
		position: {
			x: 'center',
			y: 'bottom'
		},
		content: 'Startseite'
	});

	new jBox('Tooltip', {
		closeOnMouseleave: true,
		animation: 'zoomIn',
		theme: 'TooltipBorder',
		attach: '#lieferanten',
		position: {
			x: 'center',
			y: 'bottom'
		},
		content: 'Lieferanten'
	});

	new jBox('Tooltip', {
		closeOnMouseleave: true,
		animation: 'zoomIn',
		theme: 'TooltipBorder',
		attach: '#lieferanten_anlegen',
		position: {
			x: 'center',
			y: 'bottom'
		},
		content: 'Lieferanten anlegen'
	});

	new jBox('Tooltip', {
		closeOnMouseleave: true,
		animation: 'zoomIn',
		theme: 'TooltipBorder',
		attach: '#kunden',
		position: {
			x: 'center',
			y: 'bottom'
		},
		content: 'Kunden'
	});

	new jBox('Tooltip', {
		closeOnMouseleave: true,
		animation: 'zoomIn',
		theme: 'TooltipBorder',
		attach: '#kunden_anlegen',
		position: {
			x: 'center',
			y: 'bottom'
		},
		content: 'Kunden anlegen'
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
		attach: '#transporteingang',
		position: {
			x: 'center',
			y: 'bottom'
		},
		content: 'Transporteingang'
	});

	new jBox('Tooltip', {
		closeOnMouseleave: true,
		animation: 'zoomIn',
		theme: 'TooltipBorder',
		attach: '#transportausgang',
		position: {
			x: 'center',
			y: 'bottom'
		},
		content: 'Transportausgang'
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
$(function() {
	const artTable = $('#artTable').DataTable({
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
			{"data": "article_nr"},
			{"data": "article_name"},
			{"data": "article_category"},
			{"data": "article_weight"},
			{"data": "article_ean"},
			{"data": "article_unit"},
			{"data": "article_depth"},
			{"data": "article_width"},
			{"data": "article_height"},
			{
				"data": "lbw_menge",
				"defaultContent": 0
			}
		],
		columnDefs: [
			{className: 'text-center', targets: "_all"},
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
	$(document).contextmenu({
		delegate: ".dataTable td",
		menu: [
			{title: "Filter", cmd: "filter", uiIcon: "ui-icon-volume-off ui-icon-filter"},
			{title: "Remove filter", cmd: "nofilter", uiIcon: "ui-icon-volume-off ui-icon-filter"}
		],
		select: function(event, ui) {
			const celltext = ui.target.text();
			const colvindex = ui.target.parent().children().index(ui.target);
			const colindex = $('table thead tr th:eq(' + colvindex + ')').data('column-index');
			switch(ui.cmd){
				case "filter":
					artTable
						.column( colindex )
						.search( '^' + celltext + '$', true )
						.draw();
					break;
				case "nofilter":
					artTable
						.search('')
						.columns().search('')
						.draw();
					break;
			}
		},
		beforeOpen: function(event, ui) {
			const $menu = ui.menu,
				$target = ui.target,
				extraData = ui.extraData;
			ui.menu.zIndex(9999);
		}
	});
})



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