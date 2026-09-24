---
id: WEBWMS-105
issue_type: Story
epic: WEBWMS-EPIC-EXTENSIONS
status: Done
priority: High
story_points: 8
component: "Erweiterte Funktionen"
feature_group: "Wareneingang"
source_feature: WEBWMS-REQ-105
---

# WEBWMS-105: Konfigurierbaren Barcode-Wareneingang erweitern

## User Story

Als fachlich berechtigter Benutzer möchte ich GS1/EAN-128 und kundenspezifische Barcodes für schnelle Wareneingänge auswerten, damit die Funktion zentral und nachvollziehbar genutzt werden kann.

## Fachlicher Umfang

**Prozesskontext:** Scan → Interpretation → Prüfung → Vereinnahmung

## Akzeptanzkriterien

1. Der Wareneingang nutzt die in `WEBWMS-097` gepflegten Parser inklusive verschachtelter Datenfelder.
2. GTIN, Artikel-/Lieferantenmaterialnummer, Menge, Charge, Seriennummer, MHD, Gewicht und individuelle Felder werden zugeordnet.
3. Mehrdeutige oder unvollständige Scans führen in einen erklärbaren Korrekturdialog.
4. Effektive Einlagerungsstrategie, Verpackungseignung, Avis-/Restmenge und Bestandsstatus werden vor Buchung geprüft.
5. Alle Schreiboperationen sind mandantensicher, autorisiert, auditierbar und transaktional.
6. Fachliche Erfolgs-, Fehler- und Grenzfälle sind automatisiert getestet; V3-Weboberfläche, API und Dokumentation sind vollständig.

## Abhängigkeiten

Abhängig von den vorhandenen WebWMS-3.0-Basismodulen und den bestehenden Vorgängertickets.

## Implementierungsstand

**Status:** Done

Der Erweiterungsmodul-Slice vom 24.09.2026 stellt den mandantensicheren Konfigurations- und Workflowkern, getrennte V3-Ansichten, JSON-API, Berechtigungen, Auditierung und automatisierte Tests bereit.
