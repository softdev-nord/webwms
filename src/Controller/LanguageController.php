<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Controller',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'Language'
)]
class Language extends AbstractController
{
    /**
     * @Route("/lang/{_locale}", requirements={
     *     "_locale"="en|es|de|it|ru"
     * })
     */
    public function index(Request $request): RedirectResponse
    {
        $referer = '';

        if ($request->headers->get('referer') !== null) {
            $locale = $request->getLocale();
            $request->setLocale($locale);
            $request->getSession()->set('_locale', $locale);
            $referer = $request->headers->get('referer');
        }

        return $this->redirect((string) $referer);
    }
}
