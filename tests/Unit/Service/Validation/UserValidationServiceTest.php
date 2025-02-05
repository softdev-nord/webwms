<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Service\Validation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Entity\User;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\Validation\UserValidationService;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Service\Validation',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'UserValidationServiceTest'
)]
#[CoversClass(UserValidationService::class)]
final class UserValidationServiceTest extends TestCase
{
    private UserValidationService $userValidationService;

    private User $user;

    protected function setUp(): void
    {
        $this->userValidationService = new UserValidationService();
        $this->user = new User();
    }

    public function testValidateUserDataWithValidUser(): void
    {
        $this->user->setUsername('testuser');
        $this->user->setFirstname('John');
        $this->user->setLastname('Doe');

        $result = $this->userValidationService->validateUserData($this->user);

        self::assertTrue($result['success']);
        self::assertEquals('testuser', $result['username']);
        self::assertEquals('John', $result['firstname']);
        self::assertEquals('Doe', $result['lastname']);
    }

    public function testValidateUserDataWithMissingFields(): void
    {
        $this->user->setUsername('');
        $this->user->setFirstname('');
        $this->user->setLastname('');

        $result = $this->userValidationService->validateUserData($this->user);

        $expectedResult = [
            'error' => [
                'username' => 'Der Benutzername darf nicht leer sein.',
                'firstname' => 'Der Vorname darf nicht leer sein.',
                'lastname' => 'Der Nachname darf nicht leer sein.',
            ],
        ];

        self::assertArrayNotHasKey('success', $result);
        self::assertArrayHasKey('error', $result);
        self::assertSame($expectedResult, $result);
    }
}
