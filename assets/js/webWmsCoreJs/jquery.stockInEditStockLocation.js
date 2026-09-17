// Vorausgewählten Lagerplatz ändern
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('edit-selected-stock-location')) {
        const stockLocationId = document.getElementById('stock_location_id').value;
        const url = '/edit_pre_selected_stock_location/id/' + stockLocationId;
        const headerText = 'Lagerplatz Korrektur';

        document.querySelector("#modalCenter .modal-dialog").style.maxWidth = '75%';
        document.querySelector('#modalCenter .modal-title').textContent = headerText;

        fetch(url)
            .then(response => response.text())
            .then(html => {
                document.getElementById("modal-content-ajax").innerHTML = html;

                // DataTable initialisieren (DataTables benötigt jQuery, aber es gibt Alternativen wie Tabulator)
                if (window.DataTable) {
                    new DataTable('#editStockLocationTable', {
                        pageLength: 10,
                        language: {
                            url: './resources/dataTable.German.json'
                        },
                        columnDefs: [
                            {
                                className: 'text-center',
                                targets: '_all'
                            }
                        ]
                    });
                }

                // Modal anzeigen (Bootstrap erfordert jQuery, kann aber mit Vanilla JS so geöffnet werden)
                let modal = new bootstrap.Modal(document.getElementById('modalCenter'));
                modal.show();
                document.getElementById('selectedStockLocationId').value = stockLocationId;
            })
            .catch(error => console.error('Fehler beim Laden des Modals.', error));
    }
});

// document.addEventListener("dblclick", function(event) {
//     const tr = event.target.closest("tr"); // Nur nach <tr> suchen
//     if (tr && tr.closest("#editStockLocationTable")) { // Prüfen, ob es in der Tabelle ist
//         tr.classList.toggle("selected");
//         console.log("Stock-Location-ID:", tr.getAttribute("stock-location-id"));
//     }
// });

console.log("Skript geladen!");

document.addEventListener("DOMContentLoaded", function () {
    console.log("DOM vollständig geladen!");
});

const editStockLocationTable = document.querySelector("#editStockLocationTable tbody");

// document.addEventListener("dblclick", function (event) {
//     const selectedStockLocationId = document.querySelector("#selectedStockLocationId").value;
//     const clickedRow = event.target.closest("tr");
//
//     if (!clickedRow || !selectedStockLocationId) return;
//
//     const newStockLocationId = clickedRow.getAttribute("stock-location-id");
//     if (!newStockLocationId) return;
//
//     fetch(`/lagerplatz_details/${newStockLocationId}`)
//         .then(response => response.json())
//         .then(data => {
//             if (data.success) {
//                 let targetRow = document.querySelector(`#myTable tr[stock-location-id='${selectedStockLocationId}']`);
//
//                 if (targetRow) {
//                     updateRow(targetRow, data.data, newStockLocationId);
//
//                     // Modal schließen
//                     let modal = document.querySelector("#stockLocationModal");
//                     if (modal) {
//                         let modalInstance = bootstrap.Modal.getInstance(modal);
//                         if (modalInstance) {
//                             modalInstance.hide();
//                         }
//                     }
//                 } else {
//                     alert("Fehler: Kein passender Eintrag gefunden.");
//                 }
//             } else {
//                 alert("Fehler: " + data.message);
//             }
//         })
//         .catch(() => alert("Fehler beim Abrufen der Lagerplatzdetails."));
// });
//
// function updateRow(row, stockData, newStockLocationId) {
//     row.querySelectorAll("input[name^='stock_in_final']").forEach(input => {
//         let type = input.getAttribute("data-type");
//
//         switch (type) {
//             case "stock_su_id":
//                 input.value = stockData.stock_su_id;
//                 break;
//             case "stock_system":
//                 input.value = stockData.stock_system;
//                 break;
//             case "stock_ln":
//                 input.value = stockData.stock_ln;
//                 break;
//             case "stock_fb":
//                 input.value = stockData.stock_fb;
//                 break;
//             case "stock_sp":
//                 input.value = stockData.stock_sp;
//                 break;
//             case "stock_tf":
//                 input.value = stockData.stock_tf;
//                 break;
//             case "stock_quantity":
//                 input.value = stockData.stock_quantity;
//                 break;
//             case "stock_tbe":
//                 input.value = stockData.stock_tbe;
//                 break;
//             case "stock_coordinate":
//                 input.value = stockData.stock_coordinate;
//                 break;
//             case "stock_location_id":
//                 input.value = newStockLocationId;
//                 break;
//         }
//     });
// }

document.addEventListener("dblclick", function (event) {
    let clickedRow = event.target.closest("tr"); // Doppelklick auf Zeile
    if (!clickedRow) return;

    let stockLocationId = clickedRow.getAttribute("stock-location-id");
    if (!stockLocationId) {
        console.warn("Keine stock-location-id gefunden!");
        return;
    }

    // Daten aus der angeklickten Zeile holen
    let newValues = [...clickedRow.children].map(td => td.textContent.trim());

    // Ziel-Zeile in putIntoStorageTable finden (erste Zeile mit stock-location-id Attribut)
    let targetRow = document.querySelector("#putIntoStorageTable tbody tr[stock-location-id]");
    if (!targetRow) {
        console.warn("Keine passende Zeile in putIntoStorageTable gefunden!");
        return;
    }

    // StockLocationId aktualisieren
    targetRow.setAttribute("stock-location-id", stockLocationId);

    // Felder der putIntoStorageTable aktualisieren
    let fieldMapping = {
        "stock_system": newValues[2],
        "stock_ln": newValues[3],
        "stock_fb": newValues[4],
        "stock_sp": newValues[5],
        "stock_tf": newValues[6],
        "stock_coordinate": newValues[0] // Hidden Input
    };

    for (let key in fieldMapping) {
        let input = targetRow.querySelector(`input[name^="stock_in_final"][name$="[${key}]"]`);
        if (input) {
            input.value = fieldMapping[key];
        }
    }

    console.log("Lagerplatz erfolgreich aktualisiert:", fieldMapping);

    // Modal schließen (ersetze 'modalId' mit der ID deines Modals)
    const modal = bootstrap.Modal(document.getElementById('editStockLocationModal'));
    modal.hide();
});