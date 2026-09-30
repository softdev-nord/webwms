<?php

declare(strict_types=1);

namespace WebWMS\Integration\Device\Presentation\Web;

use DateTimeImmutable;
use InvalidArgumentException;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;
use WebWMS\Integration\Application\DeviceIntegrationService;
use WebWMS\Integration\Application\Query\IntegrationQueryService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/integration/devices', name: 'v3_device_')]
class DeviceController extends AbstractController
{
    public function __construct(
        private readonly IntegrationQueryService $integrationQueries,
        private readonly DeviceIntegrationService $devices,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('integration.device.read')]
    public function index(): Response
    {
        $tenantId = $this->tenantUser()->tenantId();

        return $this->render('integration/device/index.html.twig', [
            'page' => 'integration.device.index.scanners_and_mobile_terminals',
            'devices' => $this->integrationQueries->devices($tenantId),
            'scanEvents' => $this->integrationQueries->scanEvents($tenantId, 50),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    #[IsGranted('integration.device.write')]
    public function create(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_device_create');
            $user = $this->tenantUser();
            $device = $this->devices->registerDevice(
                $user->tenantId(),
                $this->required($request, 'code'),
                $this->required($request, 'name'),
                $this->required($request, 'device_type'),
                $request->request->getBoolean('active'),
                $user->actorId(),
                new DateTimeImmutable(),
            );
            $this->addFlash('success', 'device.flash.data_capture_device_has_been_created');

            return $this->redirectToRoute('v3_device_show', ['deviceId' => $device->id]);
        }

        return $this->render('integration/device/new.html.twig', ['page' => 'integration.device.new.create_scanning_device']);
    }

    #[Route('/scan', name: 'scan', methods: ['GET', 'POST'])]
    #[IsGranted('integration.device.scan')]
    public function scan(Request $request): Response
    {
        $user = $this->tenantUser();
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_device_scan');
            $event = $this->devices->recordScan(
                $user->tenantId(),
                $this->required($request, 'device_id'),
                $this->required($request, 'scan_type'),
                $this->required($request, 'scan_value'),
                $this->required($request, 'process_type'),
                $this->required($request, 'context_reference'),
                $this->required($request, 'request_id'),
                $request->request->getBoolean('accepted'),
                $this->optional($request, 'message'),
                $user->actorId(),
                new DateTimeImmutable(),
            );
            $this->addFlash('success', 'device.flash.scan_has_been_recorded_idempotently');

            return $this->redirectToRoute('v3_device_scan_show', ['eventId' => $event->id]);
        }

        return $this->render('integration/device/scan.html.twig', [
            'page' => 'integration.device.index.capture_scan',
            'devices' => array_values(array_filter(
                $this->integrationQueries->devices($user->tenantId()),
                static fn (array $device): bool => (bool) ($device['active'] ?? false),
            )),
            'requestId' => Uuid::v7()->toRfc4122(),
        ]);
    }

    #[Route('/scans/{eventId}', name: 'scan_show', methods: ['GET'])]
    #[IsGranted('integration.device.read')]
    public function showScan(string $eventId): Response
    {
        $event = $this->integrationQueries->scanEvent($this->tenantUser()->tenantId(), $eventId);
        if ($event === null) {
            throw $this->createNotFoundException('Das Scanereignis wurde nicht gefunden.');
        }

        return $this->render('integration/device/scan-show.html.twig', [
            'page' => 'integration.device.scan_show.scan_event',
            'event' => $event,
        ]);
    }

    #[Route('/{deviceId}', name: 'show', methods: ['GET'])]
    #[IsGranted('integration.device.read')]
    public function show(string $deviceId): Response
    {
        return $this->render('integration/device/show.html.twig', [
            'page' => 'device.page.data_capture_device',
            'device' => $this->requiredDevice($deviceId),
            'scanEvents' => array_values(array_filter(
                $this->integrationQueries->scanEvents($this->tenantUser()->tenantId()),
                static fn (array $event): bool => ($event['device_id'] ?? null) === $deviceId,
            )),
        ]);
    }

    #[Route('/{deviceId}/status', name: 'status', methods: ['POST'])]
    #[IsGranted('integration.device.write')]
    public function status(string $deviceId, Request $request): RedirectResponse
    {
        $this->assertCsrf($request, 'v3_device_status_' . $deviceId);
        $device = $this->requiredDevice($deviceId);
        $active = !(bool) ($device['active'] ?? false);
        $user = $this->tenantUser();
        $this->devices->changeStatus($user->tenantId(), $deviceId, $active, $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', $active ? 'integration.flash.device_has_been_activated' : 'integration.flash.device_has_been_paused');

        return $this->redirectToRoute('v3_device_show', ['deviceId' => $deviceId]);
    }

    /** @return array<string, mixed> */
    private function requiredDevice(string $deviceId): array
    {
        $device = $this->integrationQueries->device($this->tenantUser()->tenantId(), $deviceId);
        if ($device === null) {
            throw $this->createNotFoundException('Das Erfassungsgerät wurde nicht gefunden.');
        }

        return $device;
    }

    private function required(Request $request, string $field): string
    {
        $value = trim((string) $request->request->get($field));
        if ($value === '') {
            throw new InvalidArgumentException(sprintf('Das Feld "%s" ist erforderlich.', $field));
        }

        return $value;
    }

    private function optional(Request $request, string $field): ?string
    {
        $value = trim((string) $request->request->get($field));

        return $value === '' ? null : $value;
    }

    private function assertCsrf(Request $request, string $id): void
    {
        if (!$this->isCsrfTokenValid($id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Das Formular-Token ist ungültig.');
        }
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
