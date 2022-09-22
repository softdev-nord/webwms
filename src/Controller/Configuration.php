<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Form\Configuration\GeneralConfigurationType;
use WebWMS\Service\ConfigurationService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        Configuration
 */
class Configuration extends AbstractController
{
    public function __construct(
        private ConfigurationService $configurationService,
        private Requirements $requirements
    ) {
    }

    #[Route('/einstellungen', name: 'configuration')]
    public function index(): Response
    {
        $generalConfiguration = $this->createForm(GeneralConfigurationType::class);

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('configuration/index.html.twig', [
            'appName' => $this->requirements->getAppName(),
            'appVersion' => $this->requirements->getAppVersion(),
            'appVersionNumber' => $this->requirements->getAppVersionNumber(),
            'appCopyright' => $this->requirements->getAppCopyright(),
            'appLizenz' => $this->requirements->getAppLizenz(),
            'generalConfiguration' => $generalConfiguration->createView(),
            'page' => 'Einstellungen',
        ]);
    }
}
