---
id: WEBWMS-111
issue_type: Story
epic: WEBWMS-EPIC-PLATFORM
status: Done
priority: High
story_points: 21
component: "Benutzerhandbuch"
feature_group: "Dokumentation & Hilfe"
source_feature: WEBWMS-REQ-111
---

# WEBWMS-111: Vollständiges zweisprachiges Benutzerhandbuch implementieren

## User Story

Als Benutzer möchte ich ein vollständiges, verständliches und durchsuchbares
Benutzerhandbuch direkt in WebWMS aufrufen können, damit ich sämtliche
Konfigurationsmöglichkeiten und Funktionen ohne externe Dokumentation sicher
einrichten und bedienen kann.

## Fachlicher Umfang

**Prozesskontext:** Sidebar → Handbuch → Navigation oder Suche → Anleitung →
Konfiguration beziehungsweise Bedienung

Das Benutzerhandbuch beschreibt alle im System verfügbaren Fachmodule,
Ansichten, Rollen, Konfigurationen und Bedienabläufe in Deutsch und Englisch.
Die Inhalte werden als eigene Handbuch-Templates in die bestehende
Oberflächenstruktur integriert. Zu jedem relevanten Konfigurations- und
Bedienabschnitt wird zunächst ein einheitlicher Bildplatzhalter eingebunden,
der in einem nachfolgenden Arbeitsschritt ohne strukturelle Templateänderungen
durch den jeweiligen echten Screenshot ersetzt werden kann.

## Akzeptanzkriterien

1. Sämtliche verfügbaren Konfigurationsmöglichkeiten sind vollständig und
   ausführlich beschrieben. Voraussetzungen, Berechtigungen, Felder,
   zulässige Werte, Standardwerte, Abhängigkeiten, Auswirkungen und typische
   Fehlerfälle werden erläutert.
2. Die Bedienung sämtlicher Funktionen ist anhand nachvollziehbarer
   Schritt-für-Schritt-Anleitungen dokumentiert. Dazu gehören Einstieg,
   Navigation, Lesen, Anlegen, Bearbeiten, Löschen beziehungsweise Stornieren,
   Statusübergänge, Suche, Filter, Pagination sowie fachliche Sonder- und
   Fehlerfälle, soweit die jeweilige Funktion diese Aktionen unterstützt.
3. Das Handbuch besitzt eine fachlich gegliederte Startseite, Kapitel- und
   Unterkapitelnavigation, Breadcrumbs sowie Vor-/Zurück-Navigation und folgt
   der bestehenden WebWMS-Template- und Layoutstruktur.
4. Ein sichtbarer Eintrag in der Sidebar öffnet das Handbuch. Der aktive
   Menüpfad und die zugehörigen Untermenüpunkte werden korrekt dargestellt.
5. Jeder relevante Konfigurations- und Bedienabschnitt enthält einen
   zugänglichen Bildplatzhalter mit aussagekräftigem Alternativtext und einer
   stabilen Bildreferenz. Eine zentrale Platzhaltergrafik wird wiederverwendet
   und kann später abschnittsweise durch echte Screenshots ersetzt werden.
6. Alle Texte des Benutzerhandbuchs liegen in eigenen, ausschließlich für das
   Handbuch vorgesehenen und fachlich gegliederten Übersetzungsdateien
   `handbook.de.yaml` und `handbook.en.yaml`. Es werden weder Texte in den
   Templates beziehungsweise PHP-Klassen hart codiert noch vorhandene
   Übersetzungsdateien für Handbuchinhalte erweitert.
7. Jeder deutsche Handbuchschlüssel besitzt eine englische Entsprechung und
   umgekehrt. Automatisierte Prüfungen erkennen fehlende, zusätzliche und
   unbenutzte Übersetzungsschlüssel.
8. Das Handbuch ist in der jeweils aktiven Sprache durchsuchbar. Die Suche
   berücksichtigt mindestens Kapitel, Überschriften, Konfigurationen,
   Funktionsnamen und Fließtexte, zeigt passende Fundstellen mit Kapitelpfad
   an und verlinkt direkt auf den betreffenden Abschnitt.
9. Die Suche liefert bei leerer Abfrage, unbekannten Begriffen und
   Sonderzeichen ein definiertes, lokalisiertes Ergebnis und verhindert die
   Ausgabe nicht vertrauenswürdiger HTML-Inhalte.
10. Handbuchseiten, Sidebar-Link und Suche sind für angemeldete Benutzer mit
    einer dafür vorgesehenen Berechtigung erreichbar. Mandanten- und
    Sprachkontext werden eingehalten.
11. Eine nachvollziehbare Abdeckungsmatrix ordnet jede produktive
    Benutzeroberfläche und jede administrierbare Konfiguration genau einem
    Handbuchkapitel zu. Nicht dokumentierte Funktionen oder verwaiste Kapitel
    lassen die automatisierte Vollständigkeitsprüfung fehlschlagen.
12. Routing, Rendering, Navigation, Sprachumschaltung, Suche,
    Übersetzungsparität und exemplarische Kapitelabdeckung sind automatisiert
    getestet. Die technische Dokumentation erklärt Struktur, Pflege,
    Suchindex und den späteren Austausch der Platzhalter durch Screenshots.

## Abhängigkeiten

- bestehende fachliche WebWMS-Module und deren produktive Ansichten;
- bestehende Sidebar-, Berechtigungs- und Lokalisierungsstruktur;
- WEBWMS-072 Volltextsuche als technische Grundlage, soweit sinnvoll
  wiederverwendbar.

## Implementierungsstand

**Status:** Done

Der Handbuch-Slice vom 01.10.2026 stellt zwölf fachlich gegliederte Kapitel,
die vollständige deutsche und englische Laufzeitquelle in den separaten
Dateien `translations/handbook.de.yaml` und `translations/handbook.en.yaml`,
rein strukturierte Twig- und Sidebar-Integration ohne Markdown-Rendering,
abschnittsgenaue Suche, Breadcrumbs,
Kapitel-Navigation, die Berechtigung `documentation.handbook.read` sowie
automatisierte Abdeckungs-, Paritäts-, Such- und Renderingtests bereit.

Die maschinenlesbare Abdeckungsmatrix ordnet alle 226 produktiven V3-Webrouten
genau einem Kapitel zu und lässt die CI bei neuen, nicht dokumentierten Routen
oder verwaisten Kapiteln fehlschlagen. Alle 53 Bedien- und
Konfigurationsabschnitte dokumentieren zusätzlich Voraussetzungen,
Berechtigungen, Eingaben, Statusauswirkungen und Fehlerbehandlung und verwenden
zunächst die zentrale, barrierearm beschriftete Grafik
`public/assets/images/handbook/placeholder.svg`. Die technische Dokumentation
beschreibt den späteren abschnittsweisen Austausch durch echte Screenshots.
