<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class BaseValidationService
{
    public function __construct(
        protected PropertyAccessorInterface $propertyAccessor,
    ) {
    }

    /**
     * @param array<mixed> $entity
     * @param string $path
     * @return string|null
     */
    public function getValue(array $entity, string $path): ?string
    {
        $value = strval($this->propertyAccessor->getValue($entity, $path));

        if (!$value) {
            return null;
        }

        return $value;
    }
}
