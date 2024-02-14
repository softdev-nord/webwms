<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use WebWMS\Entity\UserEntity;

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
    public function validateUserData(UserEntity $userEntity): array
    {
        $responseData = [];

        if ($userEntity->getUserIdentifier() === '' || $userEntity->getUserIdentifier() === '0') {
            $responseData['error']['username'] = 'Der Benutzername darf nicht leer sein.';
        } else {
            $responseData['username'] = $userEntity->getUserIdentifier();
        }

        if ($userEntity->getFirstname() === '' || $userEntity->getFirstname() === '0') {
            $responseData['error']['firstname'] = 'Der Vorname darf nicht leer sein.';
        } else {
            $responseData['firstname'] = $userEntity->getFirstname();
        }

        if ($userEntity->getLastname() === '' || $userEntity->getLastname() === '0') {
            $responseData['error']['lastname'] = 'Der Nachname darf nicht leer sein.';
        } else {
            $responseData['lastname'] = $userEntity->getLastname();
        }

        if (!isset($responseData['error'])) {
            $responseData['success'] = true;
        }

        return $responseData;
    }
}
