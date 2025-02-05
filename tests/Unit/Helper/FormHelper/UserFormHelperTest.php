<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Helper\FormHelper;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use WebWMS\Entity\User;
use WebWMS\Form\User\AddUserType;
use WebWMS\Form\User\ChangePasswordType;
use WebWMS\Form\User\DeleteUserType;
use WebWMS\Form\User\EditUserType;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Helper\FormHelper\UserFormHelper;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Helper\FormHelper',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'UserFormHelperTest'
)]
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
            ->expects($this->once())
            ->method('create')
            ->with($type, $data, $options)
            ->willReturn($formInterface);

        $userFormHelper = new UserFormHelper($formFactory);
        $form = $userFormHelper->createForm($type, $data, $options);

        self::assertSame($formInterface, $form);
    }

    public function testAddUserForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(AddUserType::class)
            ->willReturn($formInterface);

        $userFormHelper = new UserFormHelper($formFactory);
        $form = $userFormHelper->addUserForm();

        self::assertSame($formInterface, $form);
    }

    public function testEditUserForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $user = $this->createMock(User::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(EditUserType::class, $user)
            ->willReturn($formInterface);

        $userFormHelper = new UserFormHelper($formFactory);
        $form = $userFormHelper->editUserForm($user);

        self::assertSame($formInterface, $form);
    }

    public function testDeleteUserForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $user = $this->createMock(User::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(DeleteUserType::class, $user)
            ->willReturn($formInterface);

        $userFormHelper = new UserFormHelper($formFactory);
        $form = $userFormHelper->deleteUserForm($user);

        self::assertSame($formInterface, $form);
    }

    public function testChangePasswordForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $user = $this->createMock(User::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(ChangePasswordType::class, $user)
            ->willReturn($formInterface);

        $userFormHelper = new UserFormHelper($formFactory);
        $form = $userFormHelper->changePasswordForm($user);

        self::assertSame($formInterface, $form);
    }
}
