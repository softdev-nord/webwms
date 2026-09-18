# JSON-Web-API v3 verwenden

Die API v3 verbindet ERP-, Shop- oder Middleware-Systeme mit WebWMS. Jeder Zugang gehört genau zu einem Mandanten, verwendet einen technischen Ausführungsbenutzer für das Audit und erhält nur die benötigten Berechtigungen.

## Zugang verwenden

Das bei der Einrichtung einmalig ausgegebene Credential wird als Header gesendet:

```http
X-API-Key: CLIENT_UUID.SECRET
Accept: application/json
```

Das Secret sollte in einem Secret Store abgelegt und nicht in Quellcode, Tickets oder Protokollen gespeichert werden.

## Artikel abrufen

```bash
curl -H 'X-API-Key: CLIENT_UUID.SECRET' \
  'https://webwms.example/api/v3/products?limit=50'
```

Wenn `meta.nextCursor` gesetzt ist, wird dieser Wert im nächsten Request als `cursor` übergeben.

## Artikel anlegen

```bash
curl -X POST -H 'Content-Type: application/json' \
  -H 'X-API-Key: CLIENT_UUID.SECRET' \
  -d '{"sku":"SKU-1000","name":"Beispielartikel"}' \
  'https://webwms.example/api/v3/products'
```

## Bestände abrufen

`GET /api/v3/stock` liefert physische und verfügbare Mengen einschließlich Lagerplatz, Status, Charge, Seriennummer und MHD. Optional beschränkt `warehouseId` die Antwort auf ein Lager.

Fehler werden als JSON mit HTTP-Status und einer kurzen Beschreibung zurückgegeben. Bei 401 ist das Credential ungültig; bei 403 fehlt dem Client die erforderliche Berechtigung.

Die weiterführenden Abläufe sind unter [Kundenaufträge](outbound-order-api.md)
und [Kommissionierung](picking-api.md) beschrieben.
