<?php

declare(strict_types=1);

namespace WebWMS\Bundles\DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package:    WebWMS\Bundles\DashboardBundle\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        LanguageController
 */
class LanguageController extends AbstractController
{
    /**
     * @Route("/lang/{_locale}", requirements={
     *     "_locale"="en|es|de|it|ru"
     * })
     */
    public function index(Request $request): RedirectResponse
    {
        $locale = $request->getLocale();
        $request->setLocale($locale);
        $request->getSession()->set('_locale', $locale);
        $referer = $request->headers->get('referer');

        return $this->redirect($referer);
    }
}
