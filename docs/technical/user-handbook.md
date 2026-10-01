# Integriertes Benutzerhandbuch

## Laufzeitstruktur

Das Benutzerhandbuch wird unter `/v3/help` innerhalb des bestehenden
V3-Layouts gerendert. `UserDocumentationService` definiert ausschließlich die
stabilen Kapitel-Slugs und ihre Reihenfolge. Alle sichtbaren Inhalte liegen in
den eigenständigen Übersetzungsdateien `translations/handbook.de.yaml` und
`translations/handbook.en.yaml` im Translation-Domain `handbook`.

Die Suche arbeitet in der aktiven Sprache über Titel, Zusammenfassung und den
vollständigen Kapiteltext. Ein Treffer enthält den Kapitelpfad, einen Auszug
und – soweit bestimmbar – den Anker der zuletzt gelesenen Überschrift. Die
Markdown-Ausgabe maskiert HTML und erlaubt Bilder ausschließlich unter
`/assets/images/handbook/`.

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
existiert, alle Kapitel eine Abschnittsstruktur und einen Screenshot-Verweis
besitzen und die Schlüsselmengen beider YAML-Dateien identisch sind.

## Screenshots ersetzen

Im ersten Schritt referenzieren alle Kapitel die zentrale Grafik
`public/assets/images/handbook/placeholder.svg`. Für den Austausch wird die
Referenz im jeweiligen YAML-Kapitel auf eine fachlich benannte Datei im selben
Verzeichnis geändert, beispielsweise
`/assets/images/handbook/inventory/stock-overview.webp`. Alternativtext und
Bildunterschrift bleiben pro Verwendung lokalisiert. Externe Bildquellen werden
vom Renderer aus Sicherheits- und Datenschutzgründen verworfen.

## Berechtigung und Pflege

Der Zugriff benötigt `documentation.handbook.read`. Die Migration
`Version20261001110000` ergänzt das Recht für bestehende Rollen, damit der
bisherige Hilfebereich nach dem Update erreichbar bleibt. Bei neuen Rollen
wird es explizit ausgewählt. Inhaltliche Änderungen müssen immer parallel in
Deutsch und Englisch erfolgen; der Paritätstest verhindert unvollständige
Übersetzungen.
