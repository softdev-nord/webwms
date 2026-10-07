---
id: WEBWMS-113
issue_type: Story
epic: WEBWMS-EPIC-PLATFORM
status: Offen
priority: Medium
story_points: 13
component: "Benutzerhandbuch"
feature_group: "Dokumentation & Hilfe"
source_feature: WEBWMS-REQ-113
---

# WEBWMS-113: Handbuch-Screenshots automatisiert erzeugen

## User Story

Als Dokumentationsverantwortlicher möchte ich die Screenshot-Platzhalter des
Benutzerhandbuchs über ein bewusst gestartetes Symfony-Command durch aktuelle,
reproduzierbare Screenshots ersetzen, damit das Handbuch die tatsächliche
Benutzeroberfläche zeigt, ohne Screenshots manuell aufnehmen und zuschneiden zu
müssen.

## Ausgangslage

[WEBWMS-111](WEBWMS-111.md) und [WEBWMS-112](WEBWMS-112.md) stellen das
zweisprachige, durchsuchbare Benutzerhandbuch und die vollständige
View-/Formularabdeckung bereit. Die Bildreferenzen sind bereits stabil und
viewbezogen, verwenden jedoch noch Platzhaltergrafiken.

Für die Browserautomatisierung soll
[`playwright-php/playwright-symfony`](https://github.com/playwright-php/playwright-symfony)
als Development-Abhängigkeit eingesetzt werden. Das Paket verbindet einen
echten Playwright-Browser mit dem Symfony-Kernel, stellt den Test-Container zur
Verfügung und unterstützt die Anmeldung eines Testbenutzers ohne Prüfung des
Login-Formulars. Da sich die Bibliothek vor Version 1.0 befindet, muss ihre
Verwendung hinter einer kleinen projektinternen Abstraktion gekapselt werden.

Die Screenshot-Erzeugung ist ausdrücklich **kein Bestandteil der GitHub
Actions**. Sie wird ausschließlich lokal beziehungsweise in einer bewusst
vorbereiteten Laufzeitumgebung über ein Symfony-Command gestartet. Das Command
führt weder Git-Commits noch Pushes aus.

## Fachlicher Umfang

### Manuelles Command

Folgendes Command wird bereitgestellt:

```bash
bin/console webwms:handbook:capture-screenshots
```

Sinnvolle Optionen:

```bash
bin/console webwms:handbook:capture-screenshots \
    --locale=de \
    --view=product_form \
    --force
```

| Option | Bedeutung |
| --- | --- |
| `--locale=de|en|all` | Erzeugt Bilder in einer oder beiden Handbuchsprachen. |
| `--view=<documentation-key>` | Begrenzt die Aufnahme auf eine dokumentierte View; mehrfach verwendbar. |
| `--category=<category>` | Begrenzt die Aufnahme auf einen Fachbereich. |
| `--force` | Überschreibt bereits vorhandene echte Screenshots. |
| `--dry-run` | Prüft Szenarien, Routen, Datenreferenzen und Zielpfade ohne Browseraufnahme. |
| `--headed` | Öffnet den Browser sichtbar zur Fehlersuche. |

Das Command zeigt pro Szenario Status, Route, Zielpfad und Ergebnis und endet
bei unvollständiger Erzeugung mit einem von null abweichenden Exit-Code.

### Screenshot-Szenarien

Die Aufnahmeparameter werden in einer eigenen, versionierten Konfiguration
verwaltet, beispielsweise `config/handbook/screenshots.yaml`. Die Konfiguration
enthält keine übersetzbaren Handbuchtexte, sondern ausschließlich technische
Aufnahmeszenarien.

```yaml
handbook_screenshots:
    product_form:
        route: v3_product_new
        wait_for: 'form'
        output: product/form.png

    warehouse_occupancy_block:
        route: v3_inventory_occupancy
        query:
            warehouse: '@demo.warehouse.default'
            aisle: '@demo.aisle.block_101'
        wait_for: '.warehouse-layout--block'
        output: warehouse/occupancy-block.png
```

Ein Szenario kann mindestens folgende Angaben besitzen:

- Dokumentationskennung und Symfony-Route;
- Route- und Query-Parameter mit stabilen Demodaten-Aliasen;
- Sprache, Viewport und Zielpfad;
- Selektor, auf den vor der Aufnahme gewartet wird;
- optional auszuführende Klick-, Eingabe-, Tab- oder Scroll-Aktionen;
- auszublendende oder zu maskierende Elemente;
- Aufnahme der vollständigen Seite oder eines definierten Bereichs;
- fachlich begründete Abweichungen vom Standardszenario.

### Reproduzierbare Aufnahme

Vor jeder Aufnahme stellt der Runner einen stabilen Zustand her:

1. ein dedizierter Dokumentationsbenutzer wird über den Symfony-Testkontext
   angemeldet;
2. Sprache, Mandant und Standort werden entsprechend dem Szenario gesetzt;
3. definierte Demodaten-Aliase werden in echte IDs und Routenparameter
   aufgelöst;
4. Browsergröße, Farbschema und Geräteskalierung sind fest vorgegeben;
5. Schriftarten, relevante Requests und der konfigurierte Zielselektor sind
   vollständig geladen;
6. Animationen, Übergänge, Caret, Toasts und störende Fokuszustände werden
   deaktiviert;
7. Geheimnisse, personenbezogene Daten und volatile technische Werte werden
   maskiert;
8. der Screenshot wird atomar am bereits im Handbuch vorgesehenen Zielpfad
   gespeichert.

Die Erzeugung muss mit der Installationsroutine und deren deterministischen
Demodaten funktionieren. Sie darf keine produktive Datenbank voraussetzen oder
verändern.

### Handbuchintegration

Die deutschen und englischen Handbuchkataloge referenzieren dauerhafte,
viewbezogene Bildpfade. Solange die PNG-Datei fehlt, wird weiterhin die
Platzhaltergrafik angezeigt. Nach erfolgreicher Ausführung des Commands wird
am gleichen fachlichen Bildziel der echte Screenshot ausgeliefert, ohne dass
das Command YAML-Dateien umschreiben muss.

Für unterschiedliche fachliche Darstellungen derselben View dürfen mehrere
Bilder zugeordnet werden. Insbesondere die grafische Lagerbelegung benötigt
getrennte Szenarien für mindestens:

- Blocklager als Draufsicht;
- Fachboden- beziehungsweise Hochregallager als Frontansicht;
- Durchlauf- oder mehrfachtiefes Lager mit Tiefenkanälen.

### Technische Abgrenzung

- keine Ausführung in GitHub Actions;
- kein automatischer Commit, Push oder Pull Request;
- kein Zugriff auf Produktivdaten;
- keine Aufnahme echter Zugangsdaten oder Secrets;
- keine Ablage von Binärbildern außerhalb der vorgesehenen
  Handbuch-Assetstruktur;
- bestehende Abdeckungstests dürfen das Vorhandensein stabiler Bildreferenzen
  weiterhin prüfen, müssen aber lokale Platzhalter als zulässigen
  Ausgangszustand behandeln.

## Akzeptanzkriterien

1. `playwright-php/playwright-symfony` ist als Development-Abhängigkeit
   eingebunden, für die Test-/Dokumentationsumgebung konfiguriert und die
   benötigten Browser können über den dokumentierten Installationsbefehl
   installiert werden.
2. Das manuell gestartete Command
   `webwms:handbook:capture-screenshots` erzeugt ohne GitHub Action und ohne
   Git-Operationen echte Browser-Screenshots.
3. Das Command unterstützt mindestens Sprache, einzelne View, Fachbereich,
   Dry-Run, Überschreiben und sichtbaren Browserbetrieb als Optionen.
4. Sämtliche Aufnahmeszenarien liegen in einer zentralen, verständlich
   strukturierten und validierten Konfiguration.
5. Routenparameter und fachliche Datensätze werden über stabile Demodaten-
   Aliase aufgelöst; UUIDs oder datenbankabhängige IDs sind nicht hart codiert.
6. Ein dedizierter Dokumentationsbenutzer wird ohne Klartext-Zugangsdaten
   authentifiziert und besitzt ausschließlich die für die Aufnahmen benötigten
   Berechtigungen.
7. Viewport, Skalierung, Sprache, Farbschema, Schriften, Animationen und
   dynamische Oberflächenelemente werden reproduzierbar vorbereitet.
8. Bestehende Bilder werden ohne `--force` nicht überschrieben. Die Ausgabe
   nennt übersprungene, erzeugte und fehlgeschlagene Szenarien.
9. Fehlende Routen, Selektoren, Demodaten, Zielverzeichnisse oder unzulässige
   Pfade liefern eine verständliche Fehlermeldung und einen fehlerhaften
   Exit-Code.
10. Die Zielpfade befinden sich ausschließlich unter
    `public/assets/images/handbook/`; Path Traversal wird verhindert und Dateien
    werden atomar geschrieben.
11. Das Command maskiert konfigurierte Secrets, Tokens, personenbezogene und
    volatile Werte vor der Aufnahme.
12. Für Artikelübersicht, Artikelanlage/-bearbeitung und Artikeldetail werden
    repräsentative echte Screenshots erzeugt.
13. Für Lagertopologie, Topologieformular, Rastergenerator, CSV-Import sowie
    Block-, Regal- und Durchlauflagerbelegung werden repräsentative echte
    Screenshots erzeugt.
14. Eine Validierung vergleicht Handbuch-Bildreferenzen und Szenarien, meldet
    nicht abgedeckte Views, verwaiste Szenarien sowie fehlende Bilddateien und
    kann separat über den Dry-Run ausgeführt werden.
15. Unit- und Integrationstests decken Konfigurationsvalidierung,
    Aliasauflösung, Pfadsicherheit, Filteroptionen, Fehlercodes und mindestens
    eine repräsentative Browseraufnahme ab.
16. Eine Entwicklerdokumentation beschreibt Voraussetzungen, Browser-
    Installation, Demodatenaufbau, vollständige und selektive Ausführung,
    Fehlersuche sowie das bewusste Übernehmen der erzeugten Bilder in Git.

## Vorgeschlagene technische Struktur

```text
src/Documentation/
├── Application/Screenshot/
│   ├── CaptureHandbookScreenshots.php
│   ├── ScreenshotScenario.php
│   └── ScreenshotScenarioCollection.php
├── Infrastructure/Screenshot/
│   ├── PlaywrightScreenshotRunner.php
│   ├── ScreenshotConfigurationLoader.php
│   └── DemoReferenceResolver.php
└── UI/Console/
    └── CaptureHandbookScreenshotsCommand.php

config/handbook/screenshots.yaml
tests/Documentation/Screenshot/
```

Die Integration mit dem noch nicht stabilen Package-API bleibt auf
`PlaywrightScreenshotRunner` begrenzt. Fachliche Szenarien, Validierung und
Command-Orchestrierung bleiben dadurch unabhängig von konkreten
Bibliotheksdetails testbar.

## Abhängigkeiten

- [WEBWMS-111](WEBWMS-111.md): technische Handbuchplattform;
- [WEBWMS-112](WEBWMS-112.md): vollständige View-, Formular- und Bildmatrix;
- Installationsroutine und deterministische V3-Demodaten;
- Node.js 20+, installierte Playwright-Browser sowie PHP 8.2+;
- `playwright-php/playwright-symfony` als Development-Abhängigkeit.

## Definition of Done

- alle Akzeptanzkriterien sind automatisiert oder reproduzierbar nachgewiesen;
- das Command funktioniert mit einer frisch installierten Demo-Umgebung;
- Artikel-Handling und alle Varianten der Lagertopologie besitzen echte
  Screenshots;
- die Bedienung des Commands ist technisch dokumentiert;
- es existiert keine GitHub-Action für die Screenshot-Erzeugung;
- Roadmap, Platform-Epic und Ticketstatus sind synchronisiert.

