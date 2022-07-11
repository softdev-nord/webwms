(function($){
    /*$(document).on('change','select#selectStock',function() {

        $.ajax({
            type: 'GET',
            url: '/grafische_lagerbelegung',
            dataType: 'json',
            success: function (response) {
                console.log(response);
            }
        });

        $.ajax({
            url         : '/grafische_lagerbelegung',
            method      : 'GET',
            dataType    : 'html',
            success     : function(data) {
                console.log(data);
                console.log(this.url);
            }
        });

    });*/
    $('#stockTable').DataTable({
        "lengthChange": false,
        "searching": false,
        "info": false,
        "paging": false,
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
            {"data": "lagereinheit"},
            {"data": "art_nr"},
            {"data": "bezeichnung"},
            {"data": "trans_ein"},
            {"data": "trans_aus"},
            {"data": "lp_bestand"},
        ],
        columnDefs: [
            {className: 'text-center', targets: "_all"},
        ],
    });
})(jQuery);


