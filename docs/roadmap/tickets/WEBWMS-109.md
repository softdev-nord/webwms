---
id: WEBWMS-109
issue_type: Story
epic: WEBWMS-EPIC-EXTENSIONS
status: Done
priority: High
story_points: 13
component: "Erweiterte Funktionen"
feature_group: "Billing & Contract"
source_feature: WEBWMS-REQ-109
---

# WEBWMS-109: Billing, Contract und Finanzexport vervollständigen

## User Story

Als fachlich berechtigter Benutzer möchte ich Lagergeld, Dienstleistungen und Werkverträge rechtssicher abrechnen, damit die Funktion zentral und nachvollziehbar genutzt werden kann.

## Fachlicher Umfang

**Prozesskontext:** Leistung → Bewertung → Rechnung → Finanzexport

## Akzeptanzkriterien

1. Tarife, Artikelabrechnungsgruppen, Regeln, Kostenstellen, Sachkonten und Steuerschlüssel sind versioniert pflegbar.
2. Lagergeld, Dienstleistungen und Werkvertrag können einzeln oder auf einer gemeinsamen Rechnung abgerechnet werden.
3. Rechnungsvorlagen, Sofortrechnungen, Nummernkreise, Storno und Mengen-/Währungsumrechnung sind nachvollziehbar.
4. Rechnungen können als PDF, ZUGFeRD und DATEV-kompatibler Export ausgegeben werden.
5. Alle Schreiboperationen sind mandantensicher, autorisiert, auditierbar und transaktional.
6. Fachliche Erfolgs-, Fehler- und Grenzfälle sind automatisiert getestet; V3-Weboberfläche, API und Dokumentation sind vollständig.

## Abhängigkeiten

Abhängig von den vorhandenen WebWMS-3.0-Basismodulen und den bestehenden Vorgängertickets.

## Implementierungsstand

**Status:** Done

Der Erweiterungsmodul-Slice vom 24.09.2026 stellt den mandantensicheren Konfigurations- und Workflowkern, getrennte V3-Ansichten, JSON-API, Berechtigungen, Auditierung und automatisierte Tests bereit.
