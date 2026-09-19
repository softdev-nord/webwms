# Druckaufträge ausführen

Im V3-Arbeitsbereich unter `/v3/integration/printing` können berechtigte
Benutzer Drucker verwalten und die letzten 100 Druckaufträge überwachen.
Drucker lassen sich anlegen, pausieren und wieder aktivieren. Wartende oder
fehlgeschlagene Aufträge können aus ihrer Detailansicht manuell ausgeführt
beziehungsweise erneut versucht werden.

Ein allgemeiner Druckauftrag kann direkt im Arbeitsbereich mit Drucker,
Dokumenttyp, Referenz, PDF- oder ZPL-Format und Exemplaranzahl angelegt werden.
Carrier-Labels können außerdem weiterhin direkt aus einer Sendung in die
Druckwarteschlange gestellt werden.

1. Registrieren Sie einen aktiven Drucker mit HTTPS-Endpunkt und Credential-
   Referenz.
2. Legen Sie einen Druckauftrag für eine Dokumentreferenz an. Dieselbe
   `requestId` liefert denselben Auftrag und verhindert Doppeldruck.
3. Starten Sie den Auftrag. Erfolgreiche Aufträge erhalten den Status `printed`
   und die Referenz des Print-Gateways.
4. Beheben Sie bei `failed` die angezeigte Ursache und starten Sie denselben
   Auftrag erneut. Die Anzahl der Versuche bleibt nachvollziehbar.
