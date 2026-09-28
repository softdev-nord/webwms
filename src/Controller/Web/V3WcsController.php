<?php

declare(strict_types=1);

namespace WebWMS\Controller\Web;

use DateTimeImmutable;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;
use WebWMS\Integration\Application\ApiV3QueryService;
use WebWMS\Integration\Application\WcsIntegrationService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/integration/wcs', name: 'v3_wcs_')]
final class V3WcsController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly WcsIntegrationService $wcs,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('integration.wcs.read')]
    public function index(): Response
    {
        $tenantId = $this->user()->tenantId();

        return $this->render('v3/integration/wcs/index.html.twig', [
            'page' => 'ui.v3.integration.wcs.index.wcs.mfr.und.fordertechnik.0796567',
            'connections' => $this->queries->wcsConnections($tenantId),
            'commands' => $this->queries->machineCommands($tenantId, 50),
            'statuses' => $this->queries->machineStatuses($tenantId, 50),
        ]);
    }

    #[Route('/connections/new', name: 'connection_new', methods: ['GET', 'POST'])]
    #[IsGranted('integration.wcs.write')]
    public function createConnection(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_wcs_connection_create');
            $user = $this->user();
            $this->wcs->registerConnection($user->tenantId(), $this->required($request, 'code'), $this->required($request, 'name'), $this->required($request, 'system_type'), $this->required($request, 'endpoint_url'), $this->required($request, 'credential_env'), $request->request->getBoolean('active'), $user->actorId(), new DateTimeImmutable());
            $this->addFlash('success', 'controller.v3wcs.flash.die.wcs.verbindung.wurde.angelegt.013f60a');

            return $this->redirectToRoute('v3_wcs_index');
        }

        return $this->render('v3/integration/wcs/connection-new.html.twig', ['page' => 'ui.v3.integration.wcs.connection-new.wcs.verbindung.anlegen.58e1602']);
    }

    #[Route('/connections/{connectionId}/status', name: 'connection_status', methods: ['POST'])]
    #[IsGranted('integration.wcs.write')]
    public function connectionStatus(string $connectionId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_wcs_connection_status_' . $connectionId);
        $connection = $this->queries->wcsConnection($this->user()->tenantId(), $connectionId);
        if ($connection === null) {
            throw $this->createNotFoundException('Die WCS-Verbindung wurde nicht gefunden.');
        }
        $user = $this->user();
        $this->wcs->changeConnectionStatus($user->tenantId(), $connectionId, !(bool) $connection['active'], $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', (bool) $connection['active'] ? 'flash.connection.paused' : 'flash.connection.activated');

        return $this->redirectToRoute('v3_wcs_index');
    }

    #[Route('/commands/new', name: 'command_new', methods: ['GET', 'POST'])]
    #[IsGranted('integration.wcs.execute')]
    public function createCommand(Request $request): Response
    {
        $user = $this->user();
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_wcs_command_create');
            $command = $this->wcs->queueCommand($user->tenantId(), $this->required($request, 'connection_id'), $this->required($request, 'command_type'), $this->required($request, 'source'), $this->required($request, 'destination'), $this->required($request, 'load_unit'), $this->required($request, 'request_id'), $user->actorId(), new DateTimeImmutable());
            $this->addFlash('success', 'controller.v3wcs.flash.der.maschinenbefehl.wurde.idempotent.eingereiht.4ca9825');

            return $this->redirectToRoute('v3_wcs_command_show', ['commandId' => $command->id]);
        }

        return $this->render('v3/integration/wcs/command-new.html.twig', [
            'page' => 'ui.v3.integration.wcs.command-new.maschinenbefehl.anlegen.3850248',
            'connections' => array_values(array_filter($this->queries->wcsConnections($user->tenantId()), static fn (array $connection): bool => (bool) ($connection['active'] ?? false))),
            'requestId' => Uuid::v7()->toRfc4122(),
        ]);
    }

    #[Route('/commands/{commandId}', name: 'command_show', methods: ['GET'])]
    #[IsGranted('integration.wcs.read')]
    public function showCommand(string $commandId): Response
    {
        $command = $this->queries->machineCommand($this->user()->tenantId(), $commandId);
        if ($command === null) {
            throw $this->createNotFoundException('Der Maschinenbefehl wurde nicht gefunden.');
        }

        return $this->render('v3/integration/wcs/command-show.html.twig', ['page' => 'ui.v3.integration.wcs.command-show.maschinenbefehl.4c31388', 'command' => $command]);
    }

    #[Route('/commands/{commandId}/status', name: 'command_status', methods: ['POST'])]
    #[IsGranted('integration.wcs.execute')]
    public function commandStatus(string $commandId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_wcs_command_status_' . $commandId);
        $user = $this->user();
        $this->wcs->transitionCommand($user->tenantId(), $commandId, $this->required($request, 'status'), $this->optional($request, 'message'), $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', 'controller.v3wcs.flash.der.befehlsstatus.wurde.verarbeitet.54697b5');

        return $this->redirectToRoute('v3_wcs_command_show', ['commandId' => $commandId]);
    }

    #[Route('/statuses/new', name: 'status_new', methods: ['GET', 'POST'])]
    #[IsGranted('integration.wcs.execute')]
    public function createMachineStatus(Request $request): Response
    {
        $user = $this->user();
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_wcs_status_create');
            $this->wcs->recordStatus($user->tenantId(), $this->required($request, 'connection_id'), $this->optional($request, 'command_id'), $this->required($request, 'machine_code'), $this->required($request, 'status'), $this->optional($request, 'message'), $this->required($request, 'external_event_id'), $user->actorId(), new DateTimeImmutable());
            $this->addFlash('success', 'controller.v3wcs.flash.der.maschinenstatus.wurde.idempotent.erfasst.3a445b0');

            return $this->redirectToRoute('v3_wcs_index');
        }

        return $this->render('v3/integration/wcs/status-new.html.twig', [
            'page' => 'ui.v3.integration.wcs.status-new.maschinenstatus.erfassen.71c0460',
            'connections' => array_values(array_filter($this->queries->wcsConnections($user->tenantId()), static fn (array $connection): bool => (bool) ($connection['active'] ?? false))),
            'commands' => $this->queries->machineCommands($user->tenantId(), 100),
            'eventId' => Uuid::v7()->toRfc4122(),
        ]);
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
}
