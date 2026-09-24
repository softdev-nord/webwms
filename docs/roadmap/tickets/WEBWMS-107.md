---
id: WEBWMS-107
issue_type: Story
epic: WEBWMS-EPIC-EXTENSIONS
status: Done
priority: High
story_points: 13
component: "Erweiterte Funktionen"
feature_group: "Warenausgang & Versand"
source_feature: WEBWMS-REQ-107
---

# WEBWMS-107: Automatisierten Warenausgang und Packoptimierung implementieren

## User Story

Als fachlich berechtigter Benutzer möchte ich Sortierung, Verpackung und Ausgang regelbasiert automatisieren, damit die Funktion zentral und nachvollziehbar genutzt werden kann.

## Fachlicher Umfang

**Prozesskontext:** Bereitstellung → Sortierung → Packoptimierung → Versand

## Akzeptanzkriterien

1. Direktverpackung, automatische Verpackung und automatischer Warenausgang sind je Kunde/Auftrag konfigurierbar.
2. Packmittelwahl und Packstückoptimierung berücksichtigen Maße, Gewicht, Gefahrgut, Träger-LE und Carriergrenzen.
3. Sortierung und Konsolidierung unterstützen Gruppierungs-, Sendungs- und Auftragsmerkmale.
4. Sammelverzollung, Bestandszuordnung, Scanprüfung und Status „Label erzeugt“ sind im Zustandsmodell enthalten.
5. Alle Schreiboperationen sind mandantensicher, autorisiert, auditierbar und transaktional.
6. Fachliche Erfolgs-, Fehler- und Grenzfälle sind automatisiert getestet; V3-Weboberfläche, API und Dokumentation sind vollständig.

## Abhängigkeiten

Abhängig von den vorhandenen WebWMS-3.0-Basismodulen und den bestehenden Vorgängertickets.

## Implementierungsstand

**Status:** Done

Der Erweiterungsmodul-Slice vom 24.09.2026 stellt den mandantensicheren Konfigurations- und Workflowkern, getrennte V3-Ansichten, JSON-API, Berechtigungen, Auditierung und automatisierte Tests bereit.
