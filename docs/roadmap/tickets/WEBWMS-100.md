---
id: WEBWMS-100
issue_type: Story
epic: WEBWMS-EPIC-PARITY
status: Offen
priority: High
story_points: 13
component: "Coglas-Funktionsparität"
feature_group: "Lageroptimierung"
source_feature: COGLAS-HELP-100
---

# WEBWMS-100: ABC/XYZ, Slotting und Lagerreorganisation implementieren

## User Story

Als fachlich berechtigter Benutzer möchte ich Bestandsbewegungen analysieren und daraus nachvollziehbare Optimierungsaufträge erzeugen, damit WebWMS die im Coglas-Helpcenter dokumentierte Prozessabdeckung erreicht.

## Fachlicher Umfang

**Prozesskontext:** Analyse → Vorschlag → Reorganisation

## Akzeptanzkriterien

1. Konfigurierbare Schwellen erzeugen reproduzierbare ABC- und XYZ-Klassifizierungen.
2. Slotting berücksichtigt Umschlag, Volumen, Gewicht, Restriktionen und Pickwege.
3. Verdichtungs- und Reorganisationsvorschläge können geprüft, freigegeben und als Umlagerungsaufträge erzeugt werden.
4. Simulation, Ausführung und erzielte Verbesserung werden auditierbar gegenübergestellt.
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

