# Bestandsbewegungen in API v3

Der Slice vervollständigt den Backendumfang von `WEBWMS-084` um schreibende
Bestandsumlagerungen, Statusumbuchungen und ein lesbares Bewegungsjournal.

## Schreibmodell

`POST /api/v3/stock-transfers` delegiert ohne eigene Geschäftslogik an den
vorhandenen `TransferStockHandler`. Damit gelten dieselben Invarianten wie im
internen Bestandskern:

- Quell- und Zielbestand werden in einer DBAL-Transaktion gesperrt;
- negative oder bereits allokierte Quellbestände werden abgewiesen;
- Charge, Seriennummer und MHD bleiben beim Transfer erhalten;
- Serienbestände werden ausschließlich einzeln bewegt;
- Quelle und Ziel müssen sich im Lagerplatz oder Bestandsstatus unterscheiden;
- beide Ledger-Einträge tragen dieselbe `transfer_id`.

Eine reine Statusumbuchung verwendet denselben Lagerplatz für Quelle und Ziel,
aber unterschiedliche Werte für `sourceStatus` und `destinationStatus`.

Der Client darf `id` als UUID vorgeben. Bei einem technischen Retry verhindert
die eindeutige Kombination aus `transfer_id` und Bewegungsart eine zweite
Buchung. Ohne Vorgabe erzeugt die API eine UUIDv7. Die beiden Ledger-IDs werden
immer serverseitig erzeugt.

## Projektion

`GET /api/v3/stock-movements` liest `wms_stock_ledger` mandantengebunden und
ergänzt SKU und Lagerplatzcode. Unterstützte Filter:

| Parameter | Bedeutung |
| --- | --- |
| `productId` | Bewegungen eines Artikels |
| `locationId` | Bewegungen eines Lagerplatzes |
| `transferId` | beide Seiten einer Umlagerung |
| `movementType` | eine gültige `StockMovementType` |
| `cursor` | letzte Ledger-ID der vorherigen Seite |
| `limit` | 1 bis 100 Einträge |

`StockMovementCriteria` validiert Filter vor dem Datenbankzugriff. Die Abfrage
bindet den Mandanten ausschließlich aus `TenantPermissionUser` ein.

## Berechtigungen

- `inventory.stock.movement.read`: Bewegungsjournal lesen;
- `inventory.stock.transfer`: Umlagerungen und Statusumbuchungen durchführen.

## Fehlerverhalten

Ungültige Felder und Filter liefern 422. Fehlende fachliche Referenzen liefern
404. Nicht ausreichender oder allokierter Bestand sowie wiederverwendete
Transfer-IDs führen zu 409. Antworten folgen dem Problem-JSON-Format der API
v3.

## Nachweise

Der Slice verwendet die vorhandenen Domain- und Application-Tests für
`StockTransfer` und `TransferStockHandler`. Ergänzende Tests decken gültige und
ungültige Bewegungsfilter ab. Eine neue Migration ist nicht erforderlich, da
Ledger, Dimensionsfelder, Bewegungsart und Transferkorrelation bereits
versioniert sind.
