# Gruppen-, Rollen- ,Rechteverwaltung für Benutzer
This is a simple Symfony implementation for a role and right system.
This bundle gives you a structure to create and manage roles and rights.

## Die Implementierung enthält folgende Bestandteile
* 3 Entitäten `UserGroup, UserRight, UserRole`
* 3 Repositories `UserGroupRepository, UserRightRepository, UserRoleRepository`
* 3 Services `UserGroupService, UserRightService, UserRoleService`
* Eine Klasse um die Daten für Twig bereitzustellen
* Twig-Funktionen für die Überprüfung von `UserRole`, `UserRight` und `UserGroup`
* Twig-Filter für die Überprüfung von `roleHasRight`

Die Entität `User` aus der [Symfony Security Component](https://symfony.com/doc/current/security.html) gibt allen angemeldeten Benutzern die Rolle `ROLE_USER`.<br>

## Usage

### Controller
Prüfung auf ein bestimmtes `UserRight` in einem Controller:
```php
public function __construct(UserRoleRight $userRoleRight)
{
    if (!$userRoleRight->hasUserRight('manage-users'))
    {
        throw $this->createAccessDeniedException('You don\'t have the required permissions.');
    }
}
```
Die Logik kann entweder in den Controllern `__construct()` implementiert werden, <br>
sodass es für jede Route funktioniert, oder in einzelnen Funktionen.<br>

Die `UserRole` funktioniert genau auf die gleiche Weise.<br>
**Ersetze einfach**:
```php
if (!$userRoleRight->hasUserRight('manage-users'))
```
**durch**
```php
if (!$userRoleRight->hasUserRole('SUPER_ADMIN'))
```

### Twig-Template
Diese Implementierung bietet 3 `Twig Funktionen` und 1 `Twig Filter`

Um ein `UserRight` zu prüfen, können Sie die Funktion `has_right('Name des Rechts')` verwenden

Um eine `UserRole` zu prüfen, können Sie die Funktion `has_role('Name der Rolle')` verwenden.

Um eine `UserGroup` zu prüfen, können Sie die Funktion `has_role('Name der Gruppe')` verwenden

Um zu prüfen, ob eine `UserRole` ein `UserRight` hat, können Sie den Filter `UserRole object|roleHasRight('name of right')` verwenden.