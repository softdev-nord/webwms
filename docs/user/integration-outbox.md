# Statusmeldungen über die Outbox übernehmen

Die Integrations-Outbox stellt Pick-, Pack-, Versand-, Tracking- und
Verladestatus zuverlässig für ERP-, Shop- oder Middleware-Systeme bereit.

## Nachrichten abrufen

```http
GET /api/v3/outbox?limit=50
X-API-Key: CLIENT_UUID.SECRET
```

Die Antwort enthält ausschließlich noch nicht quittierte Nachrichten. Jede
Nachricht besitzt eine eindeutige `id`, einen `event_name`, den betroffenen
Datensatz und eine fachliche `payload`.

Wenn `meta.nextCursor` gesetzt ist, übergibt der Consumer diesen Wert beim
nächsten Abruf als `cursor`.

## Sicher verarbeiten

Das Zielsystem sollte vor einer Änderung prüfen, ob es die Nachrichten-ID
bereits verarbeitet hat. Ein erneuter Abruf derselben Nachricht ist möglich
und gewollt, solange keine Quittierung vorliegt.

Nach erfolgreicher Übernahme wird quittiert:

```http
POST /api/v3/outbox/MESSAGE_UUID/acknowledgement
X-API-Key: CLIENT_UUID.SECRET
```

Erst danach verschwindet die Nachricht aus der Liste offener Meldungen. Eine
wiederholte Quittierung ist zulässig und verändert den ursprünglichen
Quittierungszeitpunkt nicht.

## Fehlerbehandlung

Bei einem Fehler im Zielsystem wird nicht quittiert. Die Nachricht bleibt beim
nächsten Abruf verfügbar. Reihenfolge und fachliche Abhängigkeiten sollten
anhand von `occurred_at`, Aggregat und Nachrichten-ID verarbeitet werden.
