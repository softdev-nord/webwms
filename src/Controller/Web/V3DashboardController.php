<?php

declare(strict_types=1);

namespace WebWMS\Controller\Web;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use WebWMS\Administration\Application\Dashboard\V3DashboardQueryService;
use WebWMS\Integration\Application\ApiV3QueryService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3', name: 'v3_')]
final class V3DashboardController extends AbstractController
{
    #[Route('', name: 'dashboard', methods: ['GET'])]
    public function dashboard(V3DashboardQueryService $dashboard): Response
    {
        $user = $this->tenantUser();

        return $this->render('v3/dashboard/index.html.twig', [
            'summary' => $dashboard->summary($user->tenantId()),
            'page' => 'Dashboard',
        ]);
    }

    #[Route('/inventory/stock', name: 'stock', methods: ['GET'])]
    #[IsGranted('inventory.stock.read')]
    public function stock(Request $request, ApiV3QueryService $queries): Response
    {
        $user = $this->tenantUser();
        $warehouse = trim((string) $request->query->get('warehouse'));
        $warehouseId = $warehouse === '' ? null : $warehouse;

        return $this->render('v3/inventory/stock.html.twig', [
            'warehouses' => $queries->warehouses($user->tenantId()),
            'stock' => $queries->stock($user->tenantId(), $warehouseId, 200, null),
            'selectedWarehouse' => $warehouseId,
            'page' => 'Lagerbestand',
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
