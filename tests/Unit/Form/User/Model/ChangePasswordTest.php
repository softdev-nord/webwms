<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\User\Model;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WebWMS\Form\User\Model\ChangePassword;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Form\User\Model',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'ChangePasswordTest'
)]
#[CoversClass(ChangePassword::class)]
final class ChangePasswordTest extends TestCase
{
    private ChangePassword $changePassword;

    protected function setUp(): void
    {
        parent::setUp();

        $this->changePassword = new ChangePassword();
    }

    public function testGetterAndSetterMethods(): void
    {
        // Test setOldPassword() and getOldPassword()
        $oldPassword = 'oldPassword';
        $this->changePassword->setOldPassword($oldPassword);
        self::assertSame($oldPassword, $this->changePassword->getOldPassword());

        // Test setNewPassword() and getNewPassword()
        $newPassword = 'newPassword';
        $this->changePassword->setNewPassword($newPassword);
        self::assertSame($newPassword, $this->changePassword->getNewPassword());
    }
}
