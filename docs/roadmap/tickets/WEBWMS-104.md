---
id: WEBWMS-104
issue_type: Story
epic: WEBWMS-EPIC-EXTENSIONS
status: Done
priority: Highest
story_points: 13
component: "Erweiterte Funktionen"
feature_group: "Integration & Technik"
source_feature: WEBWMS-REQ-104
---

# WEBWMS-104: Konfigurierbares Schnittstellen-Mapping und Historie implementieren

## User Story

Als fachlich berechtigter Benutzer möchte ich Datenformate, Nachrichtentypen und Feldzuordnungen ohne Codeänderung konfigurieren, damit die Funktion zentral und nachvollziehbar genutzt werden kann.

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

Abhängig von den vorhandenen WebWMS-3.0-Basismodulen und den bestehenden Vorgängertickets.

## Implementierungsstand

**Status:** Done

Der Erweiterungsmodul-Slice vom 24.09.2026 stellt den mandantensicheren Konfigurations- und Workflowkern, getrennte V3-Ansichten, JSON-API, Berechtigungen, Auditierung und automatisierte Tests bereit.
