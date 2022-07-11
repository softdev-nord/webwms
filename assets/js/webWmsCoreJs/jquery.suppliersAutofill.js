const suppliersAutofill = (function () {
//
	function getNumOfBoxSupplier(type) {
		let numOfBoxSupplier;
		switch (type) {
			case 'supplier_nr':
				numOfBoxSupplier = 0;
				break;
			case 'supplier_name':
				numOfBoxSupplier = 1;
				break;
			case 'supplier_address_addition':
				numOfBoxSupplier = 2;
				break;
			case 'supplier_address_street':
				numOfBoxSupplier = 3;
				break;
			case 'supplier_address_street_nr':
				numOfBoxSupplier = 4;
				break;
			case 'supplier_country_code':
				numOfBoxSupplier = 5;
				break;
			case 'supplier_zip_code':
				numOfBoxSupplier = 6;
				break;
			case 'supplier_city':
				numOfBoxSupplier = 7;
				break;
			case 'supplier_id':
				numOfBoxSupplier = 8;
				break;
			default:
				break;
		}
		return numOfBoxSupplier;
	}

	function autocompleteHandleLief() {
		let type, numOfBoxSupplier;
		type = $(this).data('type');
		numOfBoxSupplier = getNumOfBoxSupplier(type);

		if (typeof numOfBoxSupplier === 'undefined') {
			return false;
		}

		$(this).autocomplete({
			source: function (data, cb) {
				$.ajax({
					url: '/order_supplier_ajax',
					method: 'GET',
					data: {
						name_supplier: data.term,
						numOfBoxSupplier: numOfBoxSupplier
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
									label: arr[numOfBoxSupplier],
									value: arr[numOfBoxSupplier],
									data: obj
								};
							});
						}
						cb(result.slice(0, 5));
					}
				});
			},
			autoFocus: true,
			minLength: 1,
			select: function (event, ui) {
				let resArraySupplier;

				resArraySupplier = ui.item.data.split("|");

				$('#supplier_nr_1').val(resArraySupplier[0]);
				$('#supplier_name_1').val(resArraySupplier[1]);
				$('#supplier_address_addition_1').val(resArraySupplier[2]);
				$('#supplier_address_street_1').val(resArraySupplier[3]);
				$('#supplier_address_street_nr_1').val(resArraySupplier[4]);
				$('#supplier_country_code_1').val(resArraySupplier[5]);
				$('#supplier_zip_code_1').val(resArraySupplier[6]);
				$('#supplier_city_1').val(resArraySupplier[7]);
				$('#supplier_id_1').val(resArraySupplier[8]);
			}
		});
	}

// Events registrieren
	function registerEventLief() {
		//register autocomplete events
		$(document).on('focus', '.autocomplete_suppliers', autocompleteHandleLief);
	}

// Events iniziieren
	function init() {
		registerEventLief();
	}

	return {
		init: init
	};
})();

$(document).ready(function(){
	suppliersAutofill.init();
});
