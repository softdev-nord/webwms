# Stichtagsinventur durchführen

## Inventur anlegen

Wähle Lager, Inventurcode und den Lagerplatzbereich. Der Bereich wird über einen Lagerplatzpräfix eingeschränkt, beispielsweise `A-`. WebWMS übernimmt alle vorhandenen Bestände dieses Bereichs als Stichtags-Snapshot.

Für denselben Lagerbereich kann nicht gleichzeitig eine zweite unfertige Inventur angelegt werden.

## Bestände zählen

Erfasse für jede Inventurposition die tatsächlich gezählte Menge. Die Sollmenge bleibt während der Zählung verborgen, damit eine Blindzählung möglich ist. Mengen dürfen nicht negativ sein.

Eine Position kann vor dem Einreichen korrigiert und erneut gespeichert werden. WebWMS protokolliert den letzten Zähler und den Zeitpunkt.

## Zur Prüfung einreichen

Die Inventur kann erst eingereicht werden, wenn jede Position gezählt wurde. Danach zeigt der Status `counted`, dass die Differenzprüfung und Freigabe ausstehen.

## Differenzen freigeben

Die Freigabe muss durch eine andere Person als die Einreichung erfolgen. WebWMS prüft vor der Buchung, ob sich ein betroffener Bestand seit dem Stichtags-Snapshot verändert hat. Bei einer Veränderung wird nichts gebucht und die Inventur muss fachlich geklärt werden.

Bei erfolgreicher Freigabe bucht WebWMS alle Differenzen gemeinsam. Jede Korrektur erscheint mit Benutzer, Zeitpunkt und Inventurreferenz in der Bewegungshistorie. Schlägt eine einzelne Position fehl, wird die gesamte Freigabe zurückgerollt.
