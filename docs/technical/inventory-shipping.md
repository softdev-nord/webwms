# Versandprozess

## Modell und Zustände

Eine Sendung referenziert genau einen abgeschlossenen Packauftrag. Sie speichert
eine mandantenweit eindeutige Sendungsnummer, Carrier und Service sowie die
Auditdaten jedes Prozessschritts.

| Status | Bedeutung | Zulässiger nächster Schritt |
|---|---|---|
| `prepared` | Sendung für einen abgeschlossenen Packauftrag angelegt | Label registrieren |
| `labelled` | Trackingnummer und Labelreferenz gespeichert | Carrier-Übergabe buchen |
| `dispatched` | Sendung nachweisbar an den Carrier übergeben | Endzustand |

## Transaktionen und Mandantentrennung

Anlage, Labelregistrierung und Übergabe laufen jeweils in einer
Datenbanktransaktion. Vor jedem Zustandswechsel wird die Sendung mit
`SELECT ... FOR UPDATE` gesperrt. Packauftrag, Sendung und ausführender Benutzer
müssen demselben Mandanten angehören. Dadurch sind parallele oder wiederholte
Übergänge ausgeschlossen.

Die Labelregistrierung speichert nur Trackingnummer und Labelreferenz. Die
Referenz kann beispielsweise auf einen objektgespeicherten PDF-Datensatz oder
eine Carrier-Dokument-ID zeigen; Labeldateien werden nicht in der
Transaktionstabelle abgelegt.

## Anwendung und Persistenz

- `CreateShipmentHandler`: legt die Sendung im Status `prepared` an;
- `RegisterShipmentLabelHandler`: registriert Tracking und Label;
- `DispatchShipmentHandler`: erfasst Übergabereferenz, Benutzer und Zeitpunkt;
- `wms_shipment`: Prozessdaten und vollständiger Zustandsaudit;
- Migration `Version20260917140000`.

Pro Packauftrag ist nur eine Sendung zulässig. Sendungsnummern sind pro Mandant
eindeutig; Trackingnummern sind pro Mandant und Carrier eindeutig.

## Bekannte Grenzen

Carrier-API, Erzeugung und Speicherung der Labeldatei, Mehrpaketsendungen,
Storno, Manifest/Ladeliste, Zustellstatus und ERP-Rückmeldung sind noch nicht
Bestandteil dieses Slices. Der integrationsneutrale Kern stellt dafür stabile
Referenzen und Zustände bereit.
