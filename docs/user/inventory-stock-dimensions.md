# Bestandsstatus, Chargen, Seriennummern und MHD

## Bestandsstatus

Jede Bestandsbuchung verwendet einen dieser Status:

| Status | Bedeutung |
|---|---|
| `available` | Bestand ist für normale Lagerprozesse verfügbar. |
| `blocked` | Bestand ist gesperrt und darf nicht regulär verwendet werden. |
| `quality_inspection` | Bestand wartet auf eine Qualitätsprüfung. |

Wird kein Status angegeben, verwendet WebWMS automatisch `available`.

## Charge und MHD

Bei chargengeführten Artikeln geben Sie die Chargennummer und, falls bekannt,
das Mindesthaltbarkeitsdatum an. WebWMS schreibt Chargennummern in
Großbuchstaben und führt unterschiedliche Chargen oder MHD-Daten als getrennte
Bestände. Beispiel: `lot-2026-01` wird zu `LOT-2026-01`.

Das System speichert das MHD als Datum ohne Uhrzeit. Das Datum allein sperrt
oder priorisiert den Bestand noch nicht automatisch.

## Seriennummern

Eine Seriennummer kennzeichnet genau ein Stück. Daher muss jede Ein- oder
Ausbuchung mit Seriennummer eine Menge von `1` beziehungsweise `-1` verwenden.
Eine doppelte Einbuchung derselben Seriennummer in denselben Merkmalsbestand
wird abgelehnt.

## Statuswechsel und Umlagerungen

Bis ein eigener Transferdialog verfügbar ist, besteht ein Wechsel aus zwei
zusammengehörigen Buchungen:

1. Bestand mit den bisherigen Merkmalen ausbuchen;
2. dieselbe Menge mit den neuen Merkmalen oder am neuen Lagerplatz einbuchen.

Verwenden Sie in beiden Buchungsgründen eine gemeinsame Referenz, damit der
Vorgang im Bestandsjournal eindeutig nachvollziehbar bleibt.
