<?php

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(
        private Requirements $requirements
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
        $phpVersion = $request->server->get('PHP_VERSION');

        $freeDiskSpace = $this->requirements->checkDiskFreeSpace();
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
                'phpVersion' => $phpVersion,
                'mySqlVersion' => $mySqlVersion,
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
            ]
        );
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
