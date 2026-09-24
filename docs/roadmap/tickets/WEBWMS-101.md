---
id: WEBWMS-101
issue_type: Story
epic: WEBWMS-EPIC-PARITY
status: Offen
priority: High
story_points: 8
component: "Coglas-Funktionsparität"
feature_group: "Bestandsplanung"
source_feature: COGLAS-HELP-101
---

# WEBWMS-101: Verschrottung, Bestandsabgleich und Bestellvorschläge implementieren

## User Story

Als fachlich berechtigter Benutzer möchte ich Bestände kontrolliert vernichten sowie standortübergreifend abgleichen und nachbestellen, damit WebWMS die im Coglas-Helpcenter dokumentierte Prozessabdeckung erreicht.

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

Abhängig von den vorhandenen WebWMS-3.0-Basismodulen und den in der Gap-Analyse genannten Vorgängertickets.

## Implementierungsstand

**Status:** Offen

Die Helpcenter-Analyse vom 24.09.2026 konnte keine vollständige, vertikale Umsetzung nachweisen.

## Quelle

- [Coglas-Helpcenter-Gap-Analyse](../analysis/2026-09-24-coglas-helpcenter-gap-analysis.md)
- https://help.coglas.com/ger/coglas-prozesse
- https://help.coglas.com/ger/coglas-menu
- https://help.coglas.com/ger/prozesse-konzepte
- https://help.coglas.com/ger/changelog

