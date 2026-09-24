---
id: WEBWMS-106
issue_type: Story
epic: WEBWMS-EPIC-PARITY
status: Offen
priority: Medium
story_points: 13
component: "Coglas-Funktionsparität"
feature_group: "Transport & Kommissionierung"
source_feature: COGLAS-HELP-106
---

# WEBWMS-106: Erweiterte Pickverfahren und Assistenzsysteme implementieren

## User Story

Als fachlich berechtigter Benutzer möchte ich zusätzliche Pickstrategien und Assistenzsysteme konfigurierbar einsetzen, damit WebWMS die im Coglas-Helpcenter dokumentierte Prozessabdeckung erreicht.

## Fachlicher Umfang

**Prozesskontext:** Auftrag → Führung → Validierung → Abschluss

## Akzeptanzkriterien

1. Zonenserielles Picking, Mehrmengen, Set-Artikel und frei wählbarer Zugriff sind regelbasiert steuerbar.
2. Barcodevalidierung kann GTIN, individuellen Barcode und Lieferantenmaterialnummer verlangen.
3. Pick by Vision ist über einen geräteunabhängigen Aufgaben-/Bestätigungskanal integrierbar.
4. Pick by Light, Pick to Belt und Put to Light sind als deaktivierbare Adapterpunkte vorbereitet, ohne aktuelle Parität davon abhängig zu machen.
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

