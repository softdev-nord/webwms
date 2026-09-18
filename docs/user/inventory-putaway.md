# Ware automatisch einlagern

## Strategie einrichten

Legen Sie je Lager und Bestandsstatus mindestens eine Strategie an. Das
Zielplatz-Präfix bestimmt den Lagerbereich, beispielsweise `A-` für alle Plätze
in Zone A. Kleinere Prioritätswerte werden zuerst berücksichtigt.

Lagerplätze können für die automatische Einlagerung aktiviert oder deaktiviert
werden. Zusätzlich lassen sich Platzpriorität und maximale Mengenkapazität
pflegen. Eine Kapazität von null ist unbegrenzt.

## Einlagerungsauftrag erzeugen

Nach abgeschlossener QS erzeugen Sie aus der Wareneingangsposition einen
Einlagerungsauftrag. WebWMS schlägt automatisch einen passenden Zielplatz vor.
Dabei wird vorhandener identischer Bestand bevorzugt, um Bestände zu
konsolidieren.

Wenn kein geeigneter Platz existiert, wird kein Auftrag angelegt. Prüfen Sie in
diesem Fall Strategie, Präfix, Aktivierung und Kapazitäten.

## Einlagerung bestätigen

Transportieren Sie die Ware vom Wareneingangsplatz zum vorgeschlagenen
Zielplatz und bestätigen Sie anschließend den Auftrag. WebWMS verschiebt den
Bestand atomar und protokolliert Benutzer, Zeitpunkt und Transferreferenz.

Charge, Seriennummer, Mindesthaltbarkeitsdatum und Bestandsstatus bleiben bei
der Einlagerung unverändert.
