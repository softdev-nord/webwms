---
id: WEBWMS-108
issue_type: Story
epic: WEBWMS-EPIC-PARITY
status: Done
priority: High
story_points: 13
component: "Coglas-Funktionsparität"
feature_group: "Produktion"
source_feature: COGLAS-HELP-108
---

# WEBWMS-108: Produktion und Montage vollständig abbilden

## User Story

Als fachlich berechtigter Benutzer möchte ich Produktions- und Montageaufträge einschließlich Versorgung und Rückmeldung steuern, damit WebWMS die im Coglas-Helpcenter dokumentierte Prozessabdeckung erreicht.

## Fachlicher Umfang

**Prozesskontext:** Auftrag → Versorgung → Montage → Produktionseingang

## Akzeptanzkriterien

1. Produktionsaufträge besitzen Positionen, Status, Montagereihenfolge, Seriennummern und Fortsetzungszustand.
2. Materialbedarf erzeugt reservierte, priorisierte Versorgungs- und Entsorgungstransporte.
3. Teil-, Ausschuss- und Fertigmeldungen buchen Materialverbrauch und Produktionseingang konsistent.
4. Stücklistenalternativen, KIT-/Displaybildung und Routenzugversorgung sind integriert.
5. Alle Schreiboperationen sind mandantensicher, autorisiert, auditierbar und transaktional.
6. Fachliche Erfolgs-, Fehler- und Grenzfälle sind automatisiert getestet; V3-Weboberfläche, API und Dokumentation sind vollständig.

## Abhängigkeiten

Abhängig von den vorhandenen WebWMS-3.0-Basismodulen und den in der Gap-Analyse genannten Vorgängertickets.

## Implementierungsstand

**Status:** Done

Der Gap-Closing-Slice vom 24.09.2026 stellt den mandantensicheren Konfigurations- und Workflowkern, getrennte V3-Ansichten, JSON-API, Berechtigungen, Auditierung und automatisierte Tests bereit.

## Quelle

- [Coglas-Helpcenter-Gap-Analyse](../analysis/2026-09-24-coglas-helpcenter-gap-analysis.md)
- https://help.coglas.com/ger/coglas-prozesse
- https://help.coglas.com/ger/coglas-menu
- https://help.coglas.com/ger/prozesse-konzepte
- https://help.coglas.com/ger/changelog
