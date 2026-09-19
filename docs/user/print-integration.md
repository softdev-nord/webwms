# Druckaufträge ausführen

1. Registrieren Sie einen aktiven Drucker mit HTTPS-Endpunkt und Credential-
   Referenz.
2. Legen Sie einen Druckauftrag für eine Dokumentreferenz an. Dieselbe
   `requestId` liefert denselben Auftrag und verhindert Doppeldruck.
3. Starten Sie den Auftrag. Erfolgreiche Aufträge erhalten den Status `printed`
   und die Referenz des Print-Gateways.
4. Beheben Sie bei `failed` die angezeigte Ursache und starten Sie denselben
   Auftrag erneut. Die Anzahl der Versuche bleibt nachvollziehbar.
