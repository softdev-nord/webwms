# Nachschubsteuerung

Dieser Slice ergänzt den Inventory Core um mandantenfähige Mindestbestandsregeln, eine automatische Quellplatzermittlung und atomar bestätigte Nachschubaufträge.

## Anwendungsfälle

- `CreateReplenishmentPolicyHandler` hinterlegt je Artikel und Kommissionierplatz Mindest- und Zielbestand sowie den Präfix zulässiger Quellplätze.
- `CreateReplenishmentOrderHandler` prüft die Auslöseschwelle, ermittelt eine Bestandsquelle und reserviert deren Menge logisch durch einen offenen Auftrag.
- `ConfirmReplenishmentHandler` führt die physische Umlagerung mit dem vorhandenen atomaren `StockTransfer` aus.

## Bestands- und Auswahlregeln

Ein Auftrag entsteht nur, wenn verfügbarer Zielbestand plus bereits offene Zugänge kleiner als der Mindestbestand ist. Die Sollmenge entspricht Zielbestand minus effektivem Bestand und wird auf den verfügbaren Quellbestand begrenzt.

Als Quellbestand gelten ausschließlich Bestände mit Status `available` im selben Lager, deren Lagerplatzcode mit dem konfigurierten Präfix beginnt. Aktive Allokationen und Mengen bereits offener Nachschubaufträge werden abgezogen. Die Auswahl erfolgt nach FEFO (frühestes MHD zuerst), anschließend nach Lagerplatzpriorität und Lagerplatzcode. Charge, Seriennummer, MHD und Bestandsstatus bleiben bei der Umlagerung unverändert.

## Persistenz und Transaktionen

Migration `Version20260918113000` führt `wms_replenishment_policy` und `wms_replenishment_order` ein. Eindeutige Schlüssel verhindern mehrere Regeln für denselben Artikel und Zielplatz sowie mehrfach verwendete Transfer-IDs. Indizes unterstützen die Prüfung offener Aufträge und der bereits gebundenen Quellmengen.

Regel und Auftrag werden beim Ermitteln beziehungsweise Bestätigen mit `FOR UPDATE` gesperrt. Die Bestandsbuchungen und der Statuswechsel auf `completed` laufen gemeinsam in einer Transaktion.

## Bekannte Einschränkungen

- Ein Auftrag verwendet genau einen Quellbestandsdatensatz; reicht dieser nicht bis zum Zielbestand, kann nach Bestätigung ein weiterer Auftrag erzeugt werden.
- Die automatische periodische Ausführung und eine Bedienoberfläche sind noch nicht Bestandteil dieses Slices.
- Berechtigungsprüfungen erfolgen weiterhin am späteren Transportadapter; der Anwendungskern validiert Mandant und Benutzerreferenz.
