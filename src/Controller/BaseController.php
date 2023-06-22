<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
