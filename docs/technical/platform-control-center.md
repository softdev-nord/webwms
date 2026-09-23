# Platform Control Center

Der Slice `WEBWMS-066` bis `WEBWMS-074` bündelt die Zusatzfunktionen in einem mandantenfähigen Plattformmodul. Die Migration `Version20260923190000` ergänzt Shopfloor-Aufgaben, KPI-Definitionen, Dashboardlayouts, Partnerkonten, Automationsregeln und -ausführungen, Abrechnungstarife und -positionen, Suchdokumente, Medien sowie Druckroutingregeln.

## Architektur

`PlatformControlService` enthält Validierung, Zustandsautomaten, Referenz- und Mandantenprüfungen, Abrechnungsformeln, Eventauswertung, Suche und Druckerauswahl. Web- und API-Controller übernehmen ausschließlich Transport, Authentifizierung, CSRF beziehungsweise Payload-Normalisierung. Schreiboperationen werden mit Benutzer und Zeitpunkt über das vorhandene Administrationsjournal auditiert.

Die Rechte sind in `platform.read`, `platform.write` und `platform.execute` getrennt. Partner-, Benutzer-, Standort- und Druckerreferenzen müssen demselben Mandanten angehören.

## Oberflächen und API

- Web: `/v3/platform`
- JSON-Leseprojektion: `GET /api/v3/platform`
- Ressourcen: `POST /api/v3/platform/resources/{resource}`
- Shopfloor-Status: `POST /api/v3/platform/tasks/{id}/status`
- Eventauswertung: `POST /api/v3/platform/events`
- Suche: `GET /api/v3/platform/search?q=...`
- Druckerauswahl: `GET /api/v3/platform/print-routing/select`
- Lagergeld: `POST /api/v3/platform/billing/storage`
- Zusatzleistung: `POST /api/v3/platform/billing/services`
- Medienupload: `POST /api/v3/platform/media`
- Partnerportal: `GET /v3/partner-portal` und `GET /api/v3/partner-portal`

Medien sind auf JPG, PNG, WebP und PDF bis 5 MB begrenzt. Inhalt, Größe, SHA-256-Prüfsumme, Objektzuordnung und Auditdaten werden zusammen persistiert. Druckrouting wertet Dokumenttyp und optionale Standort-, Arbeitsplatz- und Prozesskriterien nach Priorität aus.
