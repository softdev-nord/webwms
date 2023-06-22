<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use WebWMS\Service\RequirementsService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        Security
 */
class Security extends AbstractController
{
    public function __construct(
        private readonly RequirementsService $requirementsService
    ) {
    }

    #[Route(path: '/', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $webServer = $request->server->get('SERVER_SOFTWARE');
        $serverIp = $request->server->get('REMOTE_ADDR');
        $serverName = $request->server->get('SERVER_NAME');

        $freeDiskSpace = $this->requirementsService->checkDiskFreeSpace();
        $mySqlVersion = $request->server->get('DATABASE');

        return $this->render(
            'security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error,
                'webServer' => $webServer,
                'serverIp' => $serverIp,
                'serverName' => $serverName,
                'freeDiskSpace' => $freeDiskSpace,
                'phpVersion' => phpversion(),
                'mySqlVersion' => $mySqlVersion,
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
            ]
        );
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
