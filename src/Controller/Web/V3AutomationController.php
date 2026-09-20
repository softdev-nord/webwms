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
use WebWMS\Integration\Application\StorageAutomationAdapter;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/integration/automation', name: 'v3_automation_')]
final class V3AutomationController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly StorageAutomationAdapter $automation,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('integration.automation.read')]
    public function index(): Response
    {
        $tenantId = $this->user()->tenantId();

        return $this->render('v3/integration/automation/index.html.twig', [
            'page' => 'Lagerlifte und Paternoster',
            'devices' => $this->queries->automationDevices($tenantId),
            'commands' => $this->queries->deviceCommands($tenantId, 50),
        ]);
    }

    #[Route('/devices/new', name: 'device_new', methods: ['GET', 'POST'])]
    #[IsGranted('integration.automation.write')]
    public function createDevice(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_automation_device_create');
            $user = $this->user();
            $this->automation->registerDevice(
                $user->tenantId(),
                $this->required($request, 'code'),
                $this->required($request, 'name'),
                $this->required($request, 'device_type'),
                $this->required($request, 'endpoint_url'),
                $this->required($request, 'credential_env'),
                $request->request->getBoolean('active'),
                $user->actorId(),
                new DateTimeImmutable(),
            );
            $this->addFlash('success', 'Das Automationsgerät wurde angelegt.');

            return $this->redirectToRoute('v3_automation_index');
        }

        return $this->render('v3/integration/automation/device-new.html.twig', ['page' => 'Automationsgerät anlegen']);
    }

    #[Route('/devices/{deviceId}/status', name: 'device_status', methods: ['POST'])]
    #[IsGranted('integration.automation.write')]
    public function deviceStatus(string $deviceId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_automation_device_status_' . $deviceId);
        $device = $this->queries->automationDevice($this->user()->tenantId(), $deviceId);
        if ($device === null) {
            throw $this->createNotFoundException('Das Automationsgerät wurde nicht gefunden.');
        }
        $user = $this->user();
        $this->automation->changeDeviceStatus($user->tenantId(), $deviceId, !(bool) $device['active'], $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', (bool) $device['active'] ? 'Das Gerät wurde pausiert.' : 'Das Gerät wurde aktiviert.');

        return $this->redirectToRoute('v3_automation_index');
    }

    #[Route('/commands/new', name: 'command_new', methods: ['GET', 'POST'])]
    #[IsGranted('integration.automation.execute')]
    public function createCommand(Request $request): Response
    {
        $user = $this->user();
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_automation_command_create');
            $command = $this->automation->queueCommand(
                $user->tenantId(),
                $this->required($request, 'device_id'),
                $this->required($request, 'command_type'),
                $this->required($request, 'location_id'),
                $this->required($request, 'reference_type'),
                $this->required($request, 'reference_id'),
                $this->required($request, 'request_id'),
                $user->actorId(),
                new DateTimeImmutable(),
            );
            $this->addFlash('success', 'Der Gerätebefehl wurde idempotent eingereiht.');

            return $this->redirectToRoute('v3_automation_command_show', ['commandId' => $command->id]);
        }

        return $this->render('v3/integration/automation/command-new.html.twig', [
            'page' => 'Gerätebefehl anlegen',
            'devices' => array_values(array_filter(
                $this->queries->automationDevices($user->tenantId()),
                static fn (array $device): bool => (bool) ($device['active'] ?? false),
            )),
            'locations' => $this->queries->automationLocations($user->tenantId()),
            'requestId' => Uuid::v7()->toRfc4122(),
        ]);
    }

    #[Route('/commands/{commandId}', name: 'command_show', methods: ['GET'])]
    #[IsGranted('integration.automation.read')]
    public function showCommand(string $commandId): Response
    {
        $command = $this->queries->deviceCommand($this->user()->tenantId(), $commandId);
        if ($command === null) {
            throw $this->createNotFoundException('Der Gerätebefehl wurde nicht gefunden.');
        }

        return $this->render('v3/integration/automation/command-show.html.twig', [
            'page' => 'Gerätebefehl',
            'command' => $command,
        ]);
    }

    #[Route('/commands/{commandId}/status', name: 'command_status', methods: ['POST'])]
    #[IsGranted('integration.automation.execute')]
    public function commandStatus(string $commandId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_automation_command_status_' . $commandId);
        $user = $this->user();
        $this->automation->transition(
            $user->tenantId(),
            $commandId,
            $this->required($request, 'status'),
            $this->optional($request, 'message'),
            $user->actorId(),
            new DateTimeImmutable(),
        );
        $this->addFlash('success', 'Die Geräterückmeldung wurde verarbeitet.');

        return $this->redirectToRoute('v3_automation_command_show', ['commandId' => $commandId]);
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
