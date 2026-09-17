# Access Control Configuration (T6.2)

## Rollen-Hierarchie

```yaml
# config/packages/security.yaml
security:
    role_hierarchy:
        ROLE_SUPER_ADMIN: [ROLE_ADMIN, ROLE_USER]
        ROLE_ADMIN: [ROLE_MANAGER, ROLE_USER]
        ROLE_MANAGER: [ROLE_USER]
```

## Prozess-spezifische Rollen

| Rolle | Beschreibung | Prozesse |
|-------|------|----------|
| ROLE_WARENEINGANG | Wareneingang | SI101–SI110 |
| ROLE_AUSLAGERUNG | Auslagerung | SO101–SO110 |
| ROLE_INVENTUR | Inventur | Inventory Start/Count/Complete |
| ROLE_ADMIN | System-Admin | Alle Config/Benutzer |

## Endpoint-Schutz

```php
#[RequireRole('ROLE_ADMIN')]
#[Route('/artikel_löschen/{id}', name: 'delete_article')]
public function deleteArticle(int $id): Response {}

#[RequireRole('ROLE_WARENEINGANG')]
#[Route('/stock_in_final', name: 'stock_in_final')]
public function stockInFinal(Request $request): JsonResponse {}
```

## Current Status

- ✅ RequireRole Attribute definiert
- ✅ Voter-Klasse angelegt
- ⏳ EventListener für Enforcement (für nächste Iteration)
- ⏳ Security-Tests schreiben

