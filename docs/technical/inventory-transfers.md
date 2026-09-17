# Atomare Bestandsumlagerung und Statusumbuchung

## Umfang

Der Slice führt `TransferStockHandler` und das Domänenobjekt `StockTransfer`
für zwei Anwendungsfälle ein:

- Umlagerung eines Bestands auf einen anderen Lagerplatz;
- Umbuchung zwischen `available`, `blocked` und `quality_inspection`.

Lagerplatz und Status dürfen in einem Transfer gleichzeitig wechseln. Charge,
Seriennummer und MHD sind Identitätsmerkmale und müssen unverändert bleiben.

## Transaktionsablauf

Der DBAL-Adapter führt den gesamten Transfer in einer Datenbanktransaktion aus:

1. Artikel, Quell- und Ziellagerplatz sowie Benutzer im Mandanten prüfen;
2. Quell- und Zielbalance in deterministischer Schlüsselreihenfolge mit
   `SELECT ... FOR UPDATE` sperren;
3. ausreichenden Quellbestand und Seriennummernregeln prüfen;
4. beide Balances aktualisieren;
5. zwei korrelierte Journaleinträge schreiben;
6. Transaktion gemeinsam bestätigen oder vollständig zurückrollen.

Die deterministische Sperrreihenfolge reduziert Deadlock-Risiken bei
gegenläufigen parallelen Umlagerungen.

## Journal und Migration

`wms_stock_ledger` erhält:

- `movement_type` mit `posting`, `transfer_out` oder `transfer_in`;
- die optionale `transfer_id` zur Korrelation beider Transferseiten;
- einen eindeutigen Index auf `transfer_id` und `movement_type`.

Migration: `Version20260917113000`.

Bestehende Journaleinträge werden automatisch als `posting` übernommen. Jeder
Transfer benötigt eine Transfer-ID sowie getrennte IDs für Aus- und Eintrag.

## Domänenregeln

- Die Transfermenge ist positiv.
- Quelle und Ziel dürfen nicht identisch sein.
- Transfer-ID und beide Journal-IDs müssen verschieden sein.
- Charge, Seriennummer und MHD bleiben erhalten; der Status darf wechseln.
- Eine Seriennummer wird immer mit genau einem Stück transferiert.
- Quellbestände dürfen nicht negativ werden.
- Am Ziel darf eine seriennummerngeführte Balance höchstens ein Stück enthalten.

## Bekannte Einschränkungen

Reservierungen, Handling Units, Transferaufträge mit Zwischenstatus sowie
automatische Nachschub- und FEFO-Strategien sind noch nicht Bestandteil dieses
Slices.
