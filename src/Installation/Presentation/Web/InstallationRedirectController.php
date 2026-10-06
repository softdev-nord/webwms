<?php

declare(strict_types=1);

namespace WebWMS\Installation\Presentation\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;

final class InstallationRedirectController extends AbstractController
{
    #[Route('/install', name: 'installation_legacy_redirect', methods: ['GET'])]
    #[Route('/install/', name: 'installation_legacy_trailing_slash_redirect', methods: ['GET'])]
    public function __invoke(): RedirectResponse
    {
        return $this->redirectToRoute('installation_index');
    }
}
