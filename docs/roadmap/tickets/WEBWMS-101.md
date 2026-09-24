---
id: WEBWMS-101
issue_type: Story
epic: WEBWMS-EPIC-EXTENSIONS
status: Done
priority: High
story_points: 8
component: "Erweiterte Funktionen"
feature_group: "Bestandsplanung"
source_feature: WEBWMS-REQ-101
---

# WEBWMS-101: Verschrottung, Bestandsabgleich und Bestellvorschläge implementieren

## User Story

Als fachlich berechtigter Benutzer möchte ich Bestände kontrolliert vernichten sowie standortübergreifend abgleichen und nachbestellen, damit die Funktion zentral und nachvollziehbar genutzt werden kann.

## Fachlicher Umfang

**Prozesskontext:** Bestand → Entscheidung → Buchung/Vorschlag

## Akzeptanzkriterien

1. Verschrottung erfolgt über einen autorisierten, irreversiblen und vollständig protokollierten Prozess mit Grund.
2. Bestände mehrerer Standorte können dimensionsgenau abgeglichen und Abweichungen bearbeitet werden.
3. Mindestbestandsvorschau berücksichtigt verfügbaren, reservierten, bestellten und prognostizierten Bestand.
4. Aus markierten Vorschlägen entstehen lieferantenbezogene Bestellungen mit anpassbarer Menge.
5. Alle Schreiboperationen sind mandantensicher, autorisiert, auditierbar und transaktional.
6. Fachliche Erfolgs-, Fehler- und Grenzfälle sind automatisiert getestet; V3-Weboberfläche, API und Dokumentation sind vollständig.

## Abhängigkeiten

Abhängig von den vorhandenen WebWMS-3.0-Basismodulen und den bestehenden Vorgängertickets.

## Implementierungsstand

**Status:** Done

Der Erweiterungsmodul-Slice vom 24.09.2026 stellt den mandantensicheren Konfigurations- und Workflowkern, getrennte V3-Ansichten, JSON-API, Berechtigungen, Auditierung und automatisierte Tests bereit.
