# Versionierte JSON-Web-API v3

`WEBWMS-084` führt eine von den Legacy-API-Platform-Ressourcen getrennte Schnittstelle unter `/api/v3` ein. Die Endpunkte verwenden ausschließlich den WebWMS-3.0-Anwendungs- und Persistenzkern.

## Authentifizierung

API-Clients sind einem Mandanten fest zugeordnet und speichern ausschließlich einen SHA-256-Hash des Secrets. Ein Credential wird einmalig erzeugt:

```bash
php bin/console webwms:api-client:create \
  TENANT_UUID ACTING_USER_UUID erp-connector \
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
| `GET` | `/api/v3/stock-movements` | `inventory.stock.movement.read` | Bewegungsjournal filtern und lesen |
| `POST` | `/api/v3/stock-transfers` | `inventory.stock.transfer` | Bestand atomar umlagern oder Status ändern |
| `POST` | `/api/v3/orders` | `outbound.order.write` | Kundenauftrag importieren |
| `GET` | `/api/v3/orders/{id}` | `outbound.order.read` | Kundenauftrag lesen |
| `POST` | `/api/v3/orders/{id}/release` | `outbound.order.release` | Auftrag freigeben und reservieren |
| `GET` | `/api/v3/reservations/{id}` | `inventory.allocation.read` | Reservierung und Allokationen lesen |
| `POST` | `/api/v3/reservations/{id}/allocations` | `inventory.allocation.write` | Bestand allokieren |
| `POST` | `/api/v3/orders/{id}/pick-lists` | `fulfillment.pick.write` | Auftragsreine Pickliste erzeugen |
| `GET` | `/api/v3/pick-lists/{id}` | `fulfillment.pick.read` | Pickliste und Positionen lesen |
| `POST` | `/api/v3/pick-lists/{id}/assignment` | `fulfillment.pick.assign` | Pickliste zuweisen |
| `POST` | `/api/v3/pick-tasks/{id}/confirmation` | `fulfillment.pick.execute` | Pick oder Fehlmenge bestätigen |
| `POST` | `/api/v3/pick-lists/{id}/packing-orders` | `fulfillment.pack.write` | Packauftrag erzeugen |
| `GET` | `/api/v3/packing-orders/{id}` | `fulfillment.pack.read` | Packauftrag und Packstücke lesen |
| `POST` | `/api/v3/packing-orders/{id}/packages` | `fulfillment.pack.write` | Versiegeltes Packstück erfassen |
| `POST` | `/api/v3/packing-orders/{id}/complete` | `fulfillment.pack.execute` | Vollständigkeit prüfen und abschließen |
| `POST` | `/api/v3/packing-orders/{id}/shipments` | `fulfillment.ship.write` | Sendung erzeugen |
| `GET` | `/api/v3/shipments/{id}` | `fulfillment.ship.read` | Sendung und Trackingstatus lesen |
| `POST` | `/api/v3/shipments/{id}/label` | `fulfillment.ship.label` | Labelreferenz und Tracking registrieren |
| `POST` | `/api/v3/shipments/{id}/dispatch` | `fulfillment.ship.dispatch` | Carrier-Übergabe bestätigen |
| `POST` | `/api/v3/loading-manifests` | `fulfillment.loading.write` | Lademanifest erzeugen |
| `GET` | `/api/v3/loading-manifests/{id}` | `fulfillment.loading.read` | Ladeliste und Fortschritt lesen |
| `POST` | `/api/v3/loading-manifests/{id}/shipments/{shipmentId}/loading` | `fulfillment.loading.execute` | Verladung bestätigen |
| `POST` | `/api/v3/loading-manifests/{id}/complete` | `fulfillment.loading.execute` | Manifest und Übergabe abschließen |
| `GET` | `/api/v3/outbox` | `integration.outbox.read` | Statusereignisse nach Zustellstatus lesen |
| `POST` | `/api/v3/outbox/{id}/acknowledgement` | `integration.outbox.acknowledge` | Verarbeitung idempotent quittieren |
| `POST` | `/api/v3/outbox/{id}/retry` | `integration.outbox.retry` | Dead Letter geprüft wiederaufnehmen |

Listen akzeptieren `limit` von 1 bis 100. Artikel, Bestände, Bestandsbewegungen und Outbox-Nachrichten unterstützen einen opaken `cursor`; der Folgewert steht in `meta.nextCursor`. Bestände können mit `warehouseId`, Bewegungen mit Artikel, Lagerplatz, Transfer-ID und Bewegungsart eingeschränkt werden.

## Mandantentrennung

Der Client darf keine Mandanten-ID im Request vorgeben. `InventoryApiController` übernimmt den Mandanten immer aus `ApiClientUser`. `ApiV3QueryService` bindet diese ID in jede Abfrage ein; schreibende Operationen erhalten sie über den Application Command.

## Antwort- und Fehlerformat

Erfolgreiche Antworten enthalten `data`, Listen zusätzlich `meta`. Fehler verwenden `application/problem+json` mit `type`, `title`, `status` und `detail`. Validierungsfehler liefern 422, Duplikate 409, fehlende Rechte 403 und ungültige Credentials 401.

## Bekannte Restarbeiten

- konkrete ERP-, Shop- und Webhook-Handler für die Integrationsqueue;
- OpenAPI-Vertrag und API-Integrationstests mit MariaDB;
- Rotation und Widerruf von Secrets über eine Administrationsoberfläche;
- Rate-Limiting und technische Verbrauchsmetriken.
