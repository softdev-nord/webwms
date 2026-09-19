# Statusmeldungen über die Outbox übernehmen

Die Integrations-Outbox stellt Pick-, Pack-, Versand-, Tracking- und
Verladestatus zuverlässig für ERP-, Shop- oder Middleware-Systeme bereit. Im
regulären Betrieb veröffentlicht ein geplanter Lauf neue Meldungen automatisch
in die Integrationswarteschlange. Die API dient zusätzlich zur Kontrolle, zur
bisherigen Pull-Anbindung und zur Fehlerbehandlung.

## Nachrichten abrufen

```http
GET /api/v3/outbox?limit=50
X-API-Key: CLIENT_UUID.SECRET
```

Die Antwort enthält standardmäßig noch nicht veröffentlichte Nachrichten. Jede
Nachricht besitzt eine eindeutige `id`, einen Ereignisnamen, den betroffenen
Datensatz und eine fachliche `payload`. `meta.nextCursor` wird beim nächsten
Abruf als `cursor` übergeben.

Ein Pull-Zielsystem prüft vor einer Änderung, ob es die Nachrichten-ID bereits
verarbeitet hat, und quittiert danach wie bisher:

```http
POST /api/v3/outbox/MESSAGE_UUID/acknowledgement
X-API-Key: CLIENT_UUID.SECRET
```

Eine wiederholte Quittierung ist zulässig und verändert den ursprünglichen
Quittierungszeitpunkt nicht.

## Zustellung überwachen

Mit einem Statusfilter lassen sich die Betriebszustände prüfen:

```http
GET /api/v3/outbox?status=dead_letter&limit=50
X-API-Key: CLIENT_UUID.SECRET
```

Wichtige Felder sind `attempt_count`, `next_attempt_at`, `published_at` und
`last_error`. Ein Dead Letter wurde fünfmal nicht an die Queue übergeben und
benötigt eine fachliche oder technische Prüfung.

Nach Behebung der Ursache wird die Nachricht erneut freigegeben:

```http
POST /api/v3/outbox/MESSAGE_UUID/retry
X-API-Key: CLIENT_UUID.SECRET
```

Der Vorgang wird mit Benutzer und Zeitpunkt protokolliert. Ein Retry ist nur
für `dead_letter` möglich; die Nachrichten-ID bleibt unverändert.

## Betrieb

Der Systembetrieb plant `php bin/console webwms:outbox:publish --limit=100`
regelmäßig ein. Die Ausgabe zeigt beanspruchte, veröffentlichte, erneut
geplante und endgültig ausgesonderte Nachrichten. Ein Lauf mit einem neuen
Dead Letter endet mit einem Fehlercode und kann dadurch eine Alarmierung
auslösen.
