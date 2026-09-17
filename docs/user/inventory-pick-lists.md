# Mit Picklisten arbeiten

## Pickliste erstellen und zuweisen

Eine Pickliste bündelt bereits allokierte Bestände. Jede Position zeigt genau
den Bestand, der für einen Auftrag vorgesehen ist. Anschließend wird die Liste
einem Lagermitarbeiter zugewiesen.

## Position bestätigen

Der zugewiesene Mitarbeiter bearbeitet die Positionen in der gespeicherten
Reihenfolge und wählt:

- `picked`: Bestand wurde vollständig gefunden und entnommen;
- `shortage`: Bestand fehlt; eine Begründung wird gespeichert und die
  Allokation wieder freigegeben.

Wenn alle Positionen bearbeitet sind, erhält die Pickliste automatisch den
Status `completed`. Andernfalls bleibt sie `in_progress`.

## Nachvollziehbarkeit

WebWMS speichert je Bestätigung Benutzer, Zeitpunkt und Notiz. Erfolgreiche
Entnahmen sind zusätzlich über Reservierungs- und Allokations-ID mit dem
Bestandsjournal verbunden.
