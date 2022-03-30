<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Controller\Requirements as Requirements;

/**
 * Class        Homepage
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 */
class Homepage extends AbstractController
{
    /** @var Requirements */
    private $requirements;

    public function __construct(
        Requirements $requirements
    ) {
        $this->requirements = $requirements;
    }

    /**
     * @Route("/homepage", name="homepage")
     */
    public function index(): Response
    {
        return $this->render('homepage/index.html.twig',
            [
                'appName' => $this->requirements->getAppName(),
                'appVersion' => $this->requirements->getAppVersion(),
                'appVersionNumber' => $this->requirements->getAppVersionNumber(),
                'appCopyright' => $this->requirements->getAppCopyright(),
                'appLizenz' => $this->requirements->getAppLizenz(),
                'page' => 'Startseite',
            ]
        );
    }
}
