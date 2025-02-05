<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;

class BaseValidationService
{
    /**
     * @param array<string, string> $fields
     * @throws ReflectionException
     * @return array<string, mixed>
     *
     * @SuppressWarnings(ElseExpression)
     */
    protected function validate(object $entity, array $fields): array
    {
        $responseData = [];
        $reflectionClass = new ReflectionClass($entity);

        foreach ($fields as $field => $errorMessage) {
            $methodName = 'get' . ucfirst($field);
            if ($reflectionClass->hasMethod($methodName)) {
                $method = $reflectionClass->getMethod($methodName);
                $value = $method->invoke($entity);
                $responseData = match (true) {
                    $value === '' || $value === '0' => array_merge($responseData, ['error' => [$field => $errorMessage]]),
                    default => array_merge($responseData, [$field => $value]),
                };
            } else {
                throw new InvalidArgumentException(
                    sprintf(
                        'Method %s does not exist on %s',
                        $methodName,
                        $reflectionClass->getName()
                    )
                );
            }
        }

        return $responseData;
    }
}
