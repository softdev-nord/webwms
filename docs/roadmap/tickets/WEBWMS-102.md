---
id: WEBWMS-102
issue_type: Story
epic: WEBWMS-EPIC-EXTENSIONS
status: Done
priority: Highest
story_points: 8
component: "Erweiterte Funktionen"
feature_group: "Administration"
source_feature: WEBWMS-REQ-102
---

# WEBWMS-102: Login-Richtlinien, Login-Historie und Arbeitsstationen implementieren

## User Story

Als fachlich berechtigter Benutzer möchte ich Sicherheitsrichtlinien und arbeitsplatzbezogene Sitzungen zentral administrieren, damit die Funktion zentral und nachvollziehbar genutzt werden kann.

## Fachlicher Umfang

**Prozesskontext:** Richtlinie → Anmeldung → Nachweis

## Akzeptanzkriterien

1. Mandantenweite und benutzerspezifische Kennwortregeln, Ablauf und erzwungener Passwortwechsel sind konfigurierbar.
2. Erfolgreiche und fehlgeschlagene Anmeldungen werden mit Zeitpunkt, Benutzer, IP/Client und Ursache protokolliert.
3. Benutzer können Profil, Sprache und zulässige Darstellungseinstellungen pflegen.
4. Arbeitsstationen können einer Sitzung zugeordnet werden und steuern Drucker, Dokumentvorlage, Kopien und Mehrfachausgabe.
5. Alle Schreiboperationen sind mandantensicher, autorisiert, auditierbar und transaktional.
6. Fachliche Erfolgs-, Fehler- und Grenzfälle sind automatisiert getestet; V3-Weboberfläche, API und Dokumentation sind vollständig.

## Abhängigkeiten

Abhängig von den vorhandenen WebWMS-3.0-Basismodulen und den bestehenden Vorgängertickets.

## Implementierungsstand

**Status:** Done

Der Erweiterungsmodul-Slice vom 24.09.2026 stellt den mandantensicheren Konfigurations- und Workflowkern, getrennte V3-Ansichten, JSON-API, Berechtigungen, Auditierung und automatisierte Tests bereit.
