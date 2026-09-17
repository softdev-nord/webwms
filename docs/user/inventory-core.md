# Artikel, Lagerplätze und Bestände

## Artikelreferenzen

Eine Artikelreferenz identifiziert einen lagerrelevanten Artikel über eine SKU
und einen Namen. SKUs werden in Großbuchstaben gespeichert und dürfen je
Mandant nur einmal vorkommen.

## Lagerstruktur

Ein Lager gehört zu einem Standort. Darin werden Lagerplätze mit eindeutigen
Codes angelegt. Beispiele:

- Lager: `WH-01`
- Lagerplatz: `A-01-02`

## Bestandsbuchungen

Eine Buchung enthält:

- Artikel;
- Lagerplatz;
- positive oder negative Menge;
- Buchungsgrund;
- ausführenden Benutzer;
- Buchungszeitpunkt.

Positive Mengen erhöhen, negative Mengen reduzieren den Bestand. Eine Buchung,
die einen negativen Bestand erzeugen würde, wird abgelehnt.

Jede erfolgreiche Änderung wird dauerhaft im Bestandsjournal protokolliert.
Dadurch bleiben Bestand, Ursache, Benutzer und resultierende Menge
nachvollziehbar.
