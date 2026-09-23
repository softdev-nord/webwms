<?php

declare(strict_types=1);

namespace WebWMS\Controller\Web;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use WebWMS\Platform\Application\PlatformControlService;
use WebWMS\Security\V3\TenantPermissionUser;

final class V3PartnerPortalController extends AbstractController
{
    public function __construct(
        private readonly PlatformControlService $platform
    ) {
    }

    #[Route('/v3/partner-portal', name: 'v3_partner_portal', methods: ['GET'])]
    public function __invoke(): Response
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw new LogicException('The V3 session does not contain a tenant user.');
        }

        return $this->render('v3/platform/portal.html.twig', ['page' => 'Partnerportal', 'portal' => $this->platform->partnerPortal($user->tenantId(), $user->actorId())]);
    }

    #[Route('/v3/partner-portal/media/{mediaId}', name: 'v3_partner_portal_media', methods: ['GET'])]
    public function media(string $mediaId): Response
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw new LogicException('The V3 session does not contain a tenant user.');
        }
        $media = $this->platform->partnerMedia($user->tenantId(), $user->actorId(), $mediaId);
        if ($media === null) {
            throw $this->createNotFoundException();
        }

        return new Response((string) $media['content'], Response::HTTP_OK, ['Content-Type' => (string) $media['mime_type'], 'Content-Disposition' => HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_INLINE, basename((string) $media['filename']))]);
    }
}
