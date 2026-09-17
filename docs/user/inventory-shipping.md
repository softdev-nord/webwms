# Sendungen vorbereiten und übergeben

## Voraussetzungen

Eine Sendung kann erst angelegt werden, wenn der zugehörige Packauftrag
vollständig abgeschlossen ist. Pro Packauftrag ist genau eine Sendung möglich.

## Sendung anlegen

Erfassen Sie:

- eine eindeutige Sendungsnummer;
- den Carrier, zum Beispiel `DHL`;
- den gebuchten Service, zum Beispiel `PARCEL`.

Die Sendung erhält zunächst den Status **vorbereitet** (`prepared`).

## Versandlabel registrieren

Speichern Sie die vom Carrier vergebene Trackingnummer und die Referenz auf das
Versandlabel. Nach erfolgreicher Registrierung steht die Sendung auf
**etikettiert** (`labelled`). Tracking und Label können nicht versehentlich ein
zweites Mal für dieselbe Sendung registriert werden.

## Carrier-Übergabe buchen

Nach der physischen Übergabe geben Sie die Übergabereferenz an, etwa eine
Manifest-, Tour- oder Abholnummer. WebWMS protokolliert Benutzer und Zeitpunkt
und setzt den Status auf **versendet** (`dispatched`). Dieser Status ist im
aktuellen Prozess endgültig.

## Fehler vermeiden

- Verwenden Sie keine bereits belegte Sendungs- oder Trackingnummer.
- Buchen Sie die Übergabe erst, wenn das Label registriert ist.
- Bewahren Sie das eigentliche Label im angebundenen Dokument- oder
  Carrier-System auf; WebWMS speichert in diesem Slice dessen Referenz.
