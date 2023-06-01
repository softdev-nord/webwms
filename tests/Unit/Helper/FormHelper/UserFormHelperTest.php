<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Helper\FormHelper;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use WebWMS\Entity\User;
use WebWMS\Form\User\AddUserType;
use WebWMS\Form\User\ChangePasswordType;
use WebWMS\Form\User\DeleteUserType;
use WebWMS\Form\User\EditUserType;
use WebWMS\Helper\FormHelper\UserFormHelper;

/**
 * @package:    WebWMS\Tests\Unit\Helper\FormHelper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserFormHelperTest
 *
 * @covers \WebWMS\Helper\FormHelper\UserFormHelper
 */
final class UserFormHelperTest extends TestCase
{
    public function testCreateForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $type = 'SomeType';
        $data = null;
        $options = [];

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with($type, $data, $options)
            ->willReturn($formInterface);

        $helper = new UserFormHelper($formFactory);
        $result = $helper->createForm($type, $data, $options);

        self::assertSame($formInterface, $result);
    }

    public function testAddUserForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(AddUserType::class)
            ->willReturn($formInterface);

        $helper = new UserFormHelper($formFactory);
        $result = $helper->addUserForm();

        self::assertSame($formInterface, $result);
    }

    public function testEditUserForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $user = $this->createMock(User::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(EditUserType::class, $user)
            ->willReturn($formInterface);

        $helper = new UserFormHelper($formFactory);
        $result = $helper->editUserForm($user);

        self::assertSame($formInterface, $result);
    }

    public function testDeleteUserForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $user = $this->createMock(User::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(DeleteUserType::class, $user)
            ->willReturn($formInterface);

        $helper = new UserFormHelper($formFactory);
        $result = $helper->deleteUserForm($user);

        self::assertSame($formInterface, $result);
    }

    public function testChangePasswordForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $user = $this->createMock(User::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(ChangePasswordType::class, $user)
            ->willReturn($formInterface);

        $helper = new UserFormHelper($formFactory);
        $result = $helper->changePasswordForm($user);

        self::assertSame($formInterface, $result);
    }
}
