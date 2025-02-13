<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\LoggingService;
use WebWMS\Service\RequirementsService;

#[ClassInformation(
    package: 'WebWMS\Controller',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'LoggingController'
)]
class LoggingController extends AbstractController
{
    public function __construct(
        private readonly RequirementsService $requirementsService,
        private readonly LoggingService $loggingService,
    ) {
    }

    #[Route('/logging', name: 'logging')]
    public function index(): Response
    {
        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'logging/index.html.twig',
            [
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Logs',
            ]
        );
    }

    /**
     * @throws Exception
     */
    #[Route('/logging_ajax', name: 'logging_ajax')]
    public function getAllLogs(): JsonResponse
    {
        return $this->loggingService->getAllLogs();
    }
}
