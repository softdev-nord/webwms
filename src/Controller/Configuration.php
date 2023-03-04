<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Form\Configuration\GeneralConfigurationType;
use WebWMS\Service\Configuration\ConfigurationService;
use WebWMS\Service\RequirementsService;

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
        private RequirementsService $requirementsService,
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
            'appName' => $this->requirementsService->getAppName(),
            'appVersion' => $this->requirementsService->getAppVersion(),
            'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
            'appCopyright' => $this->requirementsService->getAppCopyright(),
            'appLizenz' => $this->requirementsService->getAppLizenz(),
            'generalConfiguration' => $generalConfiguration->createView(),
            'page' => 'Einstellungen',
            'configuration' => $this->configurationService->getAllConfigurations(),
        ]);
    }
}
