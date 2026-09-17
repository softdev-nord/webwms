# Bestand umlagern und Status ändern

## Einsatzmöglichkeiten

Mit einem Bestandstransfer können Sie Bestand sicher:

- auf einen anderen Lagerplatz umlagern;
- von der Qualitätsprüfung freigeben;
- sperren oder entsperren;
- gleichzeitig umlagern und den Status ändern.

Die Aus- und Einbuchung bilden einen einzigen Vorgang. Schlägt eine Seite fehl,
wird keine Bestandsänderung gespeichert.

## Benötigte Angaben

- Artikel und Menge;
- Quell- und Ziellagerplatz;
- bisheriger und neuer Bestandsstatus;
- Charge, Seriennummer und MHD des betroffenen Bestands;
- nachvollziehbarer Umbuchungsgrund.

Charge, Seriennummer und MHD können während eines Transfers nicht geändert
werden. Für eine Korrektur dieser Angaben ist ein eigener Korrekturprozess
erforderlich.

## Beispiele

### Qualitätsbestand freigeben

Quelle und Ziel verwenden denselben Lagerplatz. Der Quellstatus lautet
`quality_inspection`, der Zielstatus `available`.

### Bestand sperren und umlagern

Der Bestand wechselt auf einen Sperrlagerplatz und erhält gleichzeitig den
Status `blocked`.

### Seriennummer umlagern

Bei einer Seriennummer beträgt die Menge immer genau ein Stück. Ist dieselbe
Seriennummer im Zielbestand bereits vorhanden, lehnt WebWMS den Transfer ab.

## Nachvollziehbarkeit

Im Bestandsjournal erscheinen zwei Einträge mit derselben Transfer-ID:

- `transfer_out` für die Quelle;
- `transfer_in` für das Ziel.

Beide Einträge enthalten Benutzer, Zeitpunkt, Grund und resultierenden Bestand.
