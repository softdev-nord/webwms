<?php

declare(strict_types=1);

namespace WebWMS\Controller\Api\V3;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use WebWMS\Platform\Application\PlatformControlService;
use WebWMS\Security\V3\TenantPermissionUser;

final class PartnerPortalApiController extends AbstractController
{
    public function __construct(private readonly PlatformControlService $platform)
    {
    }

    #[Route('/api/v3/partner-portal', name: 'api_v3_partner_portal', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw new LogicException('The V3 session does not contain a tenant user.');
        }

        return new JsonResponse(['data' => $this->platform->partnerPortal($user->tenantId(), $user->actorId())]);
    }
}
