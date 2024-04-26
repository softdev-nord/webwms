<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Validation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\UserEntity;
use WebWMS\Service\Validation\UserValidationService;

/**
 * @package:    WebWMS\Tests\Unit\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        UserValidationServiceTest
 */
#[CoversClass(UserValidationService::class)]
final class UserValidationServiceTest extends TestCase
{
    private UserValidationService $userValidationService;

    private UserEntity $userEntity;

    protected function setUp(): void
    {
        $this->userValidationService = new UserValidationService();
        $this->userEntity = new UserEntity();
    }

    public function testValidateUserDataWithValidUser(): void
    {
        $requestData = [
            'username' => 'testuser',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'jdoe@test.com',
            'userGroups' => [
                0 => '12',
            ],
        ];

        $result = $this->userValidationService->validateUserData($requestData);

        self::assertTrue($result['success']);
        self::assertEquals('testuser', $result['username']);
        self::assertEquals('John', $result['firstname']);
        self::assertEquals('Doe', $result['lastname']);
    }

    public function testValidateUserDataWithMissingFields(): void
    {
        $this->userEntity->setUsername('');
        $this->userEntity->setFirstname('');
        $this->userEntity->setLastname('');

        $requestData = [
            'error' => [
                'username' => '',
                'firstname' => '',
                'lastname' => '',
                'email' => '',
                'userGroups' => '',
            ],
        ];

        $result = $this->userValidationService->validateUserData($requestData);

        $expectedResult = [
            'error' => [
                'username' => 'Der Benutzername darf nicht leer sein.',
                'firstname' => 'Der Vorname darf nicht leer sein.',
                'lastname' => 'Der Nachname darf nicht leer sein.',
                'email' => 'Die E-Mail-Adresse darf nicht leer sein.',
                'userGroups' => 'Sie müssen mindestens eine Benutzergruppe auswählen.',
            ],
        ];

        self::assertArrayNotHasKey('success', $result);
        self::assertArrayHasKey('error', $result);
        self::assertEquals($expectedResult, $result);
    }
}
