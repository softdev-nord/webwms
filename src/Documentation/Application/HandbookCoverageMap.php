<?php

declare(strict_types=1);

namespace WebWMS\Documentation\Application;

final class HandbookCoverageMap
{
    /** @var array<string, string> */
    private const array EXACT_ROUTES = [
        'v3_administration_deployment_configure' => 'system-configuration',
        'v3_administration_number_range_next' => 'system-configuration',
        'v3_administration_process_configure' => 'system-configuration',
        'v3_administration_workspace' => 'system-configuration',
        'v3_administration_workspace_create' => 'system-configuration',
        'v3_administration_workspace_status' => 'system-configuration',
        'v3_documentation_index' => 'navigation-and-lists',
        'v3_documentation_show' => 'navigation-and-lists',
        'v3_documentation_openapi' => 'api-and-automation',
        'v3_documentation_swagger' => 'api-and-automation',
        'v3_outbox_acknowledge' => 'troubleshooting',
        'v3_outbox_retry' => 'troubleshooting',
        'v3_outbox_show' => 'troubleshooting',
    ];

    /** @var array<string, string> */
    private const array PREFIX_ROUTES = [
        'app_v3_' => 'getting-started',
        'v3_dashboard' => 'getting-started',
        'v3_administration_' => 'users-roles-and-security',
        'v3_inventory_control_' => 'inventory-counting',
        'v3_inventory_' => 'warehouse-and-stock',
        'v3_stock' => 'warehouse-and-stock',
        'v3_inbound_' => 'inbound',
        'v3_internal_transport_' => 'fulfillment-and-outbound',
        'v3_outbound_' => 'fulfillment-and-outbound',
        'v3_picking_' => 'fulfillment-and-outbound',
        'v3_packing_' => 'fulfillment-and-outbound',
        'v3_shipping_' => 'fulfillment-and-outbound',
        'v3_loading_' => 'fulfillment-and-outbound',
        'v3_erp_connection_' => 'integrations-and-devices',
        'v3_data_exchange_' => 'integrations-and-devices',
        'v3_carrier_connection_' => 'integrations-and-devices',
        'v3_printing_' => 'integrations-and-devices',
        'v3_device_' => 'integrations-and-devices',
        'v3_measurement_' => 'integrations-and-devices',
        'v3_wcs_' => 'integrations-and-devices',
        'v3_outbox_' => 'api-and-automation',
        'v3_automation_' => 'api-and-automation',
        'v3_transport_' => 'api-and-automation',
        'v3_platform_' => 'platform-and-extensions',
        'v3_partner_portal' => 'platform-and-extensions',
        'v3_extension_' => 'platform-and-extensions',
    ];

    public static function chapterForRoute(string $route): ?string
    {
        if (isset(self::EXACT_ROUTES[$route])) {
            return self::EXACT_ROUTES[$route];
        }

        foreach (self::PREFIX_ROUTES as $prefix => $chapter) {
            if (str_starts_with($route, $prefix)) {
                return $chapter;
            }
        }

        return null;
    }

    /** @param iterable<string> $routes @return array<string, string> */
    public static function matrix(iterable $routes): array
    {
        $matrix = [];
        foreach ($routes as $route) {
            $chapter = self::chapterForRoute($route);
            if ($chapter !== null) {
                $matrix[$route] = $chapter;
            }
        }
        ksort($matrix);

        return $matrix;
    }
}
