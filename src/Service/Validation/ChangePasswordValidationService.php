<?php

declare(strict_types=1);

namespace WebWMS\Service\Validation;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use WebWMS\Entity\User;
use WebWMS\Form\User\Model\ChangePassword;

/**
 * @package:    WebWMS\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        ChangePasswordValidationService
 */
class ChangePasswordValidationService
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    /**
     * @return array<string, array<string, array<string>|string>|bool|string>
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function validateChangePasswordData(User $user, FormInterface $form): array
    {
        /** @var ChangePassword $requestData */
        $requestData = $form->getData();

        $hashedPassword = $this->passwordHasher
            ->isPasswordValid(
                $user,
                $requestData->getOldPassword()
            );

        $responseData = [];

        if (!$hashedPassword) {
            $responseData['error']['oldPassword'] = 'Ihr aktuelles Passwort stimmt nicht mit Ihrer Eingabe überein.';
        } elseif (!$requestData->getOldPassword()) {
            $responseData['error']['oldPassword'] = 'Sie müssen Ihr aktuelles Passwort eingeben.';
        } else {
            $responseData['oldPassword'] = $requestData->getOldPassword();
        }

        if (!$requestData->getNewPassword()) {
            $responseData['error']['newPassword'] = 'Das neue Passwort darf nicht leer sein.';
        } else {
            $responseData['newPassword'] = $requestData->getNewPassword();
        }

        if (!isset($responseData['error'])) {
            $responseData['success'] = true;
        }

        return $responseData;
    }
}
