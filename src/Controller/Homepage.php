<?php

namespace WebWMS\Controller;

use WebWMS\Controller\Requirements as Requirements;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
