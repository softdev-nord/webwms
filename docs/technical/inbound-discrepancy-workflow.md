# Mengenabweichung und Sperrbestand

Der geplante Wareneingang erfasst neben der Sollmenge aus dem Avis eine Ist-Menge. Abweichungen werden als eigener, mandantenbezogener Vorgang in `wms_inbound_discrepancy` gespeichert.

## Zustandsmodell

| Auslöser | Abweichung | Bestand nach QS | Folgeschritt |
| --- | --- | --- | --- |
| Istmenge kleiner als Soll | `shortage` | `blocked` | Freigabe oder Ablehnung |
| Istmenge größer als Soll | `overage` | `blocked` | Freigabe oder Ablehnung |
| QS entscheidet Sperre | `quality_block` | `blocked` | Freigabe oder Ablehnung |

Eine offene Mengenabweichung kann in der QS nicht direkt freigegeben werden. Bei **Freigeben** führt `ResolveInboundDiscrepancyHandler` innerhalb einer Transaktion eine Statusumbuchung von `blocked` nach `available` am selben Lagerplatz aus. Bei **Ablehnen** bleibt die Ware gesperrt. Beide Entscheidungen speichern Grund, Benutzer und Zeitpunkt; ungültige oder wiederholte Übergänge werden abgelehnt.

## API

- Die Annahme unter `POST /api/v3/inbound/planned/{deliveryId}/lines/{lineId}/receive` akzeptiert `actualQuantity` und bei Abweichung `discrepancyReason`.
- `POST /api/v3/inbound/planned/receipts/{receiptId}/resolve` erwartet `action` (`release` oder `reject`) und `note`.
- Die Worklist liefert Soll-/Istmenge, Typ, Grund und Bearbeitungsstatus.

Die Aktionen sind über `inbound.planned.receive`, `inbound.planned.inspect` und `inbound.planned.resolve` getrennt autorisiert.
