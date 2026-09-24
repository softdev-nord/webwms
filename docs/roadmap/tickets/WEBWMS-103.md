---
id: WEBWMS-103
issue_type: Story
epic: WEBWMS-EPIC-PARITY
status: Done
priority: High
story_points: 13
component: "Coglas-Funktionsparität"
feature_group: "Dokumente & Druck"
source_feature: COGLAS-HELP-103
---

# WEBWMS-103: Objektweite Anhänge, Reports und Layoutvorlagen implementieren

## User Story

Als fachlich berechtigter Benutzer möchte ich Dokumente, Reports und Layouts für alle relevanten Geschäftsobjekte verwalten, damit WebWMS die im Coglas-Helpcenter dokumentierte Prozessabdeckung erreicht.

## Fachlicher Umfang

**Prozesskontext:** Objekt → Vorlage → Ausgabe

## Akzeptanzkriterien

1. Anhänge, Fotos und Videos können an Artikel, Partner, Belege, Aufträge, Lagereinheiten und Prozesse gebunden werden.
2. Versionierte Report- und Labelvorlagen besitzen Dokumenttyp, Sprache, Gültigkeit und Mandantenbezug.
3. Vorschau, PDF-/Tabellenausgabe, direkter Druck und Nachdruck verwenden dieselbe freigegebene Vorlage.
4. Berechtigungen, Dateityp-/Größenprüfung, Aufbewahrung und Änderungshistorie sind durchgängig.
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
