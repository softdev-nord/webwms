# Inventory Core

## Umfang

Der erste Inventory-Slice stellt die Bestandsgrundlage bereit:

- mandantenbezogene Artikelreferenzen mit eindeutiger SKU;
- Lager je Standort;
- Lagerplätze je Lager;
- aktuellen Bestand je Artikel und Lagerplatz;
- unveränderliche Bestandsjournal-Einträge.

## Buchungsmodell

`PostStockHandler` erstellt eine validierte `StockPosting` und übergibt sie
an den Persistenz-Port. Der DBAL-Adapter führt innerhalb einer Transaktion aus:

1. Artikel und Lagerplatz im selben Mandanten prüfen;
2. Bestandszeile mit `SELECT ... FOR UPDATE` sperren;
3. neuen Bestand berechnen und negative Bestände verhindern;
4. Balance einfügen oder aktualisieren;
5. unveränderlichen Ledger-Eintrag schreiben.

Der Ledger speichert Delta, resultierenden Bestand, Grund, Benutzer und
Zeitpunkt. Bestandsänderungen ohne Ledger-Eintrag sind nicht vorgesehen.

## Tabellen

- `wms_product_reference`
- `wms_warehouse`
- `wms_storage_location`
- `wms_stock_balance`
- `wms_stock_ledger`

Migration: `Version20260917110000`.

## Aktuelle Grenzen

Mengen werden zunächst als ganze Basiseinheiten gespeichert. Chargen,
Seriennummern, MHD, Bestandsstatus, Handling Units und Dezimalmengen folgen in
weiteren Inventory-Slices.
