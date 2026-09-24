# WebWMS-3.0-Roadmap

Diese Roadmap ersetzt die bisherige Excel-Arbeitsdatei als verbindliche, versionierte Planungsgrundlage. Sie umfasst 110 Stories mit insgesamt 912 Story Points, aufgeteilt auf acht Epics.

## Statusmodell

| Status | Bedeutung |
| --- | --- |
| `Offen` | Im WebWMS-3.0-Code ist noch keine relevante Umsetzung vorhanden. |
| `Teilweise umgesetzt` | Einzelne fachliche oder technische Grundlagen sind vorhanden. |
| `Backend umgesetzt` | Domain-, Application- und Persistenzkern sind vorhanden; API/UI oder weitere Definition-of-Done-Bestandteile fehlen noch. |
| `Done` | Sämtliche Akzeptanzkriterien einschließlich API/UI, Autorisierung, Auditierung und Tests sind erfüllt. Der frühere Status `Umgesetzt` wurde in `Done` vereinheitlicht. |

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
| [WEBWMS-EPIC-PARITY](epics/functional-parity.md) | Coglas-Funktionsparität | 14 | 162 |

## Aktueller Gesamtfortschritt

Stand 24.09.2026 sind 82 Tickets mit 598 Story Points vollständig `Done`. Weitere 2 Tickets mit 21 Story Points besitzen einen Backendkern und 9 Tickets mit 97 Story Points sind teilweise umgesetzt. 17 Tickets mit 196 Story Points sind offen. Damit wurden 93 von 110 Tickets und 716 von 912 Story Points zumindest fachlich oder technisch begonnen.

Die Epics **Wareneingang**, **Lagerverwaltung**, **Transport & Kommissionierung**, **Warenausgang & Versand** sowie **Zusatzfunktionen** sind vollständig abgeschlossen. Die Tabellen der sieben Epic-Dateien wurden mit den Statuswerten der einzelnen Ticketdateien synchronisiert.

## Ergänzende Roadmap-Dokumente

- [Prozessketten](processes.md)
- [Fachliches Datenmodell](data-model.md)
- [Quellen](sources.md)
- [Fortschrittsanalyse vom 18.09.2026](analysis/2026-09-18-progress.md)
- [Fortschrittsanalyse Wareneingang vom 20.09.2026](analysis/2026-09-20-inbound-progress.md)
- [Abschlussanalyse Inbound und Outbound vom 23.09.2026](analysis/2026-09-23-inbound-outbound-completion.md)
- [Abschlussanalyse Platform vom 23.09.2026](analysis/2026-09-23-platform-completion.md)
- [Abschlussanalyse Inventory vom 23.09.2026](analysis/2026-09-23-inventory-completion.md)
- [Coglas-Helpcenter-Gap-Analyse vom 24.09.2026](analysis/2026-09-24-coglas-helpcenter-gap-analysis.md)
- [Abschlussanalyse Lagerbasis vom 21.09.2026](analysis/2026-09-21-inventory-workspace.md)
- [Abschlussanalyse Bestandsattribute und Rückverfolgung vom 21.09.2026](analysis/2026-09-21-stock-traceability.md)
- [Abschlussanalyse Entnahmestrategien vom 21.09.2026](analysis/2026-09-21-stock-selection.md)
- [Abschlussanalyse Bestandssperren vom 21.09.2026](analysis/2026-09-21-stock-blocking.md)

## Pflege

Jeder fachliche Slice aktualisiert gemeinsam mit Implementierung und Dokumentation:

1. den Status der betroffenen Ticketdateien;
2. konkrete Klassen, Migrationen, Tests und Dokumentationsseiten als Nachweise;
3. bekannte Restarbeiten zur vollständigen Definition of Done;
4. bei wesentlichen Meilensteinen eine neue datierte Fortschrittsanalyse.

Die ursprünglichen COGLAS-Referenzen bleiben in jeder Ticketdatei erhalten. Die Excel-Datei wird nach dieser Überführung nicht mehr als laufendes Statussystem gepflegt.
