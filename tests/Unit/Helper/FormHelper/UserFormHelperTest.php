<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Helper\FormHelper;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use WebWMS\Entity\UserEntity;
use WebWMS\Form\User\AddUserType;
use WebWMS\Form\User\ChangePasswordType;
use WebWMS\Form\User\DeleteUserType;
use WebWMS\Form\User\EditUserType;
use WebWMS\Helper\FormHelper\UserFormHelper;

/**
 * @package:    WebWMS\Tests\Unit\Helper\FormHelper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        UserFormHelperTest
 */
#[CoversClass(UserFormHelper::class)]
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

        $userFormHelper = new UserFormHelper($formFactory);
        $result = $userFormHelper->createForm($type, $data, $options);

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

        $userFormHelper = new UserFormHelper($formFactory);
        $result = $userFormHelper->addUserForm();

        self::assertSame($formInterface, $result);
    }

    public function testEditUserForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $user = $this->createMock(UserEntity::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(EditUserType::class, $user)
            ->willReturn($formInterface);

        $userFormHelper = new UserFormHelper($formFactory);
        $result = $userFormHelper->editUserForm($user);

        self::assertSame($formInterface, $result);
    }

    public function testDeleteUserForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $user = $this->createMock(UserEntity::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(DeleteUserType::class, $user)
            ->willReturn($formInterface);

        $userFormHelper = new UserFormHelper($formFactory);
        $result = $userFormHelper->deleteUserForm($user);

        self::assertSame($formInterface, $result);
    }

    public function testChangePasswordForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $user = $this->createMock(UserEntity::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(ChangePasswordType::class, $user)
            ->willReturn($formInterface);

        $userFormHelper = new UserFormHelper($formFactory);
        $result = $userFormHelper->changePasswordForm($user);

        self::assertSame($formInterface, $result);
    }
}
