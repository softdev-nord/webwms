# Picking und Fulfillment

## Umfang

Aktive Allokationen unterstützen zwei atomare Zustandsübergänge:

- `active → released`: Bestand wird ohne physische Buchung wieder verfügbar;
- `active → consumed`: Bestand wird physisch ausgebucht und im Ledger erfasst.

## Transaktion

`transitionAllocation()` sperrt die aktive Allokation samt Reservierung. Beim
Verbrauch wird zusätzlich die konkrete Bestandsbalance gesperrt. Statuswechsel,
Reservierungsmengen, Balance und Ledger werden gemeinsam bestätigt oder
vollständig zurückgerollt. Dadurch kann eine Allokation auch bei parallelen
Requests nur einmal verarbeitet werden.

Der Ledger-Typ `allocation_consumption` enthält `allocation_id` und
`reservation_id`. Die Migration `Version20260917123000` ergänzt außerdem
`fulfilled_quantity` sowie Benutzer, Zeitpunkt und Grund des Übergangs.

## Reservierungsstatus

- `open`: keine aktive Allokation;
- `partially_allocated`: Teilmenge gebunden;
- `allocated`: gesamte noch offene Menge gebunden;
- `partially_fulfilled`: Teilmenge entnommen, aktuell nichts weiter gebunden;
- `fulfilled`: komplette Sollmenge entnommen.

## Grenzen

Teilentnahmen einer einzelnen Allokation, Picklisten, Scanner-Dialoge,
Packprozesse und Versandstatus folgen in späteren Slices.
