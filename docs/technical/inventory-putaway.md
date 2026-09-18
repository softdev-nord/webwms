# Automatische Einlagerung und Lagerplatzstrategien

## Strategie

Eine Einlagerungsstrategie gehört zu einem Mandanten und Lager. Sie definiert:

- den zulässigen Bestandsstatus (`available`, `blocked` oder
  `quality_inspection`);
- ein Zielplatz-Präfix als Lagerzone;
- eine fachliche Priorität;
- den Aktivierungsstatus.

Lagerplätze besitzen zusätzlich `putaway_enabled`, `putaway_priority` und eine
optionale `capacity_quantity`. Eine Kapazität von null bedeutet unbegrenzt.

## Zielplatzermittlung

Aus einer geprüften Wareneingangsposition wird genau ein Einlagerungsauftrag
erzeugt. Die Auswahl berücksichtigt in dieser Reihenfolge:

1. Strategiepriorität;
2. Konsolidierung desselben Artikels mit identischem Bestands-Key;
3. Lagerplatzpriorität;
4. Lagerplatzcode.

Quelle und Ziel müssen im selben Lager liegen. Deaktivierte Plätze, die Quelle
selbst sowie Plätze ohne freie Kapazität werden ausgeschlossen. Offene
Einlagerungsaufträge zählen bereits gegen die Kapazität. Auswahl und Anlage
erfolgen innerhalb einer Transaktion mit Zeilensperren.

## Ausführung

Die Bestätigung erzeugt eine bestehende atomare `StockTransfer`-Bewegung. Dabei
bleiben Status, Charge, Seriennummer und MHD unverändert. Erst nach erfolgreicher
Quell- und Zielbuchung wechselt der Auftrag von `open` auf `completed` und
speichert Transfer, Benutzer und Zeitpunkt.

## Persistenz

- zusätzliche Steuerungsfelder auf `wms_storage_location`;
- `wms_putaway_strategy`;
- `wms_putaway_order`;
- Migration `Version20260918110000`.

## Bekannte Grenzen

Volumen- und Gewichtsberechnung, feste Artikelplätze, Mischverbote,
Gefahrstoffklassen, Temperaturzonen, Nachschub und manuelle Zielplatzänderungen
sind noch nicht enthalten.
