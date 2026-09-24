---
id: WEBWMS-097
issue_type: Story
epic: WEBWMS-EPIC-EXTENSIONS
status: Done
priority: Highest
story_points: 13
component: "Erweiterte Funktionen"
feature_group: "Stammdaten"
source_feature: WEBWMS-REQ-097
---

# WEBWMS-097: Erweiterte Artikel-, Barcode- und Mengeneinheitenstammdaten

## User Story

Als fachlich berechtigter Benutzer möchte ich konfigurierbare Barcodeparser, Mengeneinheiten, Umrechnungen, Gebinde, Übersetzungen und Alternativartikel zentral pflegen, damit die Funktion zentral und nachvollziehbar genutzt werden kann.

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

Abhängig von den vorhandenen WebWMS-3.0-Basismodulen und den bestehenden Vorgängertickets.

## Implementierungsstand

**Status:** Done

Der Erweiterungsmodul-Slice vom 24.09.2026 stellt den mandantensicheren Konfigurations- und Workflowkern, getrennte V3-Ansichten, JSON-API, Berechtigungen, Auditierung und automatisierte Tests bereit.
