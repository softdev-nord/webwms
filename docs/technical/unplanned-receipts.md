# Ungeplanter Wareneingang in V3

Der Slice ergänzt den bestehenden geplanten Wareneingang um Eingänge ohne Bestellung oder Avis. Annahme und Buchung sind getrennte Zustände, damit die Ware vor der Bestandswirkung identifiziert und kontrolliert werden kann.

`wms_supplier` hält den mandantenbezogenen Lieferantenstamm. `wms_unplanned_receipt` speichert Kopf, Status und Auditdaten; `wms_unplanned_receipt_item` enthält Artikel, Annahmeplatz, Menge und Bestandsdimensionen. Beim Buchen werden alle Positionen in einer äußeren Transaktion über den bestehenden Inventory-Core verarbeitet. Negative oder doppelte Folgebuchungen werden durch Statusprüfung und Zeilensperre verhindert.

Die V3-Oberfläche liegt unter `/v3/inbound`, die JSON-API unter `/api/v3/unplanned-receipts`. Rechte für Lesen, Annehmen und Buchen sind getrennt.
