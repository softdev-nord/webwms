<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use WebWMS\Entity\UserGroupEntity;
use WebWMS\Service\RequirementsService;
use WebWMS\Service\User\UserGroup\UserGroupService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        UserGroupController
 */
class UserGroupController extends AbstractController
{
    public function __construct(
        private readonly RequirementsService $requirementsService,
        private readonly UserGroupService $userGroupService
    ) {
    }

    #[Route('/einstellungen/benutzer/benutzergruppen', name: 'configuration_user_groups')]
    public function index(): Response
    {
        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'user/user_group/index.html.twig',
            [
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Übersicht Benutzergruppen',
                'user_groups' => $this->getAllUserGroups(),
            ]
        );
    }

    /**
     * @return array<int, UserGroupEntity>
     */
    #[Route('/user_group_ajax', name: 'user_group_ajax')]
    public function getAllUserGroups(): array
    {
        return $this->userGroupService->getAllUserGroups();
    }
}
