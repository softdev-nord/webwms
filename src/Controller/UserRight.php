<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Entity\UserRight as UserRightEntity;
use WebWMS\Service\RequirementsService;
use WebWMS\Service\User\UserRight\UserRightService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserRight
 */
class UserRight extends AbstractController
{
    public function __construct(
        private readonly RequirementsService $requirementsService,
        private readonly UserRightService $userRightService
    ) {
    }

    #[Route('/einstellungen/benutzer/benutzerrechte', name: 'configuration_user_rights')]
    public function index(): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'user/user_right/index.html.twig',
            [
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Übersicht Benutzerrechte',
                'user_rights' => $this->getAllUserRights(),
            ]
        );
    }

    /**
     * @return array<int, UserRightEntity>
     */
    #[Route('/user_right_ajax', name: 'user_right_ajax')]
    public function getAllUserRights(): array
    {
        return $this->userRightService->getAllUserRights();
    }
}
