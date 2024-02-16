<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use WebWMS\Form\Configuration\GeneralConfigurationType;
use WebWMS\Service\Configuration\ConfigurationService;
use WebWMS\Service\RequirementsService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        ConfigurationController
 */
class ConfigurationController extends AbstractController
{
    public function __construct(
        private readonly ConfigurationService $configurationService,
        private readonly RequirementsService $requirementsService
    ) {
    }

    #[Route('/einstellungen', name: 'configuration')]
    public function index(): Response
    {
        $form = $this->createForm(GeneralConfigurationType::class);

        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('configuration/index.html.twig', [
            'appName' => $this->requirementsService->getAppName(),
            'appVersion' => $this->requirementsService->getAppVersion(),
            'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
            'appCopyright' => $this->requirementsService->getAppCopyright(),
            'appLizenz' => $this->requirementsService->getAppLizenz(),
            'generalConfiguration' => $form->createView(),
            'page' => 'Einstellungen',
            'configurations' => $this->configurationService->getAllConfigurations(),
            'systemInformation' => $this->configurationService->prepareSystemInformation(),
        ]);
    }
}
