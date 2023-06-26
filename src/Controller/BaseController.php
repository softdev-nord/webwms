<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
        $redirect = new RedirectResponse('/');

        if ($this->getUser() === null) {
            $redirect = $this->redirectToRoute('app_login');
        }

        return $redirect;
    }
}
