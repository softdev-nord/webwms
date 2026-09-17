# Allokationen freigeben und entnehmen

## Freigeben

Wird ein Auftrag storniert oder soll anderer Bestand verwendet werden, geben
Sie die aktive Allokation frei. Die physische Menge bleibt unverändert und ist
sofort wieder für andere Aufträge verfügbar. Benutzer, Zeitpunkt und Grund
werden gespeichert.

## Entnehmen

Nach erfolgreichem Picking verbrauchen Sie die Allokation. WebWMS:

1. prüft, dass die Allokation noch aktiv ist;
2. sperrt den betroffenen Bestand;
3. reduziert den physischen Bestand;
4. schreibt einen Journal-Eintrag vom Typ `allocation_consumption`;
5. aktualisiert die erfüllte Auftragsmenge.

Ist die komplette Sollmenge entnommen, erhält die Reservierung den Status
`fulfilled`. Ein bereits freigegebener oder verbrauchter Datensatz kann nicht
erneut verarbeitet werden.

## Aktuelle Einschränkung

Eine Allokation wird vollständig freigegeben oder entnommen. Teilentnahmen
innerhalb derselben Allokation sind noch nicht vorgesehen.
