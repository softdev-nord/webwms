<?php

declare(strict_types=1);

namespace WebWMS\Inventory\Application;

use WebWMS\Administration\Domain\Tenant\TenantId;
use WebWMS\Inventory\Domain\InventoryId;
use WebWMS\Inventory\Domain\InventoryRepository;
use WebWMS\Inventory\Domain\ProductReference;
use WebWMS\Inventory\Domain\Sku;

final readonly class RegisterProductHandler
{
    public function __construct(private InventoryRepository $inventory)
    {
    }

    public function __invoke(RegisterProductCommand $command): ProductReference
    {
        $product = new ProductReference(
            new InventoryId($command->productId),
            new TenantId($command->tenantId),
            new Sku($command->sku),
            $command->name,
            $command->occurredAt,
        );
        $this->inventory->saveProduct($product);

        return $product;
    }
}
