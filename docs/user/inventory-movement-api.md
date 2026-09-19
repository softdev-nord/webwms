# Bestände über API v3 umbuchen

Die API kann Bestand zwischen Lagerplätzen verschieben, einen Bestandsstatus
ändern und das vollständige Bewegungsjournal bereitstellen.

## Bestand umlagern

```http
POST /api/v3/stock-transfers
X-API-Key: CLIENT_UUID.SECRET
Content-Type: application/json

{
  "id": "0199b9a0-ff10-7b40-9fc8-d5b992442901",
  "productId": "PRODUCT_UUID",
  "sourceLocationId": "SOURCE_LOCATION_UUID",
  "destinationLocationId": "TARGET_LOCATION_UUID",
  "quantity": 5,
  "reason": "Umlagerung in Pickzone",
  "sourceStatus": "available",
  "destinationStatus": "available",
  "batchNumber": "LOT-2026-09",
  "expiresAt": "2027-09-30"
}
```

`id` ist optional, für Integrationen aber empfohlen. Wird dieselbe Anfrage nach
einem Timeout wiederholt, verhindert diese ID eine doppelte Bestandsbewegung.
Die Antwort enthält die Transfer-ID, beide Ledger-IDs und die neuen Quell- und
Zielmengen.

## Status umbuchen

Für eine Statusänderung bleiben Quell- und Ziellagerplatz gleich. Nur die
Statuswerte unterscheiden sich:

```json
{
  "productId": "PRODUCT_UUID",
  "sourceLocationId": "LOCATION_UUID",
  "destinationLocationId": "LOCATION_UUID",
  "quantity": 1,
  "reason": "QS-Freigabe",
  "sourceStatus": "quality_inspection",
  "destinationStatus": "available",
  "batchNumber": "LOT-2026-09"
}
```

Gültige Statuswerte sind `available`, `blocked` und `quality_inspection`.
Charge, Seriennummer und MHD müssen den vorhandenen Bestand eindeutig
beschreiben. Eine Seriennummer kann nur mit Menge 1 bewegt werden.

## Bewegungen prüfen

```http
GET /api/v3/stock-movements?transferId=TRANSFER_UUID
X-API-Key: CLIENT_UUID.SECRET
```

Für eine Umlagerung erscheinen zwei Einträge: `transfer_out` für die Quelle
und `transfer_in` für das Ziel. Zusätzlich kann nach `productId`, `locationId`
oder `movementType` gefiltert werden. `meta.nextCursor` führt zur nächsten
Seite.

Bei zu geringem oder reserviertem Bestand führt die API keine Teilbuchung aus.
Quelle und Ziel bleiben vollständig unverändert.
