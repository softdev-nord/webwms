(function($){
    // Modal für Einlagern direkt
    $(document).on('click','a#stock_in',function(event) {
        const desc = 'Einlagern direkt';
        $(".modal-title").html(desc);
        $('#stock_in_modal').modal('show');
    });

    // Modal für Zugang aus Wareneingang
    $(document).on('click','a#stock_in_from_goods_receipt',function(event) {
        const desc = 'Zugang aus Wareneingang';
        $(".modal-title").html(desc);
        $('#stock_in_from_goods_receipt_modal').modal('show');
    });

    // Modal für Zugang aus Produktion
    $(document).on('click','a#stock_in_from_production',function(event) {
        const desc = 'Zugang aus Produktion';
        $(".modal-title").html(desc);
        $('#stock_in_from_production_modal').modal('show');
    });

    // Modal für Einlagern von Kostenstelle
    $(document).on('click','a#stock_in_from_cost_centre',function(event) {
        const desc = 'Einlagern von Kostenstelle';
        $(".modal-title").html(desc);
        $('#stock_in_from_cost_centre_modal').modal('show');
    });

    // Modal für Einlagern in Container
    $(document).on('click','a#stock_in_into_container',function(event) {
        const desc = 'Einlagern in Container';
        $(".modal-title").html(desc);
        $('#stock_in_into_container_modal').modal('show');
    });

    // Modal für Einlagern mit Ladehilfsmittel
    $(document).on('click','a#stock_in_using_loading_equipment',function(event) {
        const desc = 'Einlagern mit Ladehilfsmittel';
        $(".modal-title").html(desc);
        $('#stock_in_using_loading_equipment_modal').modal('show');
    });

    // Modal für Einlagern direkt in WE-Zone
    $(document).on('click','a#stock_in_into_receiving_area',function(event) {
        const desc = 'Einlagern direkt in WE-Zone';
        $(".modal-title").html(desc);
        $('#stock_in_into_receiving_area_modal').modal('show');
    });

    // Modal für Einlagern direkt in WA-Zone
    $(document).on('click','a#stock_in_into_dispatch_area',function(event) {
        const desc = 'Einlagern direkt in WA-Zone';
        $(".modal-title").html(desc);
        $('#stock_in_into_dispatch_area_modal').modal('show');
    });

    // Modal für WE zu Bestellung
    $(document).on('click','a#stock_in_for_supplier_order',function(event) {
        const desc = 'WE zu Bestellung';
        $(".modal-title").html(desc);
        $('#stock_in_for_supplier_order_modal').modal('show');
    });

    // Vorausgewählten Lagerplatz ändern
    $(document).on('click','i.edit-selected-stock-location',function(event) {
        const stockLocationId = $('#stock_location_id').val();
        const $form = $('form#stock-in-form-edit');
        const url = '/edit_pre_selected_stock_location/id/' + stockLocationId;
        const content = '<div class="modal-body"></div>';
        event.preventDefault();

        $('#modalCenter .modal-dialog').css('max-width', '50%');
        $('#modalCenter .modal-title').text('Lagerplatz Korrektur');
        $('#modal-content-ajax').html(content);
        $('#modalCenter').modal('show');

        $.ajax({
            url: url,
            type: 'GET',
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
            },
            success: function (data) {
                $('#modal-content-ajax').html(data);
            }
        });

        return false;
    });

    $(document).ready(function () {
        $('#editStockLocationTable tbody').on('click', 'tr:nth-child(1)', function () {
            console.log('Test');
        });

        $('#editStockLocationTable tbody').on('dblclick','tr',function(e){
            dataTable.rows(this).select();
        });
    });
})(jQuery);


