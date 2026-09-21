# Sperrgründe und Bestandssperren

WEBWMS-025 ergänzt den Bestandsstatus `blocked` um einen kontrollierten und vollständig auditierten Workflow.

## Datenmodell

- `wms_stock_block_reason` enthält mandantenspezifische, aktivierbare Sperrgründe.
- `wms_stock_block` hält die betroffene Bestandsdimension, Menge, den ursprünglichen Status sowie Prüf- und Freigabedaten.
- `wms_stock_block_event` ist das unveränderliche Journal für `blocked`, `reviewed` und `released`.

## Zustandsfolge

Eine Sperre bucht die angegebene Menge am selben Lagerplatz und mit unveränderter Charge, Seriennummer und MHD auf den Status `blocked` um. Der fachliche Zustand ist danach `open`.

Nur offene Sperren können geprüft werden. Die Prüfung wechselt auf `reviewed`. Erst eine geprüfte Sperre darf freigegeben werden; dabei wird die Menge auf den ursprünglichen Bestandsstatus zurückgebucht und der Zustand auf `released` gesetzt.

Alle Schreiboperationen laufen transaktional. Aktive Allokationen verhindern die Sperrumbuchung. Die Allokationspersistenz akzeptiert zusätzlich ausschließlich den Bestandsstatus `available`.

## API und Autorisierung

- `GET|POST /api/v3/inventory/stock-block-reasons`
- `GET|POST /api/v3/inventory/stock-blocks`
- `GET /api/v3/inventory/stock-blocks/{id}/events`
- `POST /api/v3/inventory/stock-blocks/{id}/review`
- `POST /api/v3/inventory/stock-blocks/{id}/release`

Die Rechte `inventory.stock_block.read`, `inventory.stock_block.write`, `inventory.stock_block.review` und `inventory.stock_block.release` trennen Einsicht, Sperrung, Prüfung und Freigabe.
