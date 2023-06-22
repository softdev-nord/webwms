<?php

declare(strict_types=1);

namespace WebWMS\Helper\FormHelper;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use WebWMS\Form\User\AddUserType;
use WebWMS\Form\User\ChangePasswordType;
use WebWMS\Form\User\DeleteUserType;
use WebWMS\Form\User\EditUserType;

/**
 * @package:    WebWMS\Helper\FormHelper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserFormHelper
 */
class UserFormHelper
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    /**
     * @param class-string<FormTypeInterface<mixed>> $type
     * @param mixed|null $data
     * @param array<string> $options
     * @return FormInterface
     */
    public function createForm(string $type, mixed $data = null, array $options = []): FormInterface
    {
        return $this->formFactory->create($type, $data, $options);
    }

    public function addUserForm(): FormInterface
    {
        return $this->createForm(AddUserType::class);
    }

    /**
     * @param object $user
     * @return FormInterface
     */
    public function editUserForm(object $user): FormInterface
    {
        return $this->createForm(EditUserType::class, $user);
    }

    /**
     * @param object $user
     * @return FormInterface
     */
    public function deleteUserForm(object $user): FormInterface
    {
        return $this->createForm(DeleteUserType::class, $user);
    }

    /**
     * @param object $changePasswordModel
     * @return FormInterface
     */
    public function changePasswordForm(object $changePasswordModel): FormInterface
    {
        return $this->createForm(ChangePasswordType::class, $changePasswordModel);
    }
}
