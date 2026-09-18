# Kundenaufträge über API v3 bearbeiten

## Auftrag importieren

`POST /api/v3/orders` erwartet eine Auftragsnummer, Kundenreferenz und mindestens eine Position:

```json
{
  "orderNumber": "ORDER-1000",
  "customerReference": "CUSTOMER-4711",
  "items": [
    {"productId": "PRODUCT_UUID", "quantity": 4}
  ]
}
```

Der Auftrag wird zunächst im Status `imported` gespeichert und kann über `GET /api/v3/orders/{id}` geprüft werden.

## Auftrag freigeben

`POST /api/v3/orders/{id}/release` gibt den Auftrag frei. Für jede Position erzeugt WebWMS automatisch eine Reservierung. Die Antwort enthält die Reservierungs-ID je Auftragsposition.

Eine erneute Freigabe ist nicht zulässig. Schlägt eine Position fehl, wird der gesamte Vorgang zurückgerollt.

## Bestand allokieren

Eine Reservierung wird mit `POST /api/v3/reservations/{id}/allocations` einem konkreten Bestand zugeordnet:

```json
{
  "locationId": "LOCATION_UUID",
  "quantity": 4,
  "status": "available",
  "batchNumber": "LOT-2026-01",
  "expiresAt": "2027-05-31"
}
```

WebWMS verhindert Überallokation und berücksichtigt bereits aktive Allokationen. `GET /api/v3/reservations/{id}` zeigt Sollmenge, allokierte Menge, Restmenge und vorhandene Allokationen.
