# Bestand reservieren und allokieren

## Reservierung anlegen

Eine Reservierung verbindet einen Artikel und eine gewünschte Menge mit einer
Auftragsreferenz, zum Beispiel `SO-2026-1001`. Sie beschreibt zunächst den
Bedarf; konkreter Lagerbestand wird erst durch die Allokation gebunden.

## Bestand allokieren

Bei der Allokation wählen Sie den konkreten Bestand anhand von:

- Lagerplatz;
- Bestandsstatus;
- Charge und MHD;
- gegebenenfalls Seriennummer.

Eine Reservierung kann aus mehreren Lagerplätzen oder Chargen erfüllt werden.
WebWMS zeigt nach jeder Allokation die insgesamt allokierte Menge, die offene
Auftragsmenge und den verbleibenden verfügbaren Bestand zurück.

## Schutz vor Überbuchung

WebWMS sperrt Reservierung und Bestand während der Allokation. Parallel
bearbeitete Aufträge können deshalb nicht dieselbe verfügbare Menge erhalten.
Auch eine Korrekturbuchung oder Umlagerung wird abgelehnt, wenn sie bereits
allokierten Bestand unterschreiten würde.

## Status

| Status | Bedeutung |
|---|---|
| `open` | Noch kein Bestand ist allokiert. |
| `partially_allocated` | Ein Teil der Auftragsmenge ist gebunden. |
| `allocated` | Die komplette Auftragsmenge ist gebunden. |

Freigabe und tatsächlicher Verbrauch einer Allokation werden mit dem kommenden
Picking- und Fulfillment-Prozess ergänzt.
