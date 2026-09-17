# Picklisten und Pickaufträge

## Modell

Eine `PickList` bündelt aktive Allokationen eines Mandanten. Für jede
Allokation entsteht eine sequenzierte Pickposition. Die Allokations-ID ist
zugleich Positions-ID und durch einen eindeutigen Index gegen doppelte Aufnahme
in mehrere Listen geschützt.

## Ablauf und Status

Picklisten wechseln von `open` über `assigned` und `in_progress` zu
`completed`. Positionen wechseln atomar von `open` nach `picked` oder
`shortage`. Nur der zugewiesene Benutzer darf bestätigen.

Bei `picked` verbraucht der bestehende Fulfillment-Prozess die Allokation und
schreibt den Bestandsledger. Bei `shortage` wird die Allokation vollständig
freigegeben. Positionsstatus und Allokationsübergang liegen in derselben
Datenbanktransaktion.

## Persistenz

- `wms_pick_list`: Code, Status, Zuweisung und Auditdaten;
- `wms_pick_task`: Reihenfolge, Allokation, Ergebnis und Notiz;
- Migration: `Version20260917130000`.

## Grenzen

Automatische Wegeoptimierung, Multi-Order-Picking, Teilmengen und Scanner-UI
folgen in späteren Slices. Eine Fehlmenge gibt aktuell die gesamte Allokation
frei.
