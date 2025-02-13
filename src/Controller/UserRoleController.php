<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use WebWMS\Entity\UserRight as UserRightEntity;
use WebWMS\Entity\UserRole as UserRoleEntity;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\RequirementsService;
use WebWMS\Service\User\UserRight\UserRightService;
use WebWMS\Service\User\UserRole\UserRoleService;

#[ClassInformation(
    package: 'WebWMS\Controller',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'UserRoleController'
)]
class UserRoleController extends AbstractController
{
    public function __construct(
        private readonly RequirementsService $requirementsService,
        private readonly UserRoleService $userRoleService,
        private readonly UserRightService $userRightService,
    ) {
    }

    #[Route('/einstellungen/benutzer/benutzerrollen', name: 'configuration_user_roles')]
    public function index(): Response
    {
        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'user/user_role/index.html.twig',
            [
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Übersicht Benutzerrollen',
                'user_roles' => $this->getAllUserRoles(),
                'user_rights' => $this->getAllUserRights(),

            ]
        );
    }

    /**
     * @return array<int, UserRoleEntity>
     */
    #[Route('/user_role_ajax', name: 'user_role_ajax')]
    public function getAllUserRoles(): array
    {
        return $this->userRoleService->getAllUserRoles();
    }

    /**
     * @return array<int, UserRightEntity>
     */
    public function getAllUserRights(): array
    {
        return $this->userRightService->getAllUserRights();
    }
}
