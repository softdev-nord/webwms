(function($){
    $(document).on('click','a#stockCoordinate',function(event) {
        event.stopPropagation();
        const stockTable = $('#stockTable').DataTable({
            "lengthChange": false,
            "searching": false,
            "info": false,
            "paging": false,
            "destroy": true,
            // Ajax-Anfrage via PHP (Json)
            ajax: {
                'url': '/stock_occupancy_ajax/' + $(event.currentTarget).attr('data-target'),
                'dataSrc': function ( json ) {
                    return json;
                },

            },
            // Seitenlänge max. 15 Einträge
            pageLength: 15,
            "language": {
                "sEmptyTable": "Keine Details für diesen Lagerplatz",
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

        const url = '/stock_location_ajax/' + $(event.currentTarget).attr('data-target');
        const $el = $(event.currentTarget)
        const currentCoordinate =  $el.attr('data-target');
        const desc = 'Lagerplatz Details ';
        const stockComplete = $(event.currentTarget).attr('data-ln-komplett');
        $(".modal-title").html(desc + stockComplete);
        $("#currentCoordinate").val(currentCoordinate);
        $('#showStockDetails').modal('show');
    });

    $(document).on('change','#selectStock',function(event) {
        const select = $('#selectStock option:selected').text();
        const url = '/stock_occupancy_ajax/stock_location_ln/' + select;

        console.log(select);

        $.ajax({
            method: 'GET',
            url         : url,
            dataType: "html",
            success     : function (data) {
                console.log(data);
                $('#stockLocationTable').load(url + ' #stockLocationTable');
            }
        });
    });
})(jQuery);


