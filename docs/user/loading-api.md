# Ladelisten über API v3 bearbeiten

## Manifest erzeugen

`POST /api/v3/loading-manifests` fasst etikettierte Sendungen für eine Tour und
ein Fahrzeug zusammen:

```json
{
  "code": "LOAD-1000",
  "tourReference": "TOUR-BERLIN-01",
  "vehicleReference": "B-AB 1234",
  "shipmentIds": ["SHIPMENT_UUID"]
}
```

Nicht etikettierte oder bereits verplante Sendungen werden abgewiesen.

## Verladung bestätigen

Nach dem physischen Scan einer Sendung wird
`POST /api/v3/loading-manifests/{id}/shipments/{shipmentId}/loading`
aufgerufen. Die Antwort zeigt die Anzahl bereits geladener und insgesamt
eingeplanter Sendungen.

`GET /api/v3/loading-manifests/{id}` liefert jederzeit die vollständige
Ladeliste mit Trackingnummer, Carrier, Positionsstatus und Auditdaten.

## Manifest abschließen

Nach vollständiger Verladung ruft das führende System
`POST /api/v3/loading-manifests/{id}/complete` auf. WebWMS verhindert den
Abschluss, solange eine Position offen ist. Bei Erfolg werden Manifest und alle
enthaltenen Sendungen gemeinsam abgeschlossen beziehungsweise versendet.
