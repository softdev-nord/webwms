# FIFO, LIFO und FEFO

Der Slice WEBWMS-023/024 ergänzt die manuelle Bestandsallokation um konfigurierbare, automatische Entnahmestrategien.

## Datenmodell

`wms_stock_selection_rule` enthält mandantenbezogene Regeln mit optionalem Lager- und Artikelbezug. Eine kleinere Prioritätszahl wird in der Oberfläche zuerst angeboten. Unterstützte Strategien sind `fifo`, `lifo` und `fefo`.

`wms_stock_selection_event` protokolliert jede erfolgreiche Ausführung unveränderlich mit Regel, Reservierung, Artikel, angeforderter und allokierter Menge, Kandidatenzahl, Benutzer und Zeitpunkt.

## Selektionslogik

- FIFO sortiert nach dem ältesten positiven Zugang im Bestandsledger.
- LIFO sortiert nach dem neuesten positiven Zugang.
- FEFO sortiert zuerst nach dem frühesten vorhandenen MHD; Bestände ohne MHD folgen anschließend.
- Abgelaufene, nicht verfügbare und nicht allokierbare Sonderbestände sind ausgeschlossen.
- Lager- und Artikelbezug einer Regel werden serverseitig erzwungen.

Die Allokation läuft in einer Datenbanktransaktion. Reicht der selektierte Bestand nicht für die gesamte Restmenge, werden alle Teilallokationen zurückgerollt.

## Schnittstellen und Berechtigungen

- `GET /api/v3/inventory/selection-rules`
- `POST /api/v3/inventory/selection-rules`
- `POST /api/v3/reservations/{reservationId}/automatic-allocation`

Die Berechtigungen `inventory.selection_rule.read`, `inventory.selection_rule.write` und `inventory.selection.execute` trennen Konfiguration und operative Ausführung.

## Fachliche Grenze

Die Zugangsreihenfolge wird aus dem unveränderlichen Bestandsledger abgeleitet. Innerhalb desselben Zugangszeitpunkts sorgen Lagerplatz und Bestandsschlüssel für eine deterministische Reihenfolge.
