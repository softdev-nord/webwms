<?php

declare(strict_types=1);

namespace WebWMS\Warehouse\Stock\Presentation\Web;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use WebWMS\Warehouse\Application\Query\WarehouseQueryService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3', name: 'v3_')]
class StockController extends AbstractController
{
    #[Route('/inventory/stock', name: 'stock', methods: ['GET'])]
    #[IsGranted('inventory.stock.read')]
    public function stock(Request $request, WarehouseQueryService $queries): Response
    {
        $user = $this->tenantUser();
        $warehouse = trim((string) $request->query->get('warehouse'));
        $warehouseId = $warehouse === '' ? null : $warehouse;

        return $this->render('warehouse/stock.html.twig', [
            'warehouses' => $queries->warehouses($user->tenantId()),
            'stock' => $queries->stock($user->tenantId(), $warehouseId, 200, null),
            'selectedWarehouse' => $warehouseId,
            'page' => 'dashboard.page.inventory',
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
