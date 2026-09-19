# Versandprozess über API v3 durchführen

## Sendung erzeugen

Nach Abschluss des Packauftrags erzeugt
`POST /api/v3/packing-orders/{id}/shipments` eine vorbereitete Sendung:

```json
{
  "shipmentNumber": "SHIP-1000",
  "carrier": "DHL",
  "service": "PARCEL"
}
```

Pro Packauftrag kann nur eine Sendung angelegt werden.

## Label und Tracking registrieren

Nach der Buchung im Carrier-System wird das Ergebnis über
`POST /api/v3/shipments/{id}/label` hinterlegt:

```json
{
  "trackingNumber": "00340434123456789012",
  "labelReference": "carrier://labels/SHIP-1000"
}
```

Die Sendung wechselt dadurch von `prepared` zu `labelled`.

## Übergabe bestätigen

Nach der physischen Übergabe an den Carrier wird
`POST /api/v3/shipments/{id}/dispatch` aufgerufen:

```json
{
  "handoverReference": "DHL-CLOSEOUT-20260919-01"
}
```

Die Sendung steht anschließend auf `dispatched`. Über
`GET /api/v3/shipments/{id}` können Status, Trackingnummer, Labelreferenz,
Übergabereferenz und Auditinformationen jederzeit abgerufen werden.

Ist eine Sendung bereits einer Ladeliste zugeordnet, erfolgt die Übergabe über
den Verladeprozess und nicht über diesen direkten Endpunkt.
