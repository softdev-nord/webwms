# Operative Geräte- und Carrier-Integrationen

Die operativen Integrationen verwenden herstellerneutrale HTTPS-/JSON-Verträge.
Carrier, Scanner/MDE, Drucker, Messgeräte und Lagerautomaten werden
mandantenbezogen konfiguriert; Secrets verbleiben in Umgebungsvariablen.

| Bereich | Eingangsvertrag | Zustands- und Idempotenzmodell |
| --- | --- | --- |
| Carrier | Produkte, Label, Tracking, Manifest | Request-ID je Operation und Fachobjekt |
| Scanner/MDE | Scanereignis mit Prozess und Kontext | Request-ID; `accepted` oder `rejected` |
| Drucker | PDF-/ZPL-Druckauftrag | `queued → printing → printed|failed`; Retry |
| Messgerät | Gewicht und/oder vollständige Abmessungen | Request-ID; Zieländerung nur bei `accepted` |
| Lagerautomat | Gerätebefehl und Rückmeldung | `queued → dispatched → completed|failed` |

Die HTTP-Endpunkte liegen unter `/api/v3`. Die jeweiligen V3-Arbeitsbereiche
stellen Konfiguration, Ausführung und Journale bereit. Geräte und externe
Systeme können dieselben Verträge direkt verwenden; herstellerspezifische
Protokolle werden als Transportadapter hinter den Anwendungsservices ergänzt.

Standort-, Arbeitsplatz- und Prozessrouting für Drucker erfolgt über
`wms_print_routing_rule`. Externe Nebenwirkungen werden mit Request-ID
idempotent ausgeführt und mit Benutzer, Zeitpunkt, Ergebnis und Fehlertext
persistiert.
