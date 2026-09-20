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
use WebWMS\Integration\Application\MeasurementService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/integration/measurements', name: 'v3_measurement_')]
final class V3MeasurementController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly MeasurementService $measurements,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('integration.measurement.read')]
    public function index(): Response
    {
        $tenantId = $this->user()->tenantId();

        return $this->render('v3/integration/measurement/index.html.twig', [
            'page' => 'Waagen und Volumenmessung',
            'devices' => $this->queries->measurementDevices($tenantId),
            'measurements' => $this->queries->measurements($tenantId, 50),
        ]);
    }

    #[Route('/devices/new', name: 'device_new', methods: ['GET', 'POST'])]
    #[IsGranted('integration.measurement.write')]
    public function createDevice(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_measurement_device_create');
            $user = $this->user();
            $this->measurements->registerDevice(
                $user->tenantId(),
                $this->required($request, 'code'),
                $this->required($request, 'name'),
                $this->required($request, 'device_type'),
                $request->request->getBoolean('active'),
                $user->actorId(),
                new DateTimeImmutable(),
            );
            $this->addFlash('success', 'Das Messgerät wurde angelegt.');

            return $this->redirectToRoute('v3_measurement_index');
        }

        return $this->render('v3/integration/measurement/device-new.html.twig', ['page' => 'Messgerät anlegen']);
    }

    #[Route('/devices/{deviceId}/status', name: 'device_status', methods: ['POST'])]
    #[IsGranted('integration.measurement.write')]
    public function status(string $deviceId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_measurement_device_status_' . $deviceId);
        $device = $this->queries->measurementDevice($this->user()->tenantId(), $deviceId);
        if ($device === null) {
            throw $this->createNotFoundException('Das Messgerät wurde nicht gefunden.');
        }
        $user = $this->user();
        $this->measurements->changeStatus(
            $user->tenantId(),
            $deviceId,
            !(bool) $device['active'],
            $user->actorId(),
            new DateTimeImmutable(),
        );
        $this->addFlash('success', (bool) $device['active'] ? 'Das Messgerät wurde pausiert.' : 'Das Messgerät wurde aktiviert.');

        return $this->redirectToRoute('v3_measurement_index');
    }

    #[Route('/capture', name: 'capture', methods: ['GET', 'POST'])]
    #[IsGranted('integration.measurement.capture')]
    public function capture(Request $request): Response
    {
        $user = $this->user();
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_measurement_capture');
            $measurement = $this->measurements->record(
                $user->tenantId(),
                $this->required($request, 'device_id'),
                $this->required($request, 'target_type'),
                $this->required($request, 'target_id'),
                $this->positiveInt($request, 'weight_grams'),
                $this->positiveInt($request, 'length_mm'),
                $this->positiveInt($request, 'width_mm'),
                $this->positiveInt($request, 'height_mm'),
                $this->required($request, 'request_id'),
                $request->request->getBoolean('accepted'),
                $this->optional($request, 'message'),
                $user->actorId(),
                new DateTimeImmutable(),
            );
            $this->addFlash('success', 'Die Messung wurde idempotent verarbeitet.');

            return $this->redirectToRoute('v3_measurement_show', ['measurementId' => $measurement->id]);
        }

        return $this->render('v3/integration/measurement/capture.html.twig', [
            'page' => 'Messung erfassen',
            'devices' => array_values(array_filter(
                $this->queries->measurementDevices($user->tenantId()),
                static fn (array $device): bool => (bool) ($device['active'] ?? false),
            )),
            'packages' => $this->queries->measurablePackages($user->tenantId()),
            'products' => $this->queries->products($user->tenantId(), 200, null),
            'requestId' => Uuid::v7()->toRfc4122(),
        ]);
    }

    #[Route('/{measurementId}', name: 'show', methods: ['GET'])]
    #[IsGranted('integration.measurement.read')]
    public function show(string $measurementId): Response
    {
        $measurement = $this->queries->measurement($this->user()->tenantId(), $measurementId);
        if ($measurement === null) {
            throw $this->createNotFoundException('Die Messung wurde nicht gefunden.');
        }

        return $this->render('v3/integration/measurement/show.html.twig', [
            'page' => 'Messung',
            'measurement' => $measurement,
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

    private function positiveInt(Request $request, string $field): ?int
    {
        $value = trim((string) $request->request->get($field));
        if ($value === '') {
            return null;
        }
        if (filter_var($value, FILTER_VALIDATE_INT) === false || (int) $value <= 0) {
            throw new \InvalidArgumentException(sprintf('Das Feld "%s" muss eine positive Ganzzahl enthalten.', $field));
        }

        return (int) $value;
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
