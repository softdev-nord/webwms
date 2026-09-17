<?php

declare(strict_types=1);

namespace WebWMS\Security\Attribute;

use Attribute;

/**
 * Definiert erforderliche Rollen für einen Controller-Endpoint
 *
 * Beispiel:
 * #[RequireRole('ROLE_ADMIN', 'ROLE_MANAGER')]
 * public function deleteArticle(): Response {}
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
final readonly class RequireRole
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        public array $roles,
    ) {
    }
}

