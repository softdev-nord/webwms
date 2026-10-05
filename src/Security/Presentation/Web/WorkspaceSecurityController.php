<?php

declare(strict_types=1);

namespace WebWMS\Security\Presentation\Web;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class WorkspaceSecurityController extends AbstractController
{
    #[Route('/v3/login', name: 'app_v3_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $identifier = explode('|', $authenticationUtils->getLastUsername(), 2);

        return $this->render('security/workspace_login.html.twig', [
            'last_email' => count($identifier) === 2 ? $identifier[1] : $identifier[0],
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/v3/logout', name: 'app_v3_logout', methods: ['GET'])]
    public function logout(): never
    {
        throw new LogicException('This route is intercepted by the security firewall.');
    }
}
