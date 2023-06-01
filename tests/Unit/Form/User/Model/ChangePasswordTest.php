<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\User\Model;

use PHPUnit\Framework\TestCase;
use WebWMS\Form\User\Model\ChangePassword;

/**
 * @package:    WebWMS\Tests\Unit\Form\User\Model
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ChangePasswordTest
 *
 * @covers \WebWMS\Form\User\Model\ChangePassword
 */
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
        self::assertEquals($oldPassword, $this->changePassword->getOldPassword());

        // Test setNewPassword() and getNewPassword()
        $newPassword = 'newPassword';
        $this->changePassword->setNewPassword($newPassword);
        self::assertEquals($newPassword, $this->changePassword->getNewPassword());
    }
}
