# Lagerlifte und Paternoster verwenden

Unter **Integration & Technik → Lagerlifte und Paternoster** verwalten berechtigte Benutzer automatische Lagergeräte und ihre Befehle.

## Gerät anlegen

1. **Gerät anlegen** wählen.
2. Eindeutigen Gerätecode, Anzeigenamen und Gerätetyp erfassen.
3. Einen HTTPS-Endpoint und den Namen der Umgebungsvariable eintragen, in der das Credential außerhalb der Datenbank bereitgestellt wird.
4. Das Gerät aktivieren und speichern.

Pausierte Geräte bleiben nachvollziehbar, können aber keine neuen Befehle erhalten.

## Gerätebefehl bearbeiten

1. **Befehl anlegen** wählen und ein aktives Gerät sowie einen Lagerplatz zuordnen.
2. Befehl und Prozessreferenz erfassen. Eine interne Request-ID verhindert eine doppelte Anlage bei wiederholter Übermittlung.
3. Den Befehl öffnen und zunächst als **An Gerät übermittelt** zurückmelden.
4. Anschließend den erfolgreichen Abschluss oder einen Fehler einschließlich optionaler Gerätemeldung erfassen.

Abgeschlossene und fehlgeschlagene Befehle sind terminal und können nicht erneut umgestellt werden. Die Übersicht zeigt den gesamten Verlauf mandantenbezogen an.
