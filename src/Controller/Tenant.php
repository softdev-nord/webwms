<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Controller',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'Tenant'
)]
class Tenant extends BaseController
{
    #[Route('/tenant', name: 'tenant')]
    public function index(): Response
    {
        return $this->render('tenant/index.html.twig');
    }
}
