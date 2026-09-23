# Outbound Control Center

Der Slice `WEBWMS-049` bis `WEBWMS-065` ergänzt den vorhandenen Fulfillment-Kern um die bislang fehlenden Outbound-Aggregate.

## Persistenz

Migration `Version20260923160000` ergänzt:

- Stornoaudit am `wms_outbound_order`;
- `wms_outbound_quality_check`;
- `wms_shipping_rule`;
- `wms_tracking_event`;
- `wms_shipping_document`;
- `wms_transport_tour` und `wms_tour_stop`;
- `wms_weight_constraint`.

Alle Tabellen sind mandantengebunden oder ausschließlich über ein mandantengebundenes Aggregat erreichbar. Eindeutige Indizes schützen Bestellnummern, Regelcodes, Dokumentnummern, Tourcodes, Stoppreihenfolgen und Gewichtsregeln. QS-Prüfungen bleiben als Historie erhalten; für die Prozessfreigabe zählt die jüngste Entscheidung.

## Prozessregeln

`OutboundProcessService` kapselt Storno, QS, Versandregelauswahl, Tracking, Dokumenterzeugung, Touren und Gewichtsprüfung. Der Packprozess prüft die QS-Freigabe vor der Anlage und das Paketlimit vor der Persistenz. Bereits vorhandene Domain-Handler bleiben für Reservierung, Picking, Packabschluss, Shipment, Carrier-Label, Dispatch und Verladescan verantwortlich.

Externe Statuswechsel werden weiterhin transaktional in `wms_integration_outbox` publiziert. Dokumente enthalten eine SHA-256-Prüfsumme; Carrier- und Druckerzugriffe verwenden die vorhandenen idempotenten Gateways.

## Oberflächen

- Web: `/v3/outbound/control`
- JSON: `/api/v3/outbound/control`
- Operative Arbeitsplätze: `/v3/outbound/orders`, `/v3/picking`, `/v3/packing`, `/v3/shipping`, `/v3/loading`

Die Tabellen des Leitstands verwenden die gemeinsame V3-Suche, Filterung und Paginierung.
