
var suppliersAutofill = (function(){
//
	function getNumOfBoxLief(type){
		var numOfBoxLief;
		switch (type) {
			case 'lief_nr':
				numOfBoxLief = 0;
				break;
			case 'lief_name':
				numOfBoxLief = 1;
				break;
			case 'lief_ans_zu':
				numOfBoxLief = 2;
				break;
			case 'lief_str':
				numOfBoxLief = 3;
				break;
			case 'lief_hnr':
				numOfBoxLief = 4;
				break;
			case 'lief_land_krz':
				numOfBoxLief = 5;
				break;
			case 'lief_plz':
				numOfBoxLief = 6;
				break;
			case 'lief_ort':
				numOfBoxLief = 7;
				break;
			case 'lief_id':
				numOfBoxLief = 8;
				break;
			default:
				break;
		}
		return numOfBoxLief;
	}

	function autocompleteHandleLief() {
		var type, numOfBoxLief;
		type = $(this).data('type');
		numOfBoxLief = getNumOfBoxLief(type);

		if(typeof numOfBoxLief === 'undefined') {
			return false;
		}

		$(this).autocomplete({
			source: function( data, cb ) {
				$.ajax({
					url:'../ajax/lieferanten.ajax.php',
					method: 'GET',
					dataType: 'json',
					data: {
						name_lief: data.term,
						numOfBoxLief: numOfBoxLief
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
									label: arr[numOfBoxLief],
									value: arr[numOfBoxLief],
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
				var resArrLief;

				resArrLief = ui.item.data.split("|");

				$('#lief_nr_1').val(resArrLief[0]);
				$('#lief_name_1').val(resArrLief[1]);
				$('#lief_ans_zu_1').val(resArrLief[2]);
				$('#lief_str_1').val(resArrLief[3]);
				$('#lief_hnr_1').val(resArrLief[4]);
				$('#lief_land_krz_1').val(resArrLief[5]);
				$('#lief_plz_1').val(resArrLief[6]);
				$('#lief_ort_1').val(resArrLief[7]);
				$('#lief_id_1').val(resArrLief[8]);
			}
		});
	}

// Events registrieren
	function registerEventLief() {
		//register autocomplete events
		$(document).on('focus','.autocomplete_suppliers', autocompleteHandleLief);
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
