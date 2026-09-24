---
id: WEBWMS-106
issue_type: Story
epic: WEBWMS-EPIC-EXTENSIONS
status: Done
priority: Medium
story_points: 13
component: "Erweiterte Funktionen"
feature_group: "Transport & Kommissionierung"
source_feature: WEBWMS-REQ-106
---

# WEBWMS-106: Erweiterte Pickverfahren und Assistenzsysteme implementieren

## User Story

Als fachlich berechtigter Benutzer möchte ich zusätzliche Pickstrategien und Assistenzsysteme konfigurierbar einsetzen, damit die Funktion zentral und nachvollziehbar genutzt werden kann.

## Fachlicher Umfang

**Prozesskontext:** Auftrag → Führung → Validierung → Abschluss

## Akzeptanzkriterien

1. Zonenserielles Picking, Mehrmengen, Set-Artikel und frei wählbarer Zugriff sind regelbasiert steuerbar.
2. Barcodevalidierung kann GTIN, individuellen Barcode und Lieferantenmaterialnummer verlangen.
3. Pick by Vision ist über einen geräteunabhängigen Aufgaben-/Bestätigungskanal integrierbar.
4. Pick by Light, Pick to Belt und Put to Light sind als deaktivierbare Adapterpunkte vorbereitet, ohne den aktuellen Funktionsumfang davon abhängig zu machen.
5. Alle Schreiboperationen sind mandantensicher, autorisiert, auditierbar und transaktional.
6. Fachliche Erfolgs-, Fehler- und Grenzfälle sind automatisiert getestet; V3-Weboberfläche, API und Dokumentation sind vollständig.

## Abhängigkeiten

Abhängig von den vorhandenen WebWMS-3.0-Basismodulen und den bestehenden Vorgängertickets.

## Implementierungsstand

**Status:** Done

Der Erweiterungsmodul-Slice vom 24.09.2026 stellt den mandantensicheren Konfigurations- und Workflowkern, getrennte V3-Ansichten, JSON-API, Berechtigungen, Auditierung und automatisierte Tests bereit.
