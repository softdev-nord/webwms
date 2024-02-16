<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Validation;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use WebWMS\Entity\UserEntity;
use WebWMS\Form\User\Model\ChangePassword;
use WebWMS\Service\Validation\ChangePasswordValidationService;

/**
 * @package:    WebWMS\Tests\Unit\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ChangePasswordValidationServiceTest
 */
#[CoversClass(ChangePasswordValidationService::class)]
final class ChangePasswordValidationServiceTest extends TestCase
{
    private ChangePasswordValidationService $changePasswordValidationService;

    #[Override]
    protected function setUp(): void
    {
        $passwordHasherMock = $this->createMock(UserPasswordHasherInterface::class);
        $passwordHasherMock
            ->method('isPasswordValid')
            ->willReturnCallback(static fn (UserEntity $userEntity, string $password): bool => $userEntity->getPassword() === $password);

        $this->changePasswordValidationService = new ChangePasswordValidationService($passwordHasherMock);
    }

    public function testValidateChangePasswordDataWithValidData(): void
    {
        $userEntity = new UserEntity();
        $userEntity->setPassword('old_password');

        $changePassword = new ChangePassword();
        $changePassword->setOldPassword('old_password');
        $changePassword->setNewPassword('new_password');

        $form = $this->createMock(FormInterface::class);
        $form->method('getData')->willReturn($changePassword);

        $expectedResponseData = [
            'oldPassword' => 'old_password',
            'newPassword' => 'new_password',
            'success' => true,
        ];

        $actualResponseData = $this->changePasswordValidationService->validateChangePasswordData($userEntity, $form);

        self::assertEquals($expectedResponseData, $actualResponseData);
    }

    public function testValidateChangePasswordDataWithIncorrectOldPassword(): void
    {
        $userEntity = new UserEntity();
        $userEntity->setPassword('old_password');

        $changePassword = new ChangePassword();
        $changePassword->setOldPassword('wrong_password');
        $changePassword->setNewPassword('new_password');

        $form = $this->createMock(FormInterface::class);
        $form->method('getData')->willReturn($changePassword);

        $expectedResponseData = [
            'error' => [
                'oldPassword' => 'Ihr aktuelles Passwort stimmt nicht mit Ihrer Eingabe überein.',
            ],
            'newPassword' => 'new_password',
        ];

        $actualResponseData = $this->changePasswordValidationService->validateChangePasswordData($userEntity, $form);

        self::assertEquals($expectedResponseData, $actualResponseData);
    }

    public function testValidateChangePasswordDataWithEmptyNewPassword(): void
    {
        $userEntity = new UserEntity();
        $userEntity->setPassword('old_password');

        $changePassword = new ChangePassword();
        $changePassword->setOldPassword('old_password');
        $changePassword->setNewPassword('');

        $form = $this->createMock(FormInterface::class);
        $form->method('getData')->willReturn($changePassword);

        $expectedResponseData = [
            'oldPassword' => 'old_password',
            'error' => [
                'newPassword' => 'Das neue Passwort darf nicht leer sein.',
            ],
        ];

        $actualResponseData = $this->changePasswordValidationService->validateChangePasswordData($userEntity, $form);

        self::assertEquals($expectedResponseData, $actualResponseData);
    }

    public function testValidateChangePasswordDataWithEmptyOldPassword(): void
    {
        $userEntity = new UserEntity();
        $userEntity->setPassword('');

        $changePassword = new ChangePassword();
        $changePassword->setOldPassword('');
        $changePassword->setNewPassword('new_password');

        $form = $this->createMock(FormInterface::class);
        $form->method('getData')->willReturn($changePassword);

        $expectedResponseData = [
            'error' => [
                'oldPassword' => 'Sie müssen Ihr aktuelles Passwort eingeben.',
            ],
            'newPassword' => 'new_password',
        ];

        $actualResponseData = $this->changePasswordValidationService->validateChangePasswordData($userEntity, $form);

        self::assertEquals($expectedResponseData, $actualResponseData);
    }
}
