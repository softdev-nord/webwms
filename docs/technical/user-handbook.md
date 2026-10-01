# Integriertes Benutzerhandbuch

## Laufzeitstruktur

Das Benutzerhandbuch wird unter `/v3/help` innerhalb des bestehenden
V3-Layouts gerendert. `UserDocumentationService` definiert ausschließlich die
stabilen Kapitel-Slugs und ihre Reihenfolge. Alle sichtbaren Inhalte und ihre
Struktur liegen in den eigenständigen Dateien `translations/handbook.de.yaml`
und `translations/handbook.en.yaml` im Translation-Domain `handbook`.

Die Suche arbeitet in der aktiven Sprache über Titel, Zusammenfassung und den
vollständigen Kapiteltext. Ein Treffer enthält den Kapitelpfad, einen Auszug
und den stabilen Anker des betroffenen Abschnitts. Twig rendert die
strukturierten Überschriften, Absätze, Schritte, Aufzählungen und Bilder direkt;
ein Markdown-Parser oder ungefiltertes `raw`-HTML wird nicht verwendet.

## Kapitel- und Funktionsabdeckung

| Produktbereich | Handbuchkapitel |
| --- | --- |
| Anmeldung, Dashboard, Sprache und Arbeitskontext | Einstieg und Arbeitsumgebung |
| Sidebar, Übersichten, Suche, Filter, Pagination und Formulare | Navigation, Übersichten, Suche und Bearbeitung |
| Benutzer, Rollen, Rechte, API-Clients und OIDC | Benutzer, Rollen, Rechte und Single Sign-on |
| Mandanten, Standorte, Nummernkreise, Stammdaten und Strategien | Mandanten-, Standort- und Systemkonfiguration |
| Topologie, Bestand, Dimensionen, Bewegungen, Sperren und Rückverfolgung | Lagertopologie, Bestand und Rückverfolgung |
| Stichtag, permanent, Nulldurchgang, Zählung und Differenzen | Inventur und Bestandskontrolle |
| Bestellung, Avis, Zugang, QS, Abweichung, Putaway und Nachschub | Wareneingang und Einlagerung |
| Auftrag, Allokation, Pick, Pack, Versand, Verladung und Retoure | Auftrag, Allokation, Kommissionierung, Packen und Versand |
| ERP, Carrier, Druck, Scanner, Messung, WCS und Geräte | ERP, Carrier, Drucker, Scanner, Messgeräte und WCS |
| Shopfloor, KPI, Portal, Events, Lagergeld, VAS und Erweiterungen | Plattformfunktionen und erweiterte Module |
| OpenAPI, API-Clients, Outbox, Adapter, Worker und Scheduler | API, Outbox, Transportadapter und Automatisierung |
| Validierung, Berechtigungen, Integration, Audit und Support | Fehlerbehebung und Support |

Die Tests prüfen, dass jeder im Katalog registrierte Eintrag in beiden Sprachen
existiert, alle Kapitel eine strukturierte Abschnittsliste und einen
Screenshot-Verweis besitzen und die Schlüsselmengen beider YAML-Dateien
identisch sind.

`HandbookCoverageMap` ist die maschinenlesbare Abdeckungsmatrix. Sie ordnet
jede produktive Route unter `/v3` genau einem der zwölf Kapitel zu. Die
Zuordnung unterscheidet unter anderem Benutzerverwaltung und
Systemkonfiguration, Lagerbetrieb und Inventur sowie Integrationsbetrieb und
Fehlerbehebung. Der Integrationstest
`HandbookCoverageTest::testEveryV3WebRouteIsAssignedToExactlyOneChapter`
liest die echte Symfony-Route-Collection. Eine neue V3-Route ohne Zuordnung
oder ein Kapitel ohne mindestens eine produktive Route lässt die CI
fehlschlagen. Zum Zeitpunkt dieses Abschlusses sind 226 Web-Routen abgedeckt.

Jedes Kapitel enthält strukturierte Hinweise zu Voraussetzungen,
Berechtigungen, Eingaben, Statusauswirkungen und typischen Fehlern. Sämtliche
53 Bedien- und Konfigurationsabschnitte besitzen eine eigene, lokalisierte
Bildbeschreibung und eine stabile Platzhalterreferenz.

## Screenshots ersetzen

Im ersten Schritt referenzieren alle Bedien- und Konfigurationsabschnitte die zentrale Grafik
`public/assets/images/handbook/placeholder.svg`. Für den Austausch wird die
`image.src` im jeweiligen YAML-Abschnitt auf eine fachlich benannte Datei im
selben Verzeichnis geändert, beispielsweise
`/assets/images/handbook/inventory/stock-overview.webp`. Alternativtext und
Bildunterschrift bleiben pro Verwendung lokalisiert. Der Content-Service lässt
aus Sicherheits- und Datenschutzgründen ausschließlich lokale Bildpfade unter
dem Handbuchverzeichnis zu.

## Berechtigung und Pflege

Der Zugriff benötigt `documentation.handbook.read`. Die Migration
`Version20261001110000` ergänzt das Recht für bestehende Rollen, damit der
bisherige Hilfebereich nach dem Update erreichbar bleibt. Bei neuen Rollen
wird es explizit ausgewählt. Inhaltliche Änderungen müssen immer parallel in
Deutsch und Englisch erfolgen. Die Tests verhindern unvollständige
Übersetzungen, unbekannte Strukturschlüssel und nicht gerenderte zusätzliche
Inhaltsbereiche. Weitere Integrationstests prüfen Routengenerierung,
Berechtigungsattribute, lokalisierte Navigation, Twig-Rendering,
HTML-Escaping, Suchgrenzfälle und die Kapitel-Untermenüs der Sidebar.
