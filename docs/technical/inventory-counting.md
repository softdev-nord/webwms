# Stichtagsinventur und Differenzfreigabe

Dieser Slice implementiert den Backendkern der Roadmap-Tickets `WEBWMS-029` und `WEBWMS-032`.

## Prozess

1. Eine Inventur für Lager und Lagerplatzpräfix anlegen.
2. Alle betroffenen Bestandsdimensionen mit ihrem Sollbestand als unveränderlichen Snapshot übernehmen.
3. Jede Position blind zählen und die Istmenge erfassen.
4. Nach vollständiger Zählung die Inventur zur Differenzprüfung einreichen.
5. Eine andere Person gibt die Inventur frei.
6. Jede Differenz wird atomar als Bestandskorrektur gebucht und im Ledger protokolliert.

Die Zustände lauten `open`, `counted` und `completed`. Eine zweite offene oder gezählte Inventur für denselben Lagerbereich wird verhindert.

## Anwendungsfälle

- `CreateInventoryCountHandler` erzeugt Inventurkopf und Snapshotpositionen.
- `RecordInventoryCountHandler` speichert Zählmenge, Differenz, Benutzer und Zeitpunkt.
- `SubmitInventoryCountHandler` verlangt eine Zählung jeder Position und friert die Differenzsumme ein.
- `ApproveInventoryCountHandler` prüft das Vier-Augen-Prinzip und erzeugt die Korrekturbuchungen.

## Konsistenz

Jede Position enthält Artikel, Lagerplatz, Bestandsstatus, Charge, Seriennummer, MHD und den Bestandsschlüssel. Vor der Freigabe wird die aktuelle Balance mit dem eingefrorenen Sollbestand verglichen. Hat sich der Bestand seit dem Snapshot verändert, wird die gesamte Freigabe zurückgerollt.

Negative Istbestände sind ausgeschlossen. Eine Korrektur darf außerdem keine aktive Allokation unterschreiten. Seriennummern behalten die bestehende Mengenregel. Differenzen verwenden den Ledger-Typ `inventory_adjustment` und benötigen jeweils eine eindeutige Ledger-ID.

## Persistenz

Migration `Version20260918120000` ergänzt:

- `wms_inventory_count` für Umfang, Status und Freigabeaudit;
- `wms_inventory_count_line` für Snapshot, Zählergebnis, Differenz und Ledgerreferenz.

Alle Schreibvorgänge laufen transaktional mit Zeilensperren. Die Freigabe protokolliert Einreicher und Freigeber getrennt.

## Bekannte Einschränkungen

- Der erste Slice übernimmt vorhandene Bestandsbalances. Leere Lagerplätze ohne Artikel-Sollbestand sind noch keine Zählpositionen.
- Nachzählung, Ablehnung und Wiederöffnung sind noch nicht enthalten.
- Permanente Inventur und Nulldurchgangsinventur folgen über `WEBWMS-030` und `WEBWMS-031`.
- API/UI und ticketbezogene Autorisierung bleiben für die vollständige Definition of Done offen.
