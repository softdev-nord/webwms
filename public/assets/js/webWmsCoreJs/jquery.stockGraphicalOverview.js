"use strict";

(function($){
    $(document).on('change','#selectStock',function(event) {
        const select = $('#selectStock option:selected').text();
        const url = '/stock_occupancy_ajax/stock_location_ln/' + select;

        $.ajaxSetup({ cache: false });

        $.ajax({
            method: 'GET',
            url: url,
            dataType: 'html',
            success: function (data) {
                $('#stockLocationDiv').load(url + " #stockLocationDiv > *");
                $('#stockSystem').load(url + " #stockSystem > *");
            }
        });
    });

    $(document).on('click','#stockCoordinate',function(event) {
        $(function(){
            // Changed the default modal width
            $("#modalCenter .modal-dialog").css('max-width', '70%');
        });

        const url = '/stock_occupancy_ajax/' + $(event.currentTarget).attr('data-target');
        const $el = $(event.currentTarget)
        const currentCoordinate =  $el.attr('data-target');
        const desc = 'Lagerplatz Details ';
        const stockComplete = $(event.currentTarget).attr('data-ln-komplett');
        $("#currentCoordinate").val(currentCoordinate);
        $('#modalCenter .modal-title').html(desc + stockComplete);
        $('#modalCenter').modal('show');

        $.ajax({
            method: 'GET',
            url: url,
            dataType: 'html',
            success: function (data) {
                $("#modal-content-ajax").html(data);
                $('#stockLocationTable').load(url + ' #stockLocationTable');
                //$('#stockSystem').replace("#stockSystem", #stockSystem);.load(url + ' #stockSystem');
            }
        });
    });

    $.contextMenu({
        selector: 'tr',
        trigger: 'right',
        callback: function(key, options, event) {
            const table = $('#showStockDetailTable').DataTable({paging: false, info: false, searching: false});
            const row = table.row(options.$trigger);
            //const article = new article();

            switch (key) {
                case 'show' :
                    console.log(row.data()[1]);
                    getStockOccupancyByArticle(row.data()[1]);
                    break;
                default :
                    break
            }
        },
        items: {
            "show": {name: "Artikel Lagerbelegungen", icon: false},
        }
    });

    function getStockOccupancyByArticle(article_nr) {
        const url = '/stock_occupancy_ajax_article/' + article_nr;
        const content = '<div class="modal-body"></div>';
        $("#modalCenter .modal-dialog").css('max-width', '90%');

        $('#modalCenter .modal-title').text("Artikel Lagerbelegungen");
        $("#modal-content-ajax").html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            method: 'GET',
            url: url,
            dataType: 'html',
            success: function (data) {
                $("#modal-content-ajax").html(data);
            }
        });
    }
})(jQuery);


