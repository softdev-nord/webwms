# Abschlussanalyse Inbound und Outbound vom 23.09.2026

## Ergebnis

Die Epics `WEBWMS-EPIC-INBOUND` und `WEBWMS-EPIC-OUTBOUND` erfüllen ihre dokumentierten vertikalen Prozessketten. Zusammen sind 31 Stories mit 191 Story Points abgeschlossen.

| Epic | Stories Done | Story Points Done | Abgedeckte Prozesskette |
| --- | ---: | ---: | --- |
| WEBWMS-EPIC-INBOUND | 14/14 | 85/85 | Bestellung/Avis → Annahme → QS/Sperre → Einlagerung/Cross-Docking |
| WEBWMS-EPIC-OUTBOUND | 17/17 | 106/106 | Auftrag/Vorschau → Reservierung/Picking → QS/Packen → Versand/Tour/Verladung → Rückmeldung |

## Outbound-Abschluss

Der neue Warenausgangsleitstand schließt die bisher offenen Querschnittsfunktionen:

- artikelbezogene Bedarfs-, Bestands- und Engpassvorschau;
- begründetes Auftragsstorno vor Freigabe;
- verpflichtende Ausgangs-QS vor dem Packprozess;
- regelbasierte Carrier- und Serviceauswahl;
- Paket-, Carrier-, Fahrzeug- und Tourgewichte;
- chronologische Trackingevents;
- Tourstammdaten und geordnete Stopps;
- archivierte Liefer-, Pack-, Lade- und CMR-Dokumente;
- Web- und JSON-Zugänge mit bestehenden V3-Berechtigungen und Auditdaten.

## Roadmap-Gesamtstand

| Status | Stories | Story Points |
| --- | ---: | ---: |
| Done | 66 | 467 |
| Backend umgesetzt | 6 | 50 |
| Teilweise umgesetzt | 11 | 113 |
| Offen | 13 | 120 |
| Gesamt | 96 | 750 |

Der frühere Status `Umgesetzt` wurde bei `WEBWMS-033` bis `WEBWMS-048` in das verbindliche Statusmodell als `Done` überführt. Damit sind Epic- und Ticketdateien konsistent.
