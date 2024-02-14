<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Validation;

use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\UserEntity;
use WebWMS\Service\Validation\UserValidationService;

/**
 * @package:    WebWMS\Tests\Unit\Service\Validation
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserValidationServiceTest
 */
#[CoversClass(UserValidationService::class)]
final class UserValidationServiceTest extends TestCase
{
    private UserValidationService $userValidationService;

    private UserEntity $userEntity;

    #[Override]
    protected function setUp(): void
    {
        $this->userValidationService = new UserValidationService();
        $this->userEntity = new UserEntity();
    }

    public function testValidateUserDataWithValidUser(): void
    {
        $this->userEntity->setUsername('testuser');
        $this->userEntity->setFirstname('John');
        $this->userEntity->setLastname('Doe');

        $result = $this->userValidationService->validateUserData($this->userEntity);

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

        $result = $this->userValidationService->validateUserData($this->userEntity);

        $expectedResult = [
            'error' => [
                'username' => 'Der Benutzername darf nicht leer sein.',
                'firstname' => 'Der Vorname darf nicht leer sein.',
                'lastname' => 'Der Nachname darf nicht leer sein.',
            ],
        ];

        self::assertArrayNotHasKey('success', $result);
        self::assertArrayHasKey('error', $result);
        self::assertEquals($expectedResult, $result);
    }
}
