# Bestandsreservierung und Auftragsallokation

## Modell

Eine `StockReservation` erfasst den Bedarf einer Auftragsreferenz für einen
Artikel. Eine `StockAllocation` bindet eine Teilmenge dieses Bedarfs an eine
konkrete Bestandsdimension aus Lagerplatz, Status, Charge, Seriennummer und MHD.

Es gilt:

`verfügbar = physischer Bestand - Summe aktiver Allokationen`

Teilallokationen sind zulässig. Der Reservierungsstatus wechselt von `open` über
`partially_allocated` zu `allocated`.

## Transaktions- und Parallelitätsregeln

Beim Allokieren sperrt der DBAL-Adapter zuerst die Reservierung und anschließend
die betroffene Bestandsbalance mit `SELECT ... FOR UPDATE`. Danach prüft er
Auftragsrestmenge und verfügbaren Bestand, schreibt die Allokation und
aktualisiert die Reservierung in derselben Transaktion.

Negative Bestandsbuchungen und Umlagerungen prüfen ebenfalls die Summe aktiver
Allokationen. Physischer Bestand darf dadurch nicht unter die reservierte Menge
fallen.

## Persistenz

- `wms_stock_reservation`: Auftragsbedarf, Soll- und allokierte Menge, Status;
- `wms_stock_allocation`: konkrete Bestandsbindung und deren Status;
- Migration: `Version20260917120000`.

Die Kombination aus Mandant, Auftragsreferenz und Artikel ist eindeutig.
Suchindizes unterstützen offene Reservierungen und aktive Allokationen je
Bestandsschlüssel.

## Regeln und Grenzen

- Reservierungs- und Allokationsmengen sind positive ganze Basiseinheiten.
- Eine Seriennummer wird immer einzeln allokiert.
- Es kann weder über den Auftragsbedarf noch über den verfügbaren Bestand hinaus
  allokiert werden.
- Mandant, Artikel, Lagerplatz und Benutzer werden referenziell geprüft.
- Freigabe, Verbrauch beim Picking, Prioritäten und automatische FEFO-Auswahl
  folgen als explizite Zustandsübergänge im nächsten Fulfillment-Slice.
