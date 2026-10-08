# Handbuch-Screenshots erzeugen

WEBWMS-113 ersetzt die Platzhalter des integrierten Benutzerhandbuchs bei
Bedarf durch reproduzierbare Browseraufnahmen. Die Erzeugung läuft bewusst nur
über ein lokal gestartetes Symfony-Command. Es existiert keine GitHub Action
und das Command führt keine Git-Operationen aus.

## Voraussetzungen

```bash
composer install
vendor/bin/playwright-install --browsers
```

Benötigt werden PHP 8.4, Node.js 20 oder neuer, die installierten
Playwright-Browser, eine laufende WEBWMS-Installation und deterministische
Demodaten. `playwright-php/playwright-symfony` ist nur als Development-
Abhängigkeit eingebunden.

Für die Aufnahme wird ein eigener Benutzer mit den benötigten Leserechten und
den gezielt erforderlichen Schreibrechten verwendet. Zugangsdaten werden nicht
in der Szenario-Konfiguration gespeichert. Sie können beispielsweise in der
lokalen, nicht versionierten `.env.local` hinterlegt werden:

```dotenv
HANDBOOK_SCREENSHOT_BASE_URL='http://127.0.0.1'
HANDBOOK_SCREENSHOT_EMAIL='documentation@example.org'
HANDBOOK_SCREENSHOT_PASSWORD='…'
```

Alternativ können die drei Werte als echte Prozess-Umgebungsvariablen gesetzt
werden. Symfony übergibt sie in beiden Fällen über den Service-Container an den
Screenshot-Runner.

Die Basis-URL bezeichnet ausschließlich Schema, Host und gegebenenfalls Port;
sie darf nicht mit `/v3` enden. Beim mitgelieferten Docker-Setup läuft Browser
und Apache im selben PHP-Container, weshalb `http://127.0.0.1` verwendet wird.
Ein nur auf dem Docker-Host definierter Name wie `www.webwms.local` ist aus dem
Container nicht automatisch auflösbar.

Das Passwort darf nicht als Command-Option übergeben werden, damit es weder in
der Shell-Historie noch in Prozesslisten erscheint.

## Konfiguration prüfen

Der Dry-Run startet keinen Browser und schreibt keine Dateien. Er validiert
Szenarien, Filter, Demodatenreferenzen und sichere Zielpfade:

```bash
bin/console webwms:handbook:capture-screenshots --dry-run --locale=all
```

Fehlende Demodaten werden mit dem betroffenen Alias ausgegeben. Die
Installationsroutine mit Demodaten stellt die für die Standardszenarien
erforderlichen Artikel-, Lager- und Topologiedaten bereit.

## Screenshots erzeugen

Alle deutschen Aufnahmen:

```bash
bin/console webwms:handbook:capture-screenshots
```

Beide Sprachen und vorhandene Aufnahmen ersetzen:

```bash
bin/console webwms:handbook:capture-screenshots --locale=all --force
```

Einzelne View oder Fachbereich aufnehmen:

```bash
bin/console webwms:handbook:capture-screenshots --view=product_form --force
bin/console webwms:handbook:capture-screenshots --category=warehouse --force
```

Browser zur Fehlersuche sichtbar öffnen:

```bash
bin/console webwms:handbook:capture-screenshots --view=warehouse_occupancy_block --headed --force
```

Die deutschen Dateien enden auf `.png`, englische Aufnahmen auf `.en.png`.
Alle Dateien werden atomar unter
`public/assets/images/handbook/screenshots/` geschrieben. Vorhandene Dateien
werden nur mit `--force` ersetzt.

## Szenarien pflegen

Technische Aufnahmeparameter befinden sich in
`config/handbook/screenshots.yaml`. Ein Szenario definiert Route,
Demodatenreferenzen, Zielpfad, Warte-Selektor, Viewport und zu maskierende
Elemente. Übersetzbare Handbuchinhalte bleiben ausschließlich in
`handbook.de.yaml` und `handbook.en.yaml`.

Unterstützte Standardreferenzen:

| Alias | Auswahl |
| --- | --- |
| `@demo.product.default` | erster aktiver Demoartikel |
| `@demo.warehouse.default` | erstes Lager nach Code |
| `@demo.warehouse.block` | Lager der ersten Blocklagerstruktur |
| `@demo.warehouse.rack` | Lager der ersten Regal-/Hochregalstruktur |
| `@demo.warehouse.flow` | Lager der ersten Durchlaufstruktur |
| `@demo.aisle.block` | erste Blocklagerstruktur |
| `@demo.aisle.rack` | erste Regal-/Hochregalstruktur |
| `@demo.aisle.flow` | erste Durchlaufstruktur |

Die Aufnahmen für Block-, Regal- und Durchlauflager sind getrennt, weil die
grafische Lagerbelegung jeweils ein anderes physisches Darstellungsmodell
verwendet.

## Stabilität und Datenschutz

Der Runner verwendet einen festen Viewport und ein helles Farbschema, wartet
auf Netzwerkruhe, Webfonts und den Szenario-Selektor und deaktiviert
Animationen, Übergänge, Cursor, Toasts sowie die Symfony-Toolbar. Konfigurierte
sensible Elemente werden vor der Aufnahme unkenntlich gemacht.

Vor dem Commit sind die Bilder fachlich zu prüfen. Das Command entscheidet
nicht selbstständig, ob erzeugte Binärdateien in Git übernommen werden.

## Fehlerbehebung

- `Playwright PHP is not installed`: `composer install` ausführen.
- Node-Prozess endet mit Code 1: Im selben PHP-Container zuerst
  `node --version` prüfen; Playwright benötigt Node.js 20 oder neuer.
- Browser fehlt: `vendor/bin/playwright-install chromium` ausführen.
- Chromium meldet fehlende Linux-Bibliotheken:
  `vendor/bin/playwright-install --with-deps` mit den dafür erforderlichen
  Containerrechten ausführen.
- `ERR_NAME_NOT_RESOLVED`: `HANDBOOK_SCREENSHOT_BASE_URL` auf einen aus dem
  PHP-Container erreichbaren Host setzen; im mitgelieferten Docker-Setup ist
  dies `http://127.0.0.1`.
- Demo-Alias nicht gefunden: Installation mit Demodaten prüfen.
- Warte-Selektor nicht gefunden: View im `--headed`-Modus öffnen und Szenario
  an die aktuelle DOM-Struktur anpassen.
- Anmeldung schlägt fehl: dedizierten Benutzer, Status, Rechte und die drei
  Umgebungsvariablen prüfen.
- vorhandene Datei wird übersprungen: bewusst `--force` verwenden.
