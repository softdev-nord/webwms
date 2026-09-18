# Packaufträge und Packstücke

## Packauftrag anlegen

Ein Packauftrag wird aus einer vollständig bearbeiteten Pickliste erzeugt. Pro
Pickliste kann nur ein Packauftrag existieren.

## Packstücke erfassen

Für jedes Packstück geben Sie an:

- eine eindeutige Packstücknummer;
- das gemessene Gewicht in Gramm;
- die enthaltenen erfolgreich gepickten Positionen.

Eine Position kann nicht versehentlich in zwei Packstücke gelegt werden. Nach
dem Erfassen gilt das Packstück als versiegelt.

## Abschlussprüfung

WebWMS schließt den Packauftrag nur ab, wenn jede erfolgreich gepickte Position
genau einmal verpackt ist und mindestens ein versiegeltes Packstück existiert.
Fehlmengen aus der Pickliste werden bei der Vollständigkeitsprüfung ignoriert.

Der Abschluss liefert Anzahl und Gesamtgewicht aller Packstücke und bereitet
damit den späteren Versandprozess vor.

Für Integrationen steht der Ablauf auch über die
[API v3](packing-api.md) zur Verfügung.
