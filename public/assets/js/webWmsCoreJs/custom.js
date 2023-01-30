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
