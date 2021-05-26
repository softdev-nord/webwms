<?php

namespace WebWMS\Controller;

use Doctrine;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use WebWMS\Controller\Requirements as Requirements;

class Security extends AbstractController
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Requirements
     */
    private $requirements;

    public function __construct(Requirements $requirements, Doctrine\DBAL\Connection $db)
    {
        $this->requirements = $requirements;
    }

    /**
     * @Route("/", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $webServer = $_SERVER['SERVER_SOFTWARE'];
        $serverIp = $_SERVER['REMOTE_ADDR'];
        $serverName = $_SERVER['SERVER_NAME'];

        $freeDiskSpace = $this->requirements->checkDiskFreeSpace();
        $phpVersion = $this->requirements->checkPhp();
        $mySqlVersion = $this->requirements->getServerVersion();

        //return $this->redirectToRoute('target_path');

        return $this->render('security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error,
                'webServer' => $webServer,
                'serverIp' => $serverIp,
                'serverName' => $serverName,
                'freeDiskSpace' => $freeDiskSpace,
                'phpVersion' => $phpVersion,
                'mySqlVersion' => $mySqlVersion,
                'appVersion' => Requirements::APP_VERSION,
                'appVersionNumber' => Requirements::APP_VERSION_NUMBER,
                'appCopyright' => Requirements::APP_COPYRIGHT,
                'appLizenz' => Requirements::APP_LIZENZ,
            ]
        );
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
