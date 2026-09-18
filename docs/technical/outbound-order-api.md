# Kundenauftrag, Reservierung und Allokation

Der Slice vertikalisiert `WEBWMS-049` und `WEBWMS-051` vom JSON-Request bis zur transaktionalen Persistenz.

## Ablauf

1. `POST /api/v3/orders` validiert und importiert einen Kundenauftrag als `imported`.
2. `POST /api/v3/orders/{id}/release` sperrt den Auftrag und erzeugt je Position genau eine Reservierung.
3. `POST /api/v3/reservations/{id}/allocations` ordnet konkreten Bestand dimensionsgenau zu.
4. `GET`-Endpunkte liefern Auftrag, Reservierungsfortschritt und Allokationen zurück.

Die Auftragsfreigabe ist atomar. Fehlende Positionen, unbekannte Produkte, eine erneute Freigabe oder unvollständige Reservierungs-IDs führen zum Rollback.

## Modell

- `OutboundOrder` enthält Auftragsnummer, Kundenreferenz und Positionen.
- Ein Artikel darf pro Auftrag nur einmal vorkommen.
- `OutboundOrderRelease` ordnet jeder Position eine eindeutige Reservierungs-ID zu.
- `StockReservation` führt Soll-, allokierte und erfüllte Menge.
- `StockAllocation` bindet Lagerplatz, Status, Charge, Seriennummer und MHD.

Migration `Version20260918191000` erzeugt `wms_outbound_order` und `wms_outbound_order_item`. Migration `Version20260918190000` bindet API-Clients an einen aktiven technischen Benutzer. Bestehende API-Clients müssen nach der Migration neu ausgestellt werden.

## Sicherheit

Mandant und Auditbenutzer stammen ausschließlich aus dem authentifizierten API-Client. Getrennte Permissions schützen Lesen, Import, Freigabe und Allokation. Artikel- und Lagerreferenzen werden innerhalb des Mandanten geprüft.

## Restarbeiten

- Auftragsänderung und Stornierung;
- automatische Allokationsstrategie;
- UI und Datenbank-Integrationstests;
- automatische Allokationsstrategie vor der Picklistenerzeugung.
