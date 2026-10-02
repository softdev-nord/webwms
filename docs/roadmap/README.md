# WebWMS-3.0-Roadmap

Diese Roadmap ersetzt die bisherige Excel-Arbeitsdatei als verbindliche, versionierte Planungsgrundlage. Sie umfasst 112 Stories mit insgesamt 967 Story Points, aufgeteilt auf acht Epics.

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
| [WEBWMS-EPIC-PLATFORM](epics/platform.md) | Zusatzfunktionen | 11 | 136 |
| [WEBWMS-EPIC-ADMIN](epics/admin.md) | Administration | 9 | 76 |
| [WEBWMS-EPIC-INTEGRATION](epics/integration.md) | Integration & Technik | 13 | 144 |
| [WEBWMS-EPIC-EXTENSIONS](epics/functional-extensions.md) | Erweiterte Funktionen | 14 | 162 |

## Aktueller Gesamtfortschritt

Stand 02.10.2026 sind 111 von 112 Tickets und 933 von 967 Story Points `Done`. WEBWMS-112 befindet sich in Umsetzung; das View- und Feldmodell sowie Anmeldung, Benutzer-, Rollen-, API-Client- und zentrale Systemkonfiguration sind auf den vollständigen Dokumentationsstandard migriert.

Sieben Epics sind vollständig abgeschlossen. Das Plattform-Epic enthält mit WEBWMS-112 noch die vollständige View-, Formular- und Feldabdeckung des Benutzerhandbuchs. WEBWMS-111 stellt hierfür weiterhin die integrierte, zweisprachige und durchsuchbare technische Handbuchplattform bereit. Die Tabellen der acht Epic-Dateien sind mit den Statuswerten der einzelnen Ticketdateien synchronisiert.

## Ergänzende Roadmap-Dokumente

- [Prozessketten](processes.md)
- [Fachliches Datenmodell](data-model.md)
- [Quellen](sources.md)
- [Fortschrittsanalyse vom 18.09.2026](analysis/2026-09-18-progress.md)
- [Fortschrittsanalyse Wareneingang vom 20.09.2026](analysis/2026-09-20-inbound-progress.md)
- [Abschlussanalyse Inbound und Outbound vom 23.09.2026](analysis/2026-09-23-inbound-outbound-completion.md)
- [Abschlussanalyse Platform vom 23.09.2026](analysis/2026-09-23-platform-completion.md)
- [Abschlussanalyse Inventory vom 23.09.2026](analysis/2026-09-23-inventory-completion.md)
- [Abschlussanalyse Lagerbasis vom 21.09.2026](analysis/2026-09-21-inventory-workspace.md)
- [Abschlussanalyse Bestandsattribute und Rückverfolgung vom 21.09.2026](analysis/2026-09-21-stock-traceability.md)
- [Abschlussanalyse Entnahmestrategien vom 21.09.2026](analysis/2026-09-21-stock-selection.md)
- [Abschlussanalyse Bestandssperren vom 21.09.2026](analysis/2026-09-21-stock-blocking.md)
- [Abschluss-Audit vom 01.10.2026](analysis/2026-10-01-completion-audit.md)
- [Abschlussanalyse Benutzerhandbuch vom 01.10.2026](analysis/2026-10-01-user-handbook-completion.md)

## Pflege

Jeder fachliche Slice aktualisiert gemeinsam mit Implementierung und Dokumentation:

1. den Status der betroffenen Ticketdateien;
2. konkrete Klassen, Migrationen, Tests und Dokumentationsseiten als Nachweise;
3. bekannte Restarbeiten zur vollständigen Definition of Done;
4. bei wesentlichen Meilensteinen eine neue datierte Fortschrittsanalyse.

Die ursprünglichen WEBWMS-Referenzen bleiben in jeder Ticketdatei erhalten. Die Excel-Datei wird nach dieser Überführung nicht mehr als laufendes Statussystem gepflegt.
