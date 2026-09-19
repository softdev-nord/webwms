# Carrier-Integration

`WEBWMS-089` stellt einen herstellerneutralen HTTPS-Adapter zwischen WebWMS und
Carrier-Systemen bereit. Eine `CarrierConnection` gehört genau einem Mandanten
und einem Carrier-Code. Zugangsdaten werden nicht persistiert: `credentialEnv`
verweist ausschließlich auf eine Laufzeit-Umgebungsvariable.

## Vertrag

Der konfigurierte Endpunkt stellt folgende Ressourcen bereit:

| Methode | Pfad | Ergebnis |
| --- | --- | --- |
| `GET` | `/products` | `{"products":[{"code":"...","name":"..."}]}` |
| `POST` | `/labels` | `{"trackingNumber":"...","labelReference":"..."}` |
| `POST` | `/manifests` | `{"handoverReference":"..."}` |
| `GET` | `/tracking/{trackingNumber}` | `{"status":"...","occurredAt":"..."}` |

WebWMS sendet das Secret als Bearer Token. Schreibende Aufrufe tragen zusätzlich
`Idempotency-Key`. Der API-Client liefert diesen Schlüssel als `requestId`.

## Konsistenz und Audit

Erfolgreiche Schreib- und Trackingaufrufe werden in `wms_carrier_request`
mandantengebunden mit Operation, Aggregat, Antwort, Benutzer und Zeitpunkt
gespeichert. `(tenant_id, idempotency_key)` ist eindeutig. Eine Wiederholung
derselben Operation für dasselbe Aggregat liefert die gespeicherte Antwort und
verursacht keinen zweiten Carrier-Aufruf.

Die Labeloperation ist nur für vorbereitete Sendungen erlaubt. Nach erfolgreicher
Carrier-Antwort registriert WebWMS Trackingnummer und Labelreferenz über den
bestehenden Versand-Zustandsautomaten. Eine Manifestübergabe setzt ein bereits
abgeschlossenes Lademanifest voraus.

## Betrieb

- Nur HTTPS-Endpunkte ohne URL-Credentials, Query oder Fragment sind zulässig.
- Das referenzierte Secret muss im PHP-Prozess verfügbar sein.
- Nicht-2xx-Antworten und unvollständige JSON-Antworten brechen die Operation ab.
- Binäre Labeldaten werden nicht in der Datenbank gespeichert; `labelReference`
  verweist auf die Carrier- oder Dokumentenablage.
