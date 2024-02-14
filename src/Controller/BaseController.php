<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        BaseController
 */
class BaseController extends AbstractController
{
    protected function checkUser(): RedirectResponse
    {
        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        return new RedirectResponse('/');
    }
}
