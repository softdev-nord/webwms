# FIFO, LIFO und FEFO verwenden

## Entnahmeregel anlegen

Unter **Lager & Bestand → Entnahmestrategien** können berechtigte Benutzer eine Regel anlegen:

1. Code und Namen vergeben.
2. FIFO, LIFO oder FEFO auswählen.
3. Optional ein Lager oder einen Artikel einschränken.
4. Priorität festlegen und die Regel aktivieren.

FIFO entnimmt den ältesten Zugang zuerst, LIFO den neuesten. FEFO bevorzugt das früheste noch gültige Mindesthaltbarkeitsdatum.

## Ausgangsauftrag automatisch allokieren

Nach der Auftragsfreigabe zeigt jede noch offene Reservierung die anwendbaren Regeln an. Mit **Restmenge automatisch allokieren** verteilt WebWMS die komplette Restmenge entsprechend der gewählten Strategie auf geeignete Bestandspositionen.

Kann die Restmenge nicht vollständig gedeckt werden, bleibt die Reservierung unverändert. Die manuelle Auswahl einer Bestandsdimension steht weiterhin darunter zur Verfügung.

## Nachvollziehbarkeit

Das Ausführungsjournal unter **Entnahmestrategien** zeigt Strategie, Artikel, Reservierung, Mengen, Kandidatenzahl, Benutzer und Zeitpunkt jeder erfolgreichen automatischen Allokation.
