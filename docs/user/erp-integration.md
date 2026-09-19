# ERP-System anbinden

Ein ERP-System kann Artikel und Aufträge an WebWMS übertragen, Bestände und
Bewegungen lesen und Statusänderungen automatisch empfangen.

## Signaturschlüssel bereitstellen

Der Systembetrieb legt zuerst einen starken, zufälligen Schlüssel als
Umgebungsvariable ab, beispielsweise `ERP_ACME_SIGNING_KEY`. Der Schlüssel wird
nicht über die WebWMS-API übertragen oder in der Datenbank gespeichert.

## Verbindung registrieren

Im V3-Arbeitsbereich können berechtigte Benutzer unter
`/v3/integration/erp-connections` Verbindungen anzeigen, anlegen, pausieren und
erneut aktivieren. Die Detailansicht zeigt Endpunkt, Credential-Referenz und
Auditinformationen. Der eigentliche Signaturschlüssel wird auch bei der Anlage
über das Frontend weder übertragen noch gespeichert.

Alternativ steht dieselbe Verwaltung über die API zur Verfügung:

```http
POST /api/v3/erp-connections
X-API-Key: CLIENT_UUID.SECRET
Content-Type: application/json

{
  "name": "Acme ERP Produktion",
  "endpointUrl": "https://erp.example.com/integrations/webwms",
  "credentialEnv": "ERP_ACME_SIGNING_KEY",
  "active": true
}
```

WebWMS sendet Statusmeldungen anschließend an
`https://erp.example.com/integrations/webwms/status-events`.

## Verbindung verwalten

Alle Verbindungen des eigenen Mandanten werden mit
`GET /api/v3/erp-connections` angezeigt. Eine Verbindung lässt sich ohne
Löschen pausieren:

```http
PATCH /api/v3/erp-connections/CONNECTION_UUID/status
X-API-Key: CLIENT_UUID.SECRET
Content-Type: application/json

{"active": false}
```

Die Änderung wird mit Benutzer und Zeitpunkt protokolliert. Bei erneuter
Aktivierung werden nur neu aus der Queue zugestellte Meldungen versendet.

Für den V3-Arbeitsbereich werden die Berechtigungen
`integration.erp_connection.read` und `integration.erp_connection.write`
benötigt.

## Statusmeldung prüfen

Das ERP prüft vor der Verarbeitung:

1. HMAC-SHA-256 des unveränderten JSON-Bodys mit dem gemeinsamen Schlüssel;
2. Vergleich mit `X-WebWMS-Signature`;
3. ob `Idempotency-Key` bereits erfolgreich verarbeitet wurde.

Die Nachrichten-ID muss dauerhaft dedupliziert werden, da eine Zustellung bei
Timeouts oder technischen Fehlern wiederholt werden kann. Das ERP bestätigt
Erfolg mit einem HTTP-Status zwischen 200 und 299.

## Fachliche Schnittstellen

- Artikel anlegen: `POST /api/v3/products`
- Kundenaufträge übernehmen: `POST /api/v3/orders`
- aktuelle Bestände lesen: `GET /api/v3/stock`
- Bestandsbewegungen lesen: `GET /api/v3/stock-movements`

Die dafür benötigten Berechtigungen werden dem API-Client zusätzlich zu den
ERP-Verbindungsrechten zugewiesen.
