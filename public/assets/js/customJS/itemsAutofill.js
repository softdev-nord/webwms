
var itemsAddNewRow = (function(){
	var rowcount, html, addButton, tableBody;

	addButton = $("#addNewButton");
	rowcount = $("#autocomplete_table tbody tr").length+1;
	tableBody = $("#autocomplete_table tbody");

	function formHtml() {
		html = '<tr id="row_'+rowcount+'">';
		html += '<th id="delete_'+rowcount+'" scope="row" class="delete_row"><span class="glyphicon glyphicon-minus-sign"></span></th>';
		html += '<td>';
		html += '<input type="text" data-type="art_nr" name="inputArtNr" id="art_nr_'+rowcount+'" class="form-control autocomplete_txt" autocomplete="off">';
		html += '<input type="hidden" data-type="id" name="inputArtId" id="art_id_'+rowcount+'" class="form-control autocomplete_txt" autocomplete="off">';
		html += '</td>';
		html += '<td>';
		html += '<input type="text" data-type="art_name" name="inputArtName" id="art_name_'+rowcount+'" class="form-control autocomplete_txt" autocomplete="off">';
		html += '</td>';
		html += '<td>';
		html += '<input type="text" data-type="aft_pos_menge" name="inputAftPosMenge" id="aft_pos_menge_'+rowcount+'" class="form-control autocomplete_txt" autocomplete="off">';
		html += '</td>';
		html += '</tr>';
		rowcount++;
		return html;
	}

	function getNumOfBoxArt(type){
		var numOfBoxArt;
		switch (type) {
			case 'art_nr':
				numOfBoxArt = 0;
				break;
			case 'art_name':
				numOfBoxArt = 1;
				break;
			case 'id':
				numOfBoxArt = 2;
				break;
			default:
				break;
		}
		return numOfBoxArt;
	}

	function autocompleteHandleArt() {
		var type, numOfBoxArt, currentElement;
		type = $(this).data('type');
		numOfBoxArt = getNumOfBoxArt(type);
		currentElement = $(this);

		if(typeof numOfBoxArt === 'undefined') {
			return false;
		}

		$(this).autocomplete({
			source: function( data, cb ) {
				$.ajax({
					'url':'/article_order_ajax',
					method: 'GET',
					'dataType': 'json',
					data: {
						name_art:  data.term,
						numOfBoxArt: numOfBoxArt
					},
					success: function(res){
						var result;
						result = [
							{
								label: 'Keine Ergebnisse für '+data.term,
								value: ''
							}
						];

						if (res.length) {
							result = $.map(res, function(obj){
								var arr = obj.split("|");
								return {
									label: arr[numOfBoxArt],
									value: arr[numOfBoxArt],
									data : obj
								};
							});
						}
						cb(result);
					}
				});
			},
			autoFocus: true,
			minLength: 1,
			select: function( event, ui ) {
				var resArr, numOfRow;

				numOfRow = getId(currentElement);
				resArr = ui.item.data.split("|");

				$('#art_nr_'+numOfRow).val(resArr[0]);
				$('#art_name_'+numOfRow).val(resArr[1]);
				$('#id_'+numOfRow).val(resArr[2]);
			}
		});
	}

	function getId(element){
		var id, idArr;
		id = element.attr('id');
		idArr = id.split("_");
		return idArr[idArr.length - 1];
	}

//Funktion neue Zeile hinzufügen
	function addNewRow() {
		tableBody.append( formHtml() );
	}

//Funktion Zeile löschen
	function deleteRow() {
		var currentElement, numOfRow;
		currentElement = $(this);
		numOfRow = getId(currentElement);
		$("#row_"+numOfRow).remove();
	}

// Events registrieren
	function registerEventsArt() {
		addButton.on("click", addNewRow);
		$(document).on('click', '.delete_row', deleteRow);
		$(document).on('focus','.autocomplete_txt', autocompleteHandleArt);

	}
// Events iniziieren
	function init() {
		registerEventsArt();
	}

	return {
		init: init
	};
})();

$(document).ready(function(){
	itemsAddNewRow.init();
});