# Wareneingang, Bestellungen, Avis und QS

## Prozessmodell

Der reguläre Wareneingang besteht aus vier getrennten Schritten:

1. Bestellung mit Lieferantenreferenz und Sollmengen anlegen.
2. Lieferavis mit Liefernotiz, Erwartungszeit und Teilmengen erfassen.
3. Avisierte Positionen physisch annehmen.
4. QS-Checkliste beantworten und Bestand freigeben oder sperren.

Mehrere Avise können eine Bestellung bedienen. Die kumulierte Avismenge darf
die Bestellmenge nicht überschreiten. Der erste Slice nimmt jede Avisposition
vollständig und genau einmal an.

## Qualität und Bestandsbuchung

Eine Annahme hat zunächst den Status `pending_quality` und keine
Bestandswirkung. Die QS-Entscheidung benötigt mindestens einen Prüfpunkt:

- `accept` ist nur zulässig, wenn alle Prüfpunkte bestanden sind, und bucht
  `available`;
- `block` bucht `blocked` und erlaubt fehlgeschlagene Prüfpunkte.

Die Buchung verwendet den Journaltyp `inbound_receipt` und übernimmt Charge,
Seriennummer und MHD in die vorhandenen Bestandsdimensionen.

## Transaktionen und Persistenz

Alle Übergänge sind mandantenbezogen und transaktional. Bestell-, Avis- und
Annahmezeilen werden vor Mengen- oder Zustandsänderungen mit `FOR UPDATE`
gesperrt.

- `wms_purchase_order` und `wms_purchase_order_item`;
- `wms_inbound_delivery` und `wms_inbound_delivery_line`;
- `wms_inbound_receipt`;
- `wms_inbound_quality_answer`;
- Migration `Version20260918103000`.

## Bekannte Grenzen

Lieferantenstammdaten, Teilannahmen, Überlieferungstoleranzen, ungeplante
Eingänge, Fotos/Anhänge, Etikettendruck, Cross-Docking und automatische
Einlagerungsaufträge folgen in späteren Slices.
