# Bestellungen und Wareneingänge bearbeiten

## Bestellung anlegen

Erfassen Sie Bestellnummer, Lieferantenreferenz sowie Artikel und Sollmengen.
Die Bestellung bleibt offen, bis ihre Mengen vollständig avisiert wurden.

## Lieferavis erfassen

Ordnen Sie das Avis einer Bestellung zu und geben Sie Liefernotiz,
Erwartungszeitpunkt und avisierte Mengen an. WebWMS verhindert, dass die Summe
mehrerer Avise die jeweilige Bestellmenge überschreitet.

## Ware annehmen

Bestätigen Sie jede physisch eingetroffene Avisposition. Die Ware ist danach
noch nicht verfügbar, sondern wartet im Wareneingang auf die Qualitätsprüfung.

## QS-Checkliste durchführen

Beantworten Sie jeden Prüfpunkt, beispielsweise Verpackungszustand,
Artikelidentität oder sichtbare Schäden, und ergänzen Sie bei Bedarf eine
Notiz. Anschließend wählen Sie:

- **Annehmen (`accept`)** für verfügbaren Bestand. Alle Prüfpunkte müssen
  bestanden sein.
- **Sperren (`block`)** für Ware, die geklärt oder nachgearbeitet werden muss.

Wählen Sie außerdem den Ziellagerplatz und erfassen Sie gegebenenfalls Charge,
Seriennummer und Mindesthaltbarkeitsdatum. WebWMS protokolliert Annahme, Prüfer,
Zeitpunkte, Antworten und die erzeugte Bestandsbewegung.
