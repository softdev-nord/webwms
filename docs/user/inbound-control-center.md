# Wareneingangsleitstand

Unter **Wareneingang → Wareneingangsleitstand** bearbeiten Sie den vollständigen Eingang vom Einkaufsbeleg bis zur Bestandsbereitstellung. Alle Daten werden innerhalb des angemeldeten Mandanten gespeichert; schreibende Aktionen protokollieren Benutzer und Zeitpunkt.

## Bestellung und Avis

1. Erfassen Sie unter **Bestellung & Avis** die Bestellnummer, Lieferantenreferenz, den Artikel und die Sollmenge.
2. Wählen Sie anschließend eine offene Bestellposition, hinterlegen Sie Avisnummer, Lieferschein, erwarteten Termin und avisierte Menge.
3. Wechseln Sie für die tatsächliche Annahme zu **Operative Annahme**. Dort dokumentieren Sie Istmenge und Abweichungsgrund.

WebWMS verhindert Überavisierung, doppelte Annahme und unzulässige Statuswechsel. Unter- und Überlieferungen werden als eigener Abweichungsvorgang geführt.

## Qualitätsprüfung und Nachweise

Unter **QS & Nachweise** können berechtigte Benutzer mandantenspezifische Checklisten anlegen. Jede Zeile im Fragenfeld wird ein Prüfpunkt. Fotos, Lieferscheine, PDF-Dateien und andere Nachweise lassen sich einer geplanten oder ungeplanten Annahme, einer Retoure oder einem Produktionszugang zuordnen. Dateien sind auf 10 MB begrenzt, erhalten eine SHA-256-Prüfsumme und werden nur im aktuellen Mandanten ausgeliefert.

Die operative QS unter **Geplante Eingänge** bucht einwandfreie Ware als verfügbar. Beschädigte oder abweichende Ware geht in Sperrbestand und muss ausdrücklich freigegeben oder abgelehnt werden.

## Bereitstellung

- **Etikett anfordern:** Erstellt einen wartenden Druckauftrag für Wareneingangs-, Artikel- oder Ladeeinheitenetiketten.
- **Cross-Docking:** Ordnet einen geprüften Eingang demselben Artikel eines offenen Ausgangsauftrags zu. Menge, Artikelidentität und Mandant werden vor der Bereitstellung geprüft.
- **Zugang aus Produktion:** Bucht eine Fertigmeldung mit Fertigungsauftrag, Artikel, Annahmeplatz, Menge und optionaler Charge direkt über den revisionssicheren Bestandsledger.
- **Einlagerung:** Die operative Annahme ermittelt über die Einlagerungsstrategie einen geeigneten Zielplatz und bestätigt anschließend die atomare Umlagerung.

## Retouren

Unter **Retouren** kündigen Sie die erwartete Retoure mit ursprünglicher Auftragsreferenz und Retourengrund an. Nach **Vereinnahmen** folgt die Prüfung. Als Entscheidung stehen Wiedereinlagerung und Sperrbestand zur Verfügung. Die Prüfung speichert Notiz, Prüfer und Zeitpunkt.

## API

Der Leitstand ist außerdem unter `GET /api/v3/inbound/control` lesbar. Schreibend stehen folgende Endpunkte zur Verfügung:

- `POST /api/v3/inbound/control/purchase-orders`
- `POST /api/v3/inbound/control/deliveries`
- `POST /api/v3/inbound/control/returns`
- `POST /api/v3/inbound/control/returns/{orderId}/items/{itemId}/receive`
- `POST /api/v3/inbound/control/returns/receipts/{receiptId}/inspect`
- `POST /api/v3/inbound/control/checklists`
- `POST /api/v3/inbound/control/attachments` (Dateiinhalt Base64-kodiert)
- `POST /api/v3/inbound/control/labels`
- `POST /api/v3/inbound/control/cross-dock`
- `POST /api/v3/inbound/control/production`

Für Annahme, QS, Abweichungsentscheidung und Einlagerung bleiben die Endpunkte unter `/api/v3/inbound/planned` maßgeblich. Ungeplante Eingänge werden unter `/api/v3/unplanned-receipts` verarbeitet.
