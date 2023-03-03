<?php

declare(strict_types=1);

namespace WebWMS\Form\User\Model;

class ChangePassword
{
    public string $oldPassword = '';

    public string $newPassword = '';

    public function getOldPassword(): string
    {
        return $this->oldPassword;
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }
}
