# Packprozess über API v3 durchführen

## Packauftrag erzeugen

Nach Abschluss einer Pickliste erzeugt
`POST /api/v3/pick-lists/{id}/packing-orders` den Packauftrag:

```json
{
  "code": "PACK-1000"
}
```

Pro Pickliste ist nur ein Packauftrag zulässig.

## Packstück erfassen

Mit `POST /api/v3/packing-orders/{id}/packages` wird ein versiegeltes
Packstück gespeichert:

```json
{
  "packageNumber": "PKG-1000-1",
  "weightGrams": 4250,
  "pickTaskIds": ["PICK_TASK_UUID"]
}
```

Die Positionen müssen erfolgreich gepickt worden sein und zur Quellpickliste
gehören. Eine Position kann nicht erneut in ein anderes Packstück aufgenommen
werden. `GET /api/v3/packing-orders/{id}` zeigt den aktuellen Packfortschritt.

## Packauftrag abschließen

`POST /api/v3/packing-orders/{id}/complete` prüft, ob jede erfolgreich
gepickte Position genau einmal verpackt wurde. Bei Erfolg liefert die Antwort
den Status `completed`, die Packstückanzahl und das Gesamtgewicht in Gramm.

Bei fehlenden oder doppelt zugeordneten Positionen bleibt der Packauftrag
offen und WebWMS liefert eine Konfliktmeldung.
