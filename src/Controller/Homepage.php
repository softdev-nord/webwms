<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Kernel;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class       Homepage
 */
class Homepage extends AbstractController
{
    public function __construct(
        private Requirements $requirements,
        private Kernel $kernel
    ) {
    }

    #[Route('/homepage', name: 'homepage')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render(
            'homepage/index.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Startseite',
                'test' => $this->kernel->getBundles(),
            ]
        );
    }
}
