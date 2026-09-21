# Bestand sperren und freigeben

## Sperrgrund verwalten

Unter **Lager & Bestand → Bestandssperren** legen berechtigte Benutzer Sperrgründe mit Code, Bezeichnung und Beschreibung an. Nur aktive Gründe können für neue Sperren verwendet werden.

## Bestand sperren

In der Bestandsliste der Seite wird eine konkrete Position gewählt. Sperrgrund, Menge und Begründung sind verpflichtend. Charge, Seriennummer und MHD bleiben bei der Sperrung erhalten.

Bereits allokierter Bestand kann nicht gesperrt werden. Seriennummernbestand wird weiterhin einzeln verarbeitet.

## Prüfen und freigeben

Die Sperrliste führt durch die festgelegte Reihenfolge:

1. Eine offene Sperre erhält ein dokumentiertes Prüfergebnis.
2. Eine geprüfte Sperre kann mit einem Freigabevermerk freigegeben werden.
3. WebWMS bucht die Menge wieder auf ihren ursprünglichen Bestandsstatus.

Ein Zustandswechsel kann nicht übersprungen oder wiederholt werden. Über **Journal öffnen** sind Sperrung, Prüfung und Freigabe mit Benutzer und Zeitpunkt nachvollziehbar.
