<?php

declare(strict_types=1);

namespace WebWMS\Form\User\Model;

/**
 * @package:    WebWMS\Form\User\Model
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ChangePassword
 */
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
