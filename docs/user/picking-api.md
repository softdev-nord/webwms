# Kommissionierung über API v3 durchführen

## Pickliste erzeugen

Nach vollständiger Allokation erzeugt
`POST /api/v3/orders/{id}/pick-lists` eine auftragsreine Pickliste:

```json
{
  "code": "PICK-1000"
}
```

WebWMS übernimmt automatisch alle aktiven Allokationen des Auftrags. Solange
noch eine Auftragsposition nicht vollständig allokiert ist, wird keine
Pickliste erzeugt.

## Mitarbeiter zuweisen

Mit `POST /api/v3/pick-lists/{id}/assignment` wird die Liste einem Benutzer
desselben Mandanten zugewiesen:

```json
{
  "assignedTo": "USER_UUID"
}
```

`GET /api/v3/pick-lists/{id}` zeigt die Reihenfolge sowie Artikel,
Lagerplatz, Menge, Status, Charge, Seriennummer und MHD jeder Position.

## Position bestätigen

Der zugewiesene Mitarbeiter meldet jede Position über
`POST /api/v3/pick-tasks/{id}/confirmation` zurück.

Erfolgreicher Pick:

```json
{
  "outcome": "picked",
  "note": "Vier Stück entnommen"
}
```

Fehlmenge:

```json
{
  "outcome": "shortage",
  "note": "Lagerplatz leer"
}
```

Nur der zugewiesene Benutzer darf bestätigen. Sobald alle Positionen erledigt
sind, setzt WebWMS die Pickliste automatisch auf `completed`.
