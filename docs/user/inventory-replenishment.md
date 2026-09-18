# Kommissionierplätze nachfüllen

Mit Nachschubregeln hält WebWMS den Bestand eines Kommissionierplatzes zwischen einem Mindest- und einem Zielbestand.

## Regel anlegen

Gib Artikel, Lager, Kommissionierplatz, Mindestbestand und Zielbestand an. Der Zielbestand muss größer als der Mindestbestand sein. Über den Quellplatz-Präfix legst du fest, aus welchem Reservebereich Ware entnommen werden darf, beispielsweise `RESERVE-`.

## Nachschubauftrag erzeugen

Starte die Prüfung der Regel. Unterschreitet der Kommissionierplatz den Mindestbestand, berücksichtigt WebWMS auch bereits offene Nachschubaufträge und erzeugt die noch benötigte Menge bis zum Zielbestand. Reicht eine Quelle nicht vollständig aus, wird zunächst nur ihre verfügbare Menge beauftragt.

WebWMS zieht reservierte Ware nicht als Quelle heran. Bei Ware mit Mindesthaltbarkeitsdatum wird zuerst die am frühesten ablaufende geeignete Charge vorgeschlagen.

## Auftrag ausführen und bestätigen

1. Entnimm die angegebene Menge vom vorgeschlagenen Quellplatz.
2. Transportiere sie zum angegebenen Kommissionierplatz.
3. Prüfe Artikel, Charge oder Seriennummer und Menge.
4. Bestätige den Auftrag.

Mit der Bestätigung bucht WebWMS Quelle und Ziel gemeinsam. Schlägt eine Buchung fehl, bleibt der gesamte Auftrag offen und es entsteht keine Teilbuchung.

Ein Auftrag kann nicht erzeugt werden, wenn der Mindestbestand noch nicht unterschritten ist oder kein frei verfügbarer Bestand auf einem zulässigen Quellplatz liegt.
