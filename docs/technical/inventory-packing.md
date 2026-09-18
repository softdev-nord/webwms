# Packprozess

## Modell

Ein Packauftrag referenziert genau eine abgeschlossene Pickliste. Packstücke
enthalten eine eindeutige Nummer, ein Gewicht in Gramm und mindestens eine
erfolgreich gepickte Position. Eine Pickposition darf durch einen Datenbankindex
global nur einmal verpackt werden.

## Ablauf

1. Packauftrag aus einer abgeschlossenen Pickliste erzeugen (`open`).
2. Ein oder mehrere Packstücke mit Positionen erfassen (`packing`).
3. Vollständigkeit prüfen und Auftrag abschließen (`completed`).

Alle Änderungen erfolgen transaktional. Beim Abschluss vergleicht der Adapter
die Anzahl erfolgreich gepickter Positionen mit den verpackten Positionen und
akzeptiert ausschließlich versiegelte Packstücke. Fehlmengenpositionen müssen
nicht verpackt werden.

## Persistenz

- `wms_packing_order`: Quelle, Status und Abschlussaudit;
- `wms_package`: Packstücknummer, Gewicht und Packer;
- `wms_package_item`: eindeutige Zuordnung einer Pickposition;
- Migration `Version20260917133000`.

Die API-v3-Anbindung ist unter [Packprozess über API v3](packing-api.md)
dokumentiert. Dieser Slice benötigt keine zusätzliche Migration, da das
bestehende normalisierte Packmodell unverändert verwendet wird.

## Grenzen

Abmessungen, Verpackungsmittel, Umpacken, Etikettendruck, Carrier und
Gefahrgutprüfung folgen in späteren Slices.
