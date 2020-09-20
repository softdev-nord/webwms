
var customersAutofill = (function(){

	function getNumOfBoxKd(type){
		var numOfBoxKd;
		switch (type) {
			case 'kd_nr':
				numOfBoxKd = 0;
				break;
			case 'kd_name':
				numOfBoxKd = 1;
				break;
			case 'kd_ans_zu':
				numOfBoxKd = 2;
				break;
			case 'kd_str':
				numOfBoxKd = 3;
				break;
			case 'kd_hnr':
				numOfBoxKd = 4;
				break;
			case 'kd_land_krz':
				numOfBoxKd = 5;
				break;
			case 'kd_plz':
				numOfBoxKd = 6;
				break;
			case 'kd_ort':
				numOfBoxKd = 7;
				break;
			case 'kd_id':
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
					url:'../ajax/kunden.ajax.php',
					method: 'GET',
					dataType: 'json',
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

				$('#kd_nr_1').val(resArrKd[0]);
				$('#kd_name_1').val(resArrKd[1]);
				$('#kd_ans_zu_1').val(resArrKd[2]);
				$('#kd_str_1').val(resArrKd[3]);
				$('#kd_hnr_1').val(resArrKd[4]);
				$('#kd_land_krz_1').val(resArrKd[5]);
				$('#kd_plz_1').val(resArrKd[6]);
				$('#kd_ort_1').val(resArrKd[7]);
				$('#kd_id_1').val(resArrKd[8]);
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
