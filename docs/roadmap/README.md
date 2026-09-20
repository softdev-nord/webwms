# WebWMS-3.0-Roadmap

Diese Roadmap ersetzt die bisherige Excel-Arbeitsdatei als verbindliche, versionierte Planungsgrundlage. Sie umfasst 96 Stories mit insgesamt 750 Story Points, aufgeteilt auf sieben Epics.

## Statusmodell

| Status | Bedeutung |
| --- | --- |
| `Offen` | Im WebWMS-3.0-Code ist noch keine relevante Umsetzung vorhanden. |
| `Teilweise umgesetzt` | Einzelne fachliche oder technische Grundlagen sind vorhanden. |
| `Backend umgesetzt` | Domain-, Application- und Persistenzkern sind vorhanden; API/UI oder weitere Definition-of-Done-Bestandteile fehlen noch. |
| `Done` | Sämtliche Akzeptanzkriterien einschließlich API/UI, Autorisierung, Auditierung und Tests sind erfüllt. |

Ein Ticket darf nur dann auf `Done` wechseln, wenn seine Akzeptanzkriterien vollständig nachgewiesen sind. Ein vorhandener Backendkern allein reicht dafür nicht aus.

## Epics

| Epic | Modul | Stories | Story Points |
| --- | --- | ---: | ---: |
| [WEBWMS-EPIC-INBOUND](epics/inbound.md) | Wareneingang | 14 | 85 |
| [WEBWMS-EPIC-INVENTORY](epics/inventory.md) | Lagerverwaltung | 18 | 126 |
| [WEBWMS-EPIC-FULFILLMENT](epics/fulfillment.md) | Transport & Kommissionierung | 16 | 132 |
| [WEBWMS-EPIC-OUTBOUND](epics/outbound.md) | Warenausgang & Versand | 17 | 106 |
| [WEBWMS-EPIC-PLATFORM](epics/platform.md) | Zusatzfunktionen | 9 | 81 |
| [WEBWMS-EPIC-ADMIN](epics/admin.md) | Administration | 9 | 76 |
| [WEBWMS-EPIC-INTEGRATION](epics/integration.md) | Integration & Technik | 13 | 144 |

## Aktueller Backendfortschritt

Nach dem V3-Waagen-und-Volumenmessungs-Slice besitzen 22 Tickets mit 157 Story Points einen substanziellen Backendkern. Weitere 33 Tickets mit 257 Story Points sind teilweise umgesetzt. Damit wurden 55 von 96 Tickets und 414 von 750 Story Points zumindest fachlich oder technisch begonnen.

## Ergänzende Roadmap-Dokumente

- [Prozessketten](processes.md)
- [Fachliches Datenmodell](data-model.md)
- [Quellen](sources.md)
- [Fortschrittsanalyse vom 18.09.2026](analysis/2026-09-18-progress.md)

## Pflege

Jeder fachliche Slice aktualisiert gemeinsam mit Implementierung und Dokumentation:

1. den Status der betroffenen Ticketdateien;
2. konkrete Klassen, Migrationen, Tests und Dokumentationsseiten als Nachweise;
3. bekannte Restarbeiten zur vollständigen Definition of Done;
4. bei wesentlichen Meilensteinen eine neue datierte Fortschrittsanalyse.

Die ursprünglichen COGLAS-Referenzen bleiben in jeder Ticketdatei erhalten. Die Excel-Datei wird nach dieser Überführung nicht mehr als laufendes Statussystem gepflegt.
