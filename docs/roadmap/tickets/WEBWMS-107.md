---
id: WEBWMS-107
issue_type: Story
epic: WEBWMS-EPIC-PARITY
status: Offen
priority: High
story_points: 13
component: "Coglas-Funktionsparität"
feature_group: "Warenausgang & Versand"
source_feature: COGLAS-HELP-107
---

# WEBWMS-107: Automatisierten Warenausgang und Packoptimierung implementieren

## User Story

Als fachlich berechtigter Benutzer möchte ich Sortierung, Verpackung und Ausgang regelbasiert automatisieren, damit WebWMS die im Coglas-Helpcenter dokumentierte Prozessabdeckung erreicht.

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

