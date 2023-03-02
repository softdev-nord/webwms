<?php

declare(strict_types=1);

namespace WebWMS\Helper\FormHelper;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use WebWMS\Form\User\AddUserType;
use WebWMS\Form\User\ChangePasswordType;
use WebWMS\Form\User\DeleteUserType;
use WebWMS\Form\User\EditUserType;

/**
 * @package:    WebWMS\Helper\FormHelper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        UserFormHelper
 */
class UserFormHelper
{
    public function __construct(
        private FormFactoryInterface $formFactory
    ) {
    }

    public function createForm(string $type, mixed $data = null, array $options = []): FormInterface
    {
        return $this->formFactory->create($type, $data, $options);
    }

    public function addUserForm(): FormInterface
    {
        return $this->createForm(AddUserType::class);
    }

    public function editUserForm($user): FormInterface
    {
        return $this->createForm(EditUserType::class, $user);
    }

    public function deleteUserForm($user): FormInterface
    {
        return $this->createForm(DeleteUserType::class, $user);
    }

    public function changePasswordForm($changePasswordModel): FormInterface
    {
        return $this->createForm(ChangePasswordType::class, $changePasswordModel);
    }
}
