# Benutzer, Rollen, Rechte und Anmeldung

## Benutzer anlegen

Ein Benutzer gehört zu genau einem Mandanten. Benötigt werden E-Mail-Adresse,
Anzeigename und ein Passwort mit mindestens zwölf Zeichen. Die E-Mail-Adresse
ist innerhalb eines Mandanten eindeutig.

Passwörter werden niemals im Klartext gespeichert.

## Rollen und Rechte

Eine Rolle bündelt fachliche Rechte. Beispiele:

- `inventory.stock.read`: Bestände anzeigen;
- `inventory.stock.post`: Bestandsbewegungen buchen;
- `fulfillment.pick.execute`: Picks ausführen.

Einem Benutzer können mehrere Rollen desselben Mandanten zugewiesen werden.
Rollen aus anderen Mandanten sind nicht zulässig.

## Anmeldung

Die Anmeldung für WebWMS 3.0 erfolgt unter `/v3/login` mit:

1. Mandanten-UUID;
2. E-Mail-Adresse;
3. Passwort.

Deaktivierte Benutzer können sich nicht anmelden. Unter `/v3/logout` wird die
3.0-Sitzung beendet. Der bisherige Login bleibt während der Übergangsphase
weiterhin verfügbar.
