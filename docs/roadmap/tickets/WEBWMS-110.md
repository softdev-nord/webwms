---
id: WEBWMS-110
issue_type: Story
epic: WEBWMS-EPIC-EXTENSIONS
status: Done
priority: High
story_points: 13
component: "Erweiterte Funktionen"
feature_group: "Compliance & Qualität"
source_feature: WEBWMS-REQ-110
---

# WEBWMS-110: Außenhandel, Compliance und Qualitätsstichproben implementieren

## User Story

Als fachlich berechtigter Benutzer möchte ich regulatorische Prüfungen und statistische Qualitätskontrollen in Ein- und Ausgang integrieren, damit die Funktion zentral und nachvollziehbar genutzt werden kann.

## Fachlicher Umfang

**Prozesskontext:** Regel → Prüfung → Entscheidung → Nachweis

## Akzeptanzkriterien

1. Sanktionslistenprüfungen können synchron oder asynchron für Partner und Lieferaufträge ausgeführt werden.
2. Konsignation, Vendor Managed Inventory, Zollstatus, Warenwert und Sammelverzollung sind bestands- und belegbezogen.
3. Konfigurierbare Stichprobenpläne bestimmen Umfang, Merkmale und Annahme-/Ablehnungsentscheidung.
4. Treffer, Freigaben, Sperren und externe Prüfnachweise werden revisionssicher protokolliert.
5. Alle Schreiboperationen sind mandantensicher, autorisiert, auditierbar und transaktional.
6. Fachliche Erfolgs-, Fehler- und Grenzfälle sind automatisiert getestet; V3-Weboberfläche, API und Dokumentation sind vollständig.

## Abhängigkeiten

Abhängig von den vorhandenen WebWMS-3.0-Basismodulen und den bestehenden Vorgängertickets.

## Implementierungsstand

**Status:** Done

Der Erweiterungsmodul-Slice vom 24.09.2026 stellt den mandantensicheren Konfigurations- und Workflowkern, getrennte V3-Ansichten, JSON-API, Berechtigungen, Auditierung und automatisierte Tests bereit.
