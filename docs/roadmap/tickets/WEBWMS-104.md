---
id: WEBWMS-104
issue_type: Story
epic: WEBWMS-EPIC-PARITY
status: Offen
priority: Highest
story_points: 13
component: "Coglas-Funktionsparität"
feature_group: "Integration & Technik"
source_feature: COGLAS-HELP-104
---

# WEBWMS-104: Konfigurierbares Schnittstellen-Mapping und Historie implementieren

## User Story

Als fachlich berechtigter Benutzer möchte ich Datenformate, Nachrichtentypen und Feldzuordnungen ohne Codeänderung konfigurieren, damit WebWMS die im Coglas-Helpcenter dokumentierte Prozessabdeckung erreicht.

## Fachlicher Umfang

**Prozesskontext:** Eingang → Mapping → Verarbeitung → Rückmeldung

## Akzeptanzkriterien

1. JSON, XML, CSV und XLSX unterstützen versionierte Feld-, Wert-, Datums- und Transformationsmappings.
2. Datei-, HTTP-, SFTP- und Queue-Endpunkte können Nachrichtentypen und Servicekonfigurationen zugeordnet werden.
3. Die Schnittstellenhistorie verknüpft Rohdaten, Mappingversion, Zielobjekt, Status, Fehler und Wiederholungen.
4. Listenaktionen, Berichte sowie synchrone und asynchrone Empfangsbestätigungen sind autorisiert und auditierbar.
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

