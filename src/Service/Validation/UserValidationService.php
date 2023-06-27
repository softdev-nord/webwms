<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use WebWMS\Entity\User;

/**
 * @package:    WebWMS\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserValidationService
 */
class UserValidationService
{
    /**
     * @return array<string, array<string>|bool|string|null>
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function validateUserData(User $user): array
    {
        $responseData = [];

        if (!$user->getUserIdentifier()) {
            $responseData['error']['username'] = 'Der Benutzername darf nicht leer sein.';
        } else {
            $responseData['username'] = $user->getUserIdentifier();
        }

        if (!$user->getFirstname()) {
            $responseData['error']['firstname'] = 'Der Vorname darf nicht leer sein.';
        } else {
            $responseData['firstname'] = $user->getFirstname();
        }

        if (!$user->getLastname()) {
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
