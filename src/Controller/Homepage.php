<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\RequirementsService;

#[ClassInformation(
    package: 'WebWMS\Controller',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'Homepage'
)]
class Homepage extends AbstractController
{
    public function __construct(
        private readonly RequirementsService $requirementsService,
    ) {
    }

    #[Route('/homepage', name: 'homepage')]
    public function index(): Response
    {
        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render(
            'homepage/index.html.twig',
            [
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Startseite',
            ]
        );
    }
}
