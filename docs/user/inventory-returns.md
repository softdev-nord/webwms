# Retouren annehmen und prüfen

## Retourenavis anlegen

Erfassen Sie die ursprüngliche Auftragsreferenz und für jede erwartete Position:

- Artikel;
- erwartete Menge;
- Rückgabegrund.

## Ware annehmen

Bestätigen Sie den physischen Eingang jeder Position. In diesem ersten
Prozessstand wird eine Position vollständig angenommen. Die Annahme erzeugt
noch keinen verfügbaren Bestand, sondern wartet auf die Qualitätsprüfung.

## Qualität entscheiden

Wählen Sie für jede angenommene Position:

- **Wiedereinlagern (`restock`)**: Die Ware wird als verfügbar gebucht.
- **Sperren (`quarantine`)**: Die Ware wird als gesperrter Bestand gebucht.

Geben Sie den Ziellagerplatz und eine aussagekräftige Prüfnotiz an. Falls
erforderlich, erfassen Sie Charge, Seriennummer und Mindesthaltbarkeitsdatum.
WebWMS protokolliert Prüfer und Zeitpunkt zusammen mit der Bestandsbewegung.

## Abschluss

Sobald alle Positionen angenommen und geprüft wurden, schließt WebWMS die
Retoure automatisch ab. Eine bereits geprüfte Annahme kann nicht erneut gebucht
werden.
