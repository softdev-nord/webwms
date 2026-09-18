# Retouren- und Rücknahmeabwicklung

## Modell und Ablauf

Ein Retourenavis referenziert den ursprünglichen Auftrag und enthält mindestens
eine erwartete Artikelposition mit Menge und Rückgabegrund.

1. Avis im Status `open` anlegen.
2. Jede erwartete Position vollständig annehmen (`received`).
3. Annahme durch die Qualitätssicherung als `restock` oder `quarantine`
   entscheiden (`processed`).
4. Nach Bearbeitung aller Positionen wird die Retoure `completed`.

`restock` bucht den Bestand als `available`, `quarantine` als `blocked`. Die
Bestandsbuchung entsteht erst bei der Qualitätsentscheidung und verwendet im
Bestandsjournal den Bewegungstyp `return_receipt`.

## Konsistenz und Mandantentrennung

Anlage, Annahme und Prüfung laufen transaktional. Retourenpositionen und
Annahmen werden vor einem Übergang mit `FOR UPDATE` gesperrt. Artikel,
Lagerplatz und ausführende Benutzer müssen zum Mandanten der Retoure gehören.
Eine Position kann durch den eindeutigen Datenbankindex nur einmal angenommen
und eine Annahme nur einmal geprüft werden.

Chargen-, Seriennummern- und MHD-Merkmale werden in die bestehende
Bestandsdimension übernommen. Eine Seriennummer kann weiterhin höchstens die
Menge eins besitzen.

## Persistenz

- `wms_return_order`: Avis, Auftragsreferenz und Abschlussaudit;
- `wms_return_item`: erwarteter Artikel, Menge, Grund und Status;
- `wms_return_receipt`: Annahme, Qualitätsentscheidung und Bestandsreferenz;
- Migration `Version20260918100000`.

## Bekannte Grenzen

Der erste Slice nimmt jede avisierte Position vollständig und genau einmal an.
Teilmengen, Übermengen, ungeplante Retouren, Fotos, Gutschriften, Reparatur,
Entsorgung und Rücksendung an Lieferanten folgen in späteren Slices.
