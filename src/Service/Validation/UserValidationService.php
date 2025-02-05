<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use WebWMS\Entity\User;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Service\Validation',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'UserValidationService'
)]
class UserValidationService
{
    /**
     * @return array<string, array<string>|bool|string|null>
     *
     * @SuppressWarnings(CyclomaticComplexity)
     * @SuppressWarnings(NPathComplexity)
     * @SuppressWarnings(ElseExpression)
     */
    public function validateUserData(User $user): array
    {
        $responseData = [];

        if ($user->getUserIdentifier() === '0') {
            $responseData['error']['username'] = 'Der Benutzername darf nicht leer sein.';
        } else {
            $responseData['username'] = $user->getUserIdentifier();
        }

        if ($user->getFirstname() === '' || $user->getFirstname() === '0') {
            $responseData['error']['firstname'] = 'Der Vorname darf nicht leer sein.';
        } else {
            $responseData['firstname'] = $user->getFirstname();
        }

        if ($user->getLastname() === '' || $user->getLastname() === '0') {
            $responseData['error']['lastname'] = 'Der Nachname darf nicht leer sein.';
        } else {
            $responseData['lastname'] = $user->getLastname();
        }

        if (!isset($responseData['error'])) {
            $responseData['success'] = true;
        }

        return $responseData;
    }
}
