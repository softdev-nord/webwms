<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use WebWMS\Entity\User;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        UserValidationService
 */
class UserValidationService
{
    /**
     * @return array<string, array<string, string>|bool|float|string>
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function validateUserData(User $requestData): array
    {
        $responseData = [];

        if (empty($requestData->getUserIdentifier())) {
            $responseData['error']['username'] = 'Der Benutzername darf nicht leer sein.';
        } else {
            $responseData['username'] = $requestData->getUserIdentifier();
        }

        if (empty($requestData->getRoles())) {
            $responseData['error']['roles'] = 'Die Benutzerrolle darf nicht leer sein.';
        } else {
            $responseData['roles'] = $requestData->getRoles();
        }

        if (empty($requestData->getFirstname())) {
            $responseData['error']['firstname'] = 'Der Vorname darf nicht leer sein.';
        } else {
            $responseData['firstname'] = $requestData->getFirstname();
        }

        if (empty($requestData->getLastname())) {
            $responseData['error']['lastname'] = 'Der Nachname darf nicht leer sein.';
        } else {
            $responseData['lastname'] = $requestData->getLastname();
        }

        $responseData['success'] = empty($responseData['error']);

        return $responseData;
    }
}
