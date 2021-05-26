<?php

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Controller\Requirements as Requirements;

class Homepage extends AbstractController
{
    /**
     * @Route("/homepage", name="homepage")
     */
    public function index()
    {
        return $this->render('homepage/index.html.twig', [
            'appVersion' => Requirements::APP_VERSION,
            'appVersionNumber' => Requirements::APP_VERSION_NUMBER,
            'appName' => Requirements::APP_NAME,
            'page' => 'Startseite',
        ]);
    }
}
