<?php

declare(strict_types=1);

namespace WebWMS\Controller\Web;

use DateTimeImmutable;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use WebWMS\Platform\Application\PlatformControlService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/platform', name: 'v3_platform_')]
#[IsGranted('platform.read')]
final class V3PlatformController extends AbstractController
{
    public function __construct(private readonly PlatformControlService $platform)
    {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $user = $this->user();
        $query = trim((string) $request->query->get('q'));

        return $this->render('v3/platform/index.html.twig', ['page' => 'Platform Control Center', 'platform' => $this->platform->workspace($user->tenantId()), 'query' => $query, 'searchResults' => $query === '' ? [] : $this->platform->search($user->tenantId(), $query)]);
    }

    #[Route('/resources/{resource}', name: 'create', requirements: ['resource' => 'task|kpi|dashboard|partner_account|automation_rule|storage_fee_rule|service|print_route'], methods: ['POST'])]
    #[IsGranted('platform.write')]
    public function create(string $resource, Request $request): Response
    {
        $this->csrf($request, 'v3_platform_create_' . $resource);
        $user = $this->user();
        $this->platform->create($user->tenantId(), $user->actorId(), $resource, $request->request->all(), new DateTimeImmutable());
        $this->addFlash('success', 'Die Plattformkonfiguration wurde gespeichert.');

        return $this->back();
    }

    #[Route('/tasks/{taskId}/status', name: 'task_status', methods: ['POST'])]
    #[IsGranted('platform.execute')]
    public function taskStatus(string $taskId, Request $request): Response
    {
        $this->csrf($request, 'v3_platform_task_' . $taskId);
        $user = $this->user();
        $this->platform->transitionTask($user->tenantId(), $user->actorId(), $taskId, $this->required($request, 'status'), new DateTimeImmutable());
        $this->addFlash('success', 'Der Shopfloor-Status wurde aktualisiert.');

        return $this->back();
    }

    #[Route('/events', name: 'event', methods: ['POST'])]
    #[IsGranted('platform.execute')]
    public function event(Request $request): Response
    {
        $this->csrf($request, 'v3_platform_event');
        $user = $this->user();
        $payload = json_decode($this->required($request, 'payload'), true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($payload)) {
            throw new \InvalidArgumentException('Der Event-Payload muss ein JSON-Objekt sein.');
        }
        $count = $this->platform->executeEvent($user->tenantId(), $user->actorId(), $this->required($request, 'event_name'), $payload, new DateTimeImmutable());
        $this->addFlash('success', sprintf('%d Automationsregeln wurden ausgewertet.', $count));

        return $this->back();
    }

    #[Route('/billing/storage', name: 'bill_storage', methods: ['POST'])]
    #[IsGranted('platform.execute')]
    public function billStorage(Request $request): Response
    {
        $this->csrf($request, 'v3_platform_bill_storage');
        $user = $this->user();
        $this->platform->billStorage($user->tenantId(), $user->actorId(), $this->required($request, 'rule_id'), (float) $this->required($request, 'quantity'), $request->request->getInt('days'), $this->required($request, 'reference'), new DateTimeImmutable());
        $this->addFlash('success', 'Die Lagergeldposition wurde berechnet.');

        return $this->back();
    }

    #[Route('/billing/service', name: 'bill_service', methods: ['POST'])]
    #[IsGranted('platform.execute')]
    public function billService(Request $request): Response
    {
        $this->csrf($request, 'v3_platform_bill_service');
        $user = $this->user();
        $this->platform->billService($user->tenantId(), $user->actorId(), $this->required($request, 'service_id'), $this->required($request, 'business_partner_id'), (float) $this->required($request, 'quantity'), $this->required($request, 'reference'), new DateTimeImmutable());
        $this->addFlash('success', 'Die Zusatzleistung wurde erfasst.');

        return $this->back();
    }

    #[Route('/media', name: 'media', methods: ['POST'])]
    #[IsGranted('platform.write')]
    public function media(Request $request): Response
    {
        $this->csrf($request, 'v3_platform_media');
        $file = $request->files->get('file');
        if ($file === null || !$file->isValid()) {
            throw new \InvalidArgumentException('Eine gültige Aufnahme ist erforderlich.');
        }
        $user = $this->user();
        $this->platform->captureMedia($user->tenantId(), $user->actorId(), $this->required($request, 'aggregate_type'), $this->required($request, 'aggregate_id'), $file->getClientOriginalName(), (string) $file->getMimeType(), (string) file_get_contents($file->getPathname()), new DateTimeImmutable());
        $this->addFlash('success', 'Die Aufnahme wurde revisionssicher zugeordnet.');

        return $this->back();
    }

    #[Route('/media/{mediaId}', name: 'media_show', methods: ['GET'])]
    public function showMedia(string $mediaId): Response
    {
        $media = $this->platform->media($this->user()->tenantId(), $mediaId);
        if ($media === null) {
            throw $this->createNotFoundException();
        }

        return new Response((string) $media['content'], Response::HTTP_OK, ['Content-Type' => (string) $media['mime_type'], 'Content-Disposition' => HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_INLINE, basename((string) $media['filename']))]);
    }

    private function user(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw new LogicException('The V3 session does not contain a tenant user.');
        }

        return $user;
    }

    private function required(Request $request, string $field): string
    {
        $value = trim((string) $request->request->get($field));
        if ($value === '') {
            throw new \InvalidArgumentException(sprintf('Das Feld "%s" ist erforderlich.', $field));
        }

        return $value;
    }

    private function csrf(Request $request, string $id): void
    {
        if (!$this->isCsrfTokenValid($id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Das Formular-Token ist ungültig.');
        }
    }

    private function back(): Response
    {
        return $this->redirectToRoute('v3_platform_index');
    }
}
