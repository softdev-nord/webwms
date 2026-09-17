# Benutzer, Rollen, Rechte und Symfony Security

## Berechtigungsmodell

Benutzer und Rollen sind mandantenbezogen. Rollen enthalten validierte
Permission-Keys wie `inventory.stock.read`. Die Persistenz ist normalisiert:

- `wms_user_account`;
- `wms_role`;
- `wms_user_role`;
- `wms_role_permission`.

E-Mail-Adressen und Rollen-Codes sind innerhalb eines Mandanten eindeutig.
Eine Rolle eines anderen Mandanten kann keinem Benutzer zugewiesen werden.

## Passwortverarbeitung

`CreateUserHandler` erhält ausschließlich das Klartextpasswort des Use Cases
und übergibt es an den `PasswordHasher`-Port. Der Symfony-Adapter:

- fordert mindestens zwölf Zeichen;
- nutzt den für `SecurityUser` konfigurierten Symfony Password Hasher;
- persistiert ausschließlich den Hash.

## Authentifizierung

Der 3.0-Login ist unter `/v3/login` verfügbar. Der interne Identifier besteht
aus `tenant UUID|email`. `DbalUserProvider` lädt nur aktive Accounts sowie
ihre Rollen und Permissions.

Legacy- und 3.0-Firewall teilen den Kontext `webwms`. Der Chain Provider kann
beide Benutzerklassen aktualisieren. Der Legacy-Login bleibt während der
Migration bestehen.

## Autorisierung

`PermissionVoter` verarbeitet Permission-Keys direkt:

```php
$this->denyAccessUnlessGranted('inventory.stock.read');
```

Ein unbekanntes Nicht-Permission-Attribut wird nicht abgelehnt, sondern vom
Voter mit `ACCESS_ABSTAIN` an andere Voter weitergereicht.

## Migration

`Version20260917101000` erstellt Rollen, Benutzerkonten und die beiden
Zuordnungstabellen.
