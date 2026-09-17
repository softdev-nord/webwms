# Bestandsmerkmale

## Umfang

Dieser Inventory-Slice erweitert Bestände und Journalbuchungen um:

- Bestandsstatus `available`, `blocked` und `quality_inspection`;
- optionale Charge;
- optionale Seriennummer;
- optionales Mindesthaltbarkeitsdatum (MHD).

`StockDimensions` normalisiert Chargen und Seriennummern in Großbuchstaben und
bildet aus allen vier Merkmalen einen stabilen SHA-256-Bestandsschlüssel. Damit
führt jede Merkmalskombination am selben Artikel und Lagerplatz einen getrennten
Bestand.

## Persistenz und Migration

Die Tabellen `wms_stock_balance` und `wms_stock_ledger` erhalten die Spalten
`stock_key`, `stock_status`, `batch_number`, `serial_number` und `expires_at`.
Der Primärschlüssel der Balance enthält zusätzlich `stock_key`. Indizes auf
Charge und MHD bereiten spätere Such- und FEFO-Prozesse vor.

Migration: `Version20260917111500`.

Vorhandene Bestände und Journaleinträge werden als `available` ohne Charge,
Seriennummer oder MHD übernommen. Ihr vorbelegter Schlüssel entspricht exakt
dem von `StockDimensions` berechneten Standardschlüssel, sodass Folgebuchungen
dieselbe Balance fortschreiben.

## Regeln

- Statuswerte außerhalb des Enums werden abgelehnt.
- Leere Charge oder Seriennummer wird als nicht gesetzt behandelt.
- Charge und Seriennummer haben maximal 100 Zeichen.
- Das MHD wird als reines Kalenderdatum gespeichert.
- Seriennummern werden ausschließlich einzeln gebucht; ihre Balance darf nur
  null oder ein Stück betragen.
- Negative Bestände bleiben je vollständiger Merkmalskombination verboten.
- Jede Buchung übernimmt alle Merkmale unveränderlich in das Bestandsjournal.

## Bekannte Einschränkungen

Statuswechsel und Umlagerungen werden derzeit als zusammengehörige Aus- und
Einbuchungen durch den aufrufenden Prozess modelliert. Eine atomare
Transfer-Anwendung, FEFO-Reservierung, MHD-Warnungen und seriennummernweite
Eindeutigkeit über mehrere Lagerplätze folgen in späteren Slices.
