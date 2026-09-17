<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class V3SecurityController extends AbstractController
{
    #[Route('/v3/login', name: 'app_v3_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('security/v3_login.html.twig', [
            'last_identifier' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/v3/logout', name: 'app_v3_logout', methods: ['GET'])]
    public function logout(): never
    {
        throw new LogicException('This route is intercepted by the security firewall.');
    }
}
