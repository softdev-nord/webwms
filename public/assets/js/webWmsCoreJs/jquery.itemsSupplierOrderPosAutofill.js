const itemsAddNewRow = (function () {
    let rowcount, html, addButton, tableBody;

    addButton = $('#addNewButton');
    rowcount = $('#autocomplete_table tbody tr').length + 1;
    tableBody = $('#autocomplete_table tbody');

    function formHtml() {
        html = '<tr id="row_' + rowcount + '">';
        html += '<th id="delete_' + rowcount + '" scope="row" class="delete_row fs-22"><span class="mdi mdi-trash-can-outline"></span></th>';
        html += '<td>';
        html += '<input id="supplier_order_pos_articleNr_' + rowcount + '" type="text" data-type="article_nr" name="supplier_order_pos[articleNr]" class="form-control autocomplete_items" autocomplete="off">';
        html += '<input id="supplier_order_pos_articleId_' + rowcount + '" type="hidden" data-type="article_id" name="supplier_order_pos[articleId]" class="form-control autocomplete_items" autocomplete="off">';
        html += '</td>';
        html += '<td>';
        html += '<input id="supplier_order_pos_articleName_' + rowcount + '" type="text" data-type="article_name" name="supplier_order_pos[articleName]" class="form-control autocomplete_items" autocomplete="off">';
        html += '<input id="supplier_order_pos_supplierOrderId_' + rowcount + '" type="hidden" data-type="supplier_order_id" name="supplier_order_pos[supplierOrderId]" class="inputOrderId2" autocomplete="off" value="' + orderId + '">';
        html += '</td>';
        html += '<td>';
        html += '<input id="supplier_order_pos_supplierOrderPosQuantity_' + rowcount + '" type="text" data-type="supplier_order_pos_quantity" name="supplier_order_pos[supplierOrderPosQuantity]" class="form-control autocomplete_items" autocomplete="off">';
        html += '</td>';
        html += '</tr>';
        rowcount++;
        return html;
    }

    function getNumOfBoxArt(type) {
        let numOfBoxArt;
        switch (type) {
            case 'article_id':
                numOfBoxArt = 0;
                break;
            case 'article_nr':
                numOfBoxArt = 1;
                break;
            case 'article_name':
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

                $('#supplier_order_pos_articleId_' + numOfRow).val(resArr[0]);
                $('#supplier_order_pos_articleNr_' + numOfRow).val(resArr[1]);
                $('#supplier_order_pos_articleName_' + numOfRow).val(resArr[2]);
            }
        });
    }

    function getId(element) {
        let id, idArr;
        id = element.attr('id');
        idArr = id.split("_");
        return idArr[idArr.length - 1];
    }

    // Funktion neue Zeile hinzufügen
    function addNewRow(event) {
        event.preventDefault();
        tableBody.append(formHtml());
    }

    // Funktion Zeile löschen
    function deleteRow() {
        let currentElement, numOfRow;
        currentElement = $(this);
        numOfRow = getId(currentElement);
        $('#row_' + numOfRow).remove();
    }

    // Events registrieren
    function registerEventsArt() {
        addButton.on('click', addNewRow);
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