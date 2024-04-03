<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

/**
 * @package:    WebWMS\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserValidationService
 */
class UserValidationService
{
    /**
     * @param array<mixed> $requestData
     * @return array<string, array<string>|bool|string|null>
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function validateUserData(array $requestData): array
    {
        $responseData = [];

        if (!$requestData['username']) {
            $responseData['error']['username'] = 'Der Benutzername darf nicht leer sein.';
        } else {
            $responseData['username'] = $requestData['username'];
        }

        if (!$requestData['firstname']) {
            $responseData['error']['firstname'] = 'Der Vorname darf nicht leer sein.';
        } else {
            $responseData['firstname'] = $requestData['firstname'];
        }

        if (!$requestData['lastname']) {
            $responseData['error']['lastname'] = 'Der Nachname darf nicht leer sein.';
        } else {
            $responseData['lastname'] = $requestData['lastname'];
        }

        if (!$requestData['email']) {
            $responseData['error']['email'] = 'Die E-Mail-Adresse darf nicht leer sein.';
        } else {
            $responseData['email'] = $requestData['email'];
        }

        if (!$requestData['userGroups']) {
            $responseData['error']['userGroups'] = 'Sie müssen mindestens eine Benutzergruppe auswählen.';
        } else {
            $responseData['userGroups'] = $requestData['userGroups'];
        }

        if (!isset($responseData['error'])) {
            $responseData['success'] = true;
        }

        return $responseData;
    }
}
