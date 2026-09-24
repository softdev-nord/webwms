---
id: WEBWMS-099
issue_type: Story
epic: WEBWMS-EPIC-PARITY
status: Done
priority: High
story_points: 13
component: "Coglas-Funktionsparität"
feature_group: "Lagerstruktur"
source_feature: COGLAS-HELP-099
---

# WEBWMS-099: Erweiterte Lagerplatz- und Zonenrestriktionen implementieren

## User Story

Als fachlich berechtigter Benutzer möchte ich Fachlasten, Temperatur, Stapelung und mehrfachtiefe Lagerung regelbasiert prüfen, damit WebWMS die im Coglas-Helpcenter dokumentierte Prozessabdeckung erreicht.

## Fachlicher Umfang

**Prozesskontext:** Topologie → Restriktion → Platzprüfung

## Akzeptanzkriterien

1. Lagerplätze und Zonen unterstützen Gewichts-/Kapazitätsgrenzen, Temperaturbereiche und Stapelparameter.
2. Mehrfachtiefe Plätze besitzen Zugriffstiefe, Blockierungs- und Auslagerungsregeln.
3. Gefahrstoffbereiche unterstützen Maximalgewicht, Nur-Gefahrstoff und eine Zusammenlagerungsmatrix.
4. Einlagerung und Umlagerung lehnen verletzte Restriktionen transaktional ab und erklären den Grund.
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
