---
id: WEBWMS-097
issue_type: Story
epic: WEBWMS-EPIC-PARITY
status: Offen
priority: Highest
story_points: 13
component: "Coglas-Funktionsparität"
feature_group: "Stammdaten"
source_feature: COGLAS-HELP-097
---

# WEBWMS-097: Erweiterte Artikel-, Barcode- und Mengeneinheitenstammdaten

## User Story

Als fachlich berechtigter Benutzer möchte ich konfigurierbare Barcodeparser, Mengeneinheiten, Umrechnungen, Gebinde, Übersetzungen und Alternativartikel zentral pflegen, damit WebWMS die im Coglas-Helpcenter dokumentierte Prozessabdeckung erreicht.

## Fachlicher Umfang

**Prozesskontext:** Stammdaten → Validierung → operative Verwendung

## Akzeptanzkriterien

1. Barcodekonfigurationen unterstützen Identifikator, Start, Länge, numerische Werte, Dezimalindikator, Datumsformat, Suffix, Mapping, Priorität und verschachtelte Konfigurationen.
2. Mengeneinheiten besitzen Art, Faktor, Standardkennzeichen und Artikeltauglichkeit; Umrechnungen werden historisch konsistent behandelt.
3. Artikel unterstützen Gebindegrößen, alternative Artikel, Prozesshinweise und mandantenspezifische Übersetzungen.
4. Konfigurationen sind über getrennte V3-Übersichts-, Anlage- und Bearbeitungsseiten sowie API verfügbar.
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

