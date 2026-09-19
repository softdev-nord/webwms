# Drucker-Integration

`WEBWMS-091` stellt eine mandantenfähige Druckwarteschlange zwischen WebWMS und
HTTPS-fähigen Print-Gateways bereit. Ein `Printer` speichert ausschließlich die
Referenz auf eine Umgebungsvariable; Zugangsdaten gelangen weder in Datenbank
noch API-Antworten.

## Ablauf

1. Drucker über `POST /api/v3/printers` registrieren.
2. Druckauftrag mit Dokumenttyp, Dokumentreferenz, Format, Kopien und
   `requestId` über `POST /api/v3/print-jobs` idempotent einreihen.
3. Auftrag über `POST /api/v3/print-jobs/{id}/execute` ausführen.
4. Status, Versuche, externe Referenz und letzten Fehler über die Print-Job-API
   nachvollziehen.

Der Zustandsautomat erlaubt `queued|failed → printing → printed|failed`.
Bereits erfolgreich gedruckte oder gerade laufende Aufträge können nicht erneut
gestartet werden. Ein fehlgeschlagener Auftrag kann dagegen kontrolliert erneut
ausgeführt werden.

## HTTP-Vertrag

`HttpPrintTransport` sendet `POST {endpointUrl}/print-jobs` mit Bearer-Token und
`Idempotency-Key`. Der Print-Gateway muss `jobReference` zurückgeben. Unterstützt
werden zunächst `PDF` und `ZPL` sowie Carrier-Labels, allgemeine Dokumente und
Lademanifeste.
