<?php

namespace WebWMS\Controller;

use Composer\IO\NullIO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Form\Module\ModuleType;
use WebWMS\Service\ModuleService;
use WebWMS\Service\RequirementsService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        Module
 */
class Module extends AbstractController
{
    public function __construct(
        private readonly RequirementsService $requirementsService,
        private readonly ModuleService       $bundleService,
        private readonly string              $bundleDir,
        private readonly string              $projectDir,
    ) {
    }

    #[Route('/module', name: 'module')]
    public function index(): Response
    {
        if ($this->getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        $pluginsFromFileSystem = $this->bundleService->getModules($this->bundleDir, $this->projectDir, new NullIO());
        $modules = $this->createForm(ModuleType::class);

        return $this->render('module/index.html.twig', [
            'appName' => $this->requirementsService->getAppName(),
            'appVersion' => $this->requirementsService->getAppVersion(),
            'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
            'appCopyright' => $this->requirementsService->getAppCopyright(),
            'appLizenz' => $this->requirementsService->getAppLizenz(),
            'modulesForm' => $modules->createView(),
            'modules' => $pluginsFromFileSystem,
            'page' => 'Übersicht Module',
        ]);
    }
}
