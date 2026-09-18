# Versionierte JSON-Web-API v3

`WEBWMS-084` führt eine von den Legacy-API-Platform-Ressourcen getrennte Schnittstelle unter `/api/v3` ein. Die Endpunkte verwenden ausschließlich den WebWMS-3.0-Anwendungs- und Persistenzkern.

## Authentifizierung

API-Clients sind einem Mandanten fest zugeordnet und speichern ausschließlich einen SHA-256-Hash des Secrets. Ein Credential wird einmalig erzeugt:

```bash
php bin/console webwms:api-client:create \
  TENANT_UUID erp-connector \
  inventory.product.read,inventory.product.write,inventory.location.read,inventory.stock.read
```

Die Ausgabe besitzt das Format `CLIENT_UUID.SECRET` und wird im Header `X-API-Key` übertragen. Der Firewall `api_v3` ist stateless. Jede erfolgreiche Verwendung aktualisiert `last_used_at`.

## Ressourcen

| Methode | Pfad | Berechtigung | Funktion |
| --- | --- | --- | --- |
| `GET` | `/api/v3/meta` | authentifiziert | API-Version |
| `GET` | `/api/v3/products` | `inventory.product.read` | Artikel lesen |
| `POST` | `/api/v3/products` | `inventory.product.write` | Artikel anlegen |
| `GET` | `/api/v3/warehouses` | `inventory.location.read` | Lager lesen |
| `GET` | `/api/v3/stock` | `inventory.stock.read` | Bestand und verfügbare Menge lesen |

Listen akzeptieren `limit` von 1 bis 100. Artikel und Bestände unterstützen einen opaken `cursor`; der Folgewert steht in `meta.nextCursor`. Bestände können mit `warehouseId` eingeschränkt werden.

## Mandantentrennung

Der Client darf keine Mandanten-ID im Request vorgeben. `InventoryApiController` übernimmt den Mandanten immer aus `ApiClientUser`. `ApiV3QueryService` bindet diese ID in jede Abfrage ein; schreibende Operationen erhalten sie über den Application Command.

## Antwort- und Fehlerformat

Erfolgreiche Antworten enthalten `data`, Listen zusätzlich `meta`. Fehler verwenden `application/problem+json` mit `type`, `title`, `status` und `detail`. Validierungsfehler liefern 422, Duplikate 409, fehlende Rechte 403 und ungültige Credentials 401.

## Bekannte Restarbeiten

- Kundenaufträge, Reservierungen, Picks, Packstücke, Sendungen und Statusereignisse als Ressourcen;
- OpenAPI-Vertrag und API-Integrationstests mit MariaDB;
- Rotation und Widerruf von Secrets über eine Administrationsoberfläche;
- Rate-Limiting und technische Verbrauchsmetriken.
