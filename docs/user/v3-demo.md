# WebWMS V3 lokal testen

Der erste vertikale V3-Slice stellt einen eigenen, mandantenfähigen Arbeitsbereich mit E-Mail-Login, Dashboard und Bestandsübersicht bereit.

## Demo-Daten anlegen

Nach dem Start der lokalen Umgebung werden zuerst alle Migrationen und anschließend der idempotente Demo-Bootstrap ausgeführt:

```shell
docker compose exec php bin/console doctrine:migrations:migrate --no-interaction
docker compose exec php bin/console webwms:v3:demo-bootstrap
```

Der Befehl zeigt beim ersten Lauf ein zufällig erzeugtes Passwort genau einmal an. Alternativ kann für ein lokales Testsystem ein eigenes Passwort aus einer lokalen Umgebungsvariable gesetzt werden:

```shell
docker compose exec php bin/console webwms:v3:demo-bootstrap --password="$WEBWMS_DEMO_PASSWORD"
```

Der zweite und jeder weitere Lauf verändert den bestehenden Benutzer und seinen Bestand nicht.

## Anmeldung und Smoke-Test

1. `/v3/login` im Browser öffnen.
2. Als Mandanten-ID `11111111-1111-4111-8111-111111111111` verwenden.
3. Mit `admin@demo.webwms.local` und dem beim Bootstrap verwendeten Passwort anmelden.
4. Dashboard-Zahlen prüfen und anschließend **Bestand** öffnen.
5. Im Lagerfilter `DEMO-01` wählen. Erwartet werden `DEMO-1000` mit Menge 25 und `DEMO-2000` mit Menge 100.
6. Unter **Aufträge** den Auftrag `DEMO-ORDER-001` öffnen, freigeben und die Menge 2 vom Lagerplatz `A-01-01` allokieren.
7. Eine Pickliste erzeugen, unter **Picking** selbst zuweisen und die Position bestätigen.
8. Aus der abgeschlossenen Pickliste einen Packauftrag erzeugen, die Position in ein Paket übernehmen und den Packauftrag abschließen.
9. Eine Sendung erzeugen, Demo-Tracking und Label-Referenz registrieren und das Label an den `Demo ZPL Drucker` senden.
10. Unter **Verladung** ein Manifest für die etikettierte Sendung mit Tour- und Fahrzeugreferenz anlegen.
11. Die Sendung als verladen bestätigen und anschließend das vollständige Manifest abschließen. Die Sendung wechselt dabei auf `dispatched`.
12. Unter **Integrations-Outbox** die beim Pick-, Pack-, Versand- und Verladeprozess entstandenen Statusmeldungen nach Status filtern und ihre Payload öffnen.
13. Unter **ERP-Verbindungen** eine Verbindung mit einer lokalen HTTPS-Test-URL und einer Credential-Umgebungsvariablen anlegen, anschließend pausieren und erneut aktivieren.
14. Unter **Carrier-Verbindungen** eine Verbindung für den in der Sendung verwendeten Carrier-Code anlegen, pausieren und erneut aktivieren. Mit einem kompatiblen HTTPS-Testadapter können zusätzlich Versandprodukte und die automatische Labelerzeugung geprüft werden.
15. Unter **Druckwarteschlange** den vorhandenen Demo-ZPL-Drucker sowie den aus der Sendung erzeugten Druckauftrag prüfen. Mit einem kompatiblen HTTPS-Printadapter kann der Auftrag ausgeführt und ein fehlgeschlagener Versuch erneut gestartet werden.
16. Unter **Scanner und MDE** das Demo-MDE öffnen und einen akzeptierten sowie einen abgelehnten Scan mit Prozessreferenz erfassen.
17. Unter **Administration** eine eingeschränkte Rolle und einen Benutzer mit E-Mail-Anmeldung anlegen.
18. Einen API-Client erzeugen und das nur einmal angezeigte Credential sicher kopieren.

Der Bootstrap legt einen Demo-Mandanten, Standort, Administrator mit allen derzeitigen V3-Berechtigungen, ein Lager, zwei Lagerplätze, zwei Artikel, Anfangsbestände, einen offenen Beispielauftrag, einen Demo-ZPL-Drucker und ein Demo-MDE an. Der Drucker dient nur zum Testen der Warteschlange; eine Ausführung erfordert eine echte Druckeranbindung. Es werden keine Zugangsdaten im Repository gespeichert.

API-Client-Secrets werden ausschließlich beim Erzeugen angezeigt. In der Datenbank wird nur ihr SHA-256-Hash gespeichert; ein verlorenes Secret muss durch einen neuen Client ersetzt werden.
