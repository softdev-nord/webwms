<?php

declare(strict_types=1);

namespace WebWMS\Administration\Application\Access;

final class V3PermissionCatalog
{
    /** @var list<string> */
    public const ALL = [
        'administration.api_client.read', 'administration.api_client.write',
        'administration.role.read', 'administration.role.write',
        'administration.user.read', 'administration.user.write',
        'fulfillment.loading.execute', 'fulfillment.loading.read', 'fulfillment.loading.write',
        'fulfillment.pack.execute', 'fulfillment.pack.read', 'fulfillment.pack.write',
        'fulfillment.pick.assign', 'fulfillment.pick.execute', 'fulfillment.pick.read', 'fulfillment.pick.write',
        'fulfillment.ship.dispatch', 'fulfillment.ship.label', 'fulfillment.ship.read', 'fulfillment.ship.write',
        'integration.carrier.execute', 'integration.carrier.read',
        'integration.carrier_connection.read', 'integration.carrier_connection.write',
        'integration.erp_connection.read', 'integration.erp_connection.write',
        'integration.device.read', 'integration.device.scan', 'integration.device.write',
        'integration.automation.execute', 'integration.automation.read', 'integration.automation.write',
        'integration.measurement.capture', 'integration.measurement.read', 'integration.measurement.write',
        'integration.outbox.acknowledge', 'integration.outbox.read', 'integration.outbox.retry',
        'integration.print_job.execute', 'integration.print_job.read', 'integration.print_job.write',
        'integration.printer.read', 'integration.printer.write',
        'inventory.allocation.read', 'inventory.allocation.write', 'inventory.location.read',
        'inventory.product.read', 'inventory.product.write', 'inventory.stock.movement.read',
        'inventory.stock.read', 'inventory.stock.transfer',
        'outbound.order.read', 'outbound.order.release', 'outbound.order.write',
    ];

    public static function contains(string $permission): bool
    {
        return in_array($permission, self::ALL, true);
    }
}
