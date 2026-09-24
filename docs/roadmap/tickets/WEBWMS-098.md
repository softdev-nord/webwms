---
id: WEBWMS-098
issue_type: Story
epic: WEBWMS-EPIC-EXTENSIONS
status: Done
priority: High
story_points: 8
component: "Erweiterte Funktionen"
feature_group: "Ladehilfsmittel"
source_feature: WEBWMS-REQ-098
---

# WEBWMS-098: Geschlossenen Behälterkreislauf implementieren

## User Story

Als fachlich berechtigter Benutzer möchte ich nummerierte Mehrwegbehälter lückenlos von Ausgabe bis Rücklauf verfolgen, damit die Funktion zentral und nachvollziehbar genutzt werden kann.

## Fachlicher Umfang

**Prozesskontext:** Behälter → Verwendung → Rücklauf

## Akzeptanzkriterien

1. Behältertypen und eindeutige Barcodes können gepflegt und importiert/exportiert werden.
2. Status, aktuelle Verwendung, Standort, Partner und Datum der letzten Verwendung sind nachvollziehbar.
3. Doppelte Barcodes und ungültige Zustandswechsel werden verhindert.
4. Ausgabe, Rücknahme und Differenzen erzeugen auditierte LHM-Kontobewegungen.
5. Alle Schreiboperationen sind mandantensicher, autorisiert, auditierbar und transaktional.
6. Fachliche Erfolgs-, Fehler- und Grenzfälle sind automatisiert getestet; V3-Weboberfläche, API und Dokumentation sind vollständig.

## Abhängigkeiten

Abhängig von den vorhandenen WebWMS-3.0-Basismodulen und den bestehenden Vorgängertickets.

## Implementierungsstand

**Status:** Done

Der Erweiterungsmodul-Slice vom 24.09.2026 stellt den mandantensicheren Konfigurations- und Workflowkern, getrennte V3-Ansichten, JSON-API, Berechtigungen, Auditierung und automatisierte Tests bereit.
