<?php

declare(strict_types=1);

namespace WebWMS\Platform\Presentation\Web;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use WebWMS\Administration\Application\Dashboard\DashboardQueryService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3', name: 'v3_')]
class DashboardController extends AbstractController
{
    #[Route('', name: 'dashboard', methods: ['GET'])]
    public function dashboard(DashboardQueryService $dashboard): Response
    {
        $user = $this->tenantUser();

        return $this->render('platform/dashboard/index.html.twig', [
            'summary' => $dashboard->summary($user->tenantId()),
            'page' => 'theme.dashboard',
        ]);
    }

    private function tenantUser(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw new LogicException('The V3 session does not contain a tenant user.');
        }

        return $user;
    }
}
