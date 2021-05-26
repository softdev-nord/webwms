
var customersAutofill = (function(){

	function getNumOfBoxKd(type){
		var numOfBoxKd;
		switch (type) {
			case 'customer_nr':
				numOfBoxKd = 0;
				break;
			case 'customer_name':
				numOfBoxKd = 1;
				break;
			case 'customer_address_addition':
				numOfBoxKd = 2;
				break;
			case 'customer_address_street':
				numOfBoxKd = 3;
				break;
			case 'customer_address_street_nr':
				numOfBoxKd = 4;
				break;
			case 'customer_country_code':
				numOfBoxKd = 5;
				break;
			case 'customer_zip_code':
				numOfBoxKd = 6;
				break;
			case 'customer_city':
				numOfBoxKd = 7;
				break;
			case 'id':
				numOfBoxKd = 8;
				break;
			default:
				break;
		}
		return numOfBoxKd;
	}
	// Handling der eingegebenen Daten
	function autocompleteHandleKd() {
		var type, numOfBoxKd;
		type = $(this).data('type');
		numOfBoxKd = getNumOfBoxKd(type);

		if(typeof numOfBoxKd === 'undefined') {
			return false;
		}
		// Autocomplete Ajax Datenübertragung in dem Datenformat JSON
		$(this).autocomplete({
			source: function( data, cb ) {
				$.ajax({
					'url':'/order_customer_ajax',
					'method': 'GET',
					//dataType: 'json',
					data: {
						name_kd: data.term,
						numOfBoxKd: numOfBoxKd
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
									label: arr[numOfBoxKd],
									value: arr[numOfBoxKd],
									data : obj
								};
							});
						}
						cb(result.slice(0, 5));
					}
				});
			},
			autoFocus: true,
			minLength: 1,
			select: function( event, ui ) {
				var resArrKd;
				//Splittung des resArrKd Arrays
				resArrKd = ui.item.data.split("|");

				$('#customer_nr_1').val(resArrKd[0]);
				$('#customer_name_1').val(resArrKd[1]);
				$('#customer_address_addition_1').val(resArrKd[2]);
				$('#customer_address_street_1').val(resArrKd[3]);
				$('#customer_address_street_nr_1').val(resArrKd[4]);
				$('#customer_country_code_1').val(resArrKd[5]);
				$('#customer_zip_code_1').val(resArrKd[6]);
				$('#customer_city_1').val(resArrKd[7]);
				$('#customer_order_customer_id').val(resArrKd[8]);
			}
		});
	}

	// Funktion Events registrieren
	function registerEventKd() {
		//Registrierung der Autocomplete events
		$(document).on('focus','.autocomplete_customers', autocompleteHandleKd);
	}
	// Events iniziieren
	function init() {
		registerEventKd();
	}
	return {
		init: init
	};
})();

$(document).ready(function(){
	customersAutofill.init();
});
