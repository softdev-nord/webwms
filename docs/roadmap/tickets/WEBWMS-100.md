---
id: WEBWMS-100
issue_type: Story
epic: WEBWMS-EPIC-EXTENSIONS
status: Done
priority: High
story_points: 13
component: "Erweiterte Funktionen"
feature_group: "Lageroptimierung"
source_feature: WEBWMS-REQ-100
---

# WEBWMS-100: ABC/XYZ, Slotting und Lagerreorganisation implementieren

## User Story

Als fachlich berechtigter Benutzer möchte ich Bestandsbewegungen analysieren und daraus nachvollziehbare Optimierungsaufträge erzeugen, damit die Funktion zentral und nachvollziehbar genutzt werden kann.

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

Abhängig von den vorhandenen WebWMS-3.0-Basismodulen und den bestehenden Vorgängertickets.

## Implementierungsstand

**Status:** Done

Der Erweiterungsmodul-Slice vom 24.09.2026 stellt den mandantensicheren Konfigurations- und Workflowkern, getrennte V3-Ansichten, JSON-API, Berechtigungen, Auditierung und automatisierte Tests bereit.
