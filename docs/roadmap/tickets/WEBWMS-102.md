---
id: WEBWMS-102
issue_type: Story
epic: WEBWMS-EPIC-PARITY
status: Offen
priority: Highest
story_points: 8
component: "Coglas-Funktionsparität"
feature_group: "Administration"
source_feature: COGLAS-HELP-102
---

# WEBWMS-102: Login-Richtlinien, Login-Historie und Arbeitsstationen implementieren

## User Story

Als fachlich berechtigter Benutzer möchte ich Sicherheitsrichtlinien und arbeitsplatzbezogene Sitzungen zentral administrieren, damit WebWMS die im Coglas-Helpcenter dokumentierte Prozessabdeckung erreicht.

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

