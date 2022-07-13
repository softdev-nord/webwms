(function($){
    $(document).on('click','a#stockCoordinate',function(event) {
        event.stopPropagation();
        $('#stockTable').DataTable({
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
})(jQuery);


