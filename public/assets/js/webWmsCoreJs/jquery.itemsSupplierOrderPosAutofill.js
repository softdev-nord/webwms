const itemsAddNewRow = (function () {
	let rowcount, html, addButton, tableBody;

	addButton = $("#addNewButton");
	rowcount = $("#autocomplete_table tbody tr").length + 1;
	tableBody = $("#autocomplete_table tbody");

	function formHtml() {

		html = '<tr id="row_' + rowcount + '">';
		html += '<th id="delete_' + rowcount + '" scope="row" class="delete_row"><span class="glyphicon glyphicon-minus-sign"></span></th>';
		html += '<td>';
		html += '<input id="art_nr_' + rowcount + '" type="text" data-type="art_nr" name="inputArtNr[]" class="form-control autocomplete_items" autocomplete="off">';
		html += '<input id="id_' + rowcount + '" type="hidden" data-type="id" name="inputArtId[]" class="form-control autocomplete_items" autocomplete="off">';
		html += '</td>';
		html += '<td>';
		html += '<input id="art_name_' + rowcount + '" type="text" data-type="art_name" name="inputArtName[]" class="form-control autocomplete_items" autocomplete="off">';
		html += '<input id="order_id_' + rowcount + '" type="hidden" data-type="order_id" name="inputOrderId[]" class="inputOrderId2" autocomplete="off" value="' + orderId + 1 + '">';
		html += '</td>';
		html += '<td>';
		html += '<input id="order_pos_quantity_' + rowcount + '" type="text" data-type="order_pos_quantity" name="inputOrderPosQuantity[]" class="form-control autocomplete_items" autocomplete="off">';
		html += '</td>';
		html += '</tr>';
		rowcount++;
		return html;
	}

	function getNumOfBoxArt(type) {
		let numOfBoxArt;
		switch (type) {
			case 'id':
				numOfBoxArt = 0;
				break;
			case 'art_nr':
				numOfBoxArt = 1;
				break;
			case 'art_name':
				numOfBoxArt = 2;
				break;
			default:
				break;
		}
		return numOfBoxArt;
	}

	function autocompleteHandleArt() {
		let type, numOfBoxArt, currentElement;
		type = $(this).data('type');
		numOfBoxArt = getNumOfBoxArt(type);
		currentElement = $(this);

		if (typeof numOfBoxArt === 'undefined') {
			return false;
		}

		$(this).autocomplete({
			source: function (data, cb) {
				$.ajax({
					url: '/article_order_ajax',
					method: 'GET',
					dataType: 'json',
					data: {
						name_art: data.term,
						numOfBoxArt: numOfBoxArt
					},
					success: function (res) {
						let result;
						result = [
							{
								label: 'Keine Ergebnisse für ' + data.term,
								value: ''
							}
						];

						if (res.length) {
							result = $.map(res, function (obj) {
								const arr = obj.split("|");
								return {
									label: arr[numOfBoxArt],
									value: arr[numOfBoxArt],
									data: obj
								};
							});
						}
						cb(result);
					}
				});
			},
			autoFocus: true,
			minLength: 1,
			select: function (event, ui) {
				let resArr, numOfRow;

				numOfRow = getId(currentElement);
				resArr = ui.item.data.split("|");

				$('#id_' + numOfRow).val(resArr[0]);
				$('#art_nr_' + numOfRow).val(resArr[1]);
				$('#art_name_' + numOfRow).val(resArr[2]);
			}
		});
	}

	function getId(element) {
		let id, idArr;
		id = element.attr('id');
		idArr = id.split("_");
		return idArr[idArr.length - 1];
	}

	//Funktion neue Zeile hinzufügen
	function addNewRow() {
		tableBody.append(formHtml());
	}

	//Funktion Zeile löschen
	function deleteRow() {
		let currentElement, numOfRow;
		currentElement = $(this);
		numOfRow = getId(currentElement);
		$("#row_" + numOfRow).remove();
	}

	// Events registrieren
	function registerEventsArt() {
		addButton.on("click", addNewRow);
		$(document).on('click', '.delete_row', deleteRow);
		$(document).on('focus', '.autocomplete_items', autocompleteHandleArt);

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