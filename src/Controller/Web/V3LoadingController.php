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
use WebWMS\Inventory\Application\CompleteLoadingManifestCommand;
use WebWMS\Inventory\Application\CompleteLoadingManifestHandler;
use WebWMS\Inventory\Application\ConfirmShipmentLoadingCommand;
use WebWMS\Inventory\Application\ConfirmShipmentLoadingHandler;
use WebWMS\Inventory\Application\CreateLoadingManifestCommand;
use WebWMS\Inventory\Application\CreateLoadingManifestHandler;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/loading', name: 'v3_loading_')]
final class V3LoadingController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly CreateLoadingManifestHandler $createManifest,
        private readonly ConfirmShipmentLoadingHandler $confirmLoading,
        private readonly CompleteLoadingManifestHandler $completeManifest,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('fulfillment.loading.read')]
    public function index(): Response
    {
        return $this->render('v3/loading/index.html.twig', [
            'page' => 'Verladung',
            'manifests' => $this->queries->loadingManifests($this->tenantUser()->tenantId()),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    #[IsGranted('fulfillment.loading.write')]
    public function create(Request $request): Response
    {
        $user = $this->tenantUser();
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_loading_create');
            $manifestId = Uuid::v7()->toRfc4122();
            ($this->createManifest)(new CreateLoadingManifestCommand(
                $manifestId,
                $user->tenantId(),
                $this->required($request, 'code'),
                $this->required($request, 'tour_reference'),
                $this->required($request, 'vehicle_reference'),
                $this->shipmentIds($request),
                $user->actorId(),
                new DateTimeImmutable(),
            ));
            $this->addFlash('success', 'Das Lademanifest wurde angelegt.');

            return $this->redirectToRoute('v3_loading_show', ['manifestId' => $manifestId]);
        }

        return $this->render('v3/loading/new.html.twig', [
            'page' => 'Lademanifest anlegen',
            'shipments' => $this->queries->shipmentsAvailableForLoading($user->tenantId()),
        ]);
    }

    #[Route('/{manifestId}', name: 'show', methods: ['GET'])]
    #[IsGranted('fulfillment.loading.read')]
    public function show(string $manifestId): Response
    {
        return $this->render('v3/loading/show.html.twig', [
            'page' => 'Lademanifest',
            'manifest' => $this->requiredManifest($manifestId),
        ]);
    }

    #[Route('/{manifestId}/shipments/{shipmentId}/loading', name: 'confirm', methods: ['POST'])]
    #[IsGranted('fulfillment.loading.execute')]
    public function confirm(string $manifestId, string $shipmentId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_loading_confirm_' . $shipmentId);
        $this->requiredManifest($manifestId);
        $user = $this->tenantUser();
        ($this->confirmLoading)(new ConfirmShipmentLoadingCommand(
            $manifestId,
            $user->tenantId(),
            $shipmentId,
            $user->actorId(),
            new DateTimeImmutable(),
        ));
        $this->addFlash('success', 'Die Sendung wurde als verladen bestätigt.');

        return $this->redirectToRoute('v3_loading_show', ['manifestId' => $manifestId]);
    }

    #[Route('/{manifestId}/complete', name: 'complete', methods: ['POST'])]
    #[IsGranted('fulfillment.loading.execute')]
    public function complete(string $manifestId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_loading_complete_' . $manifestId);
        $this->requiredManifest($manifestId);
        $user = $this->tenantUser();
        ($this->completeManifest)(new CompleteLoadingManifestCommand(
            $manifestId,
            $user->tenantId(),
            $user->actorId(),
            new DateTimeImmutable(),
        ));
        $this->addFlash('success', 'Die Verladung wurde abgeschlossen.');

        return $this->redirectToRoute('v3_loading_show', ['manifestId' => $manifestId]);
    }

    /** @return array<string, mixed> */
    private function requiredManifest(string $manifestId): array
    {
        $manifest = $this->queries->loadingManifest($this->tenantUser()->tenantId(), $manifestId);
        if ($manifest === null) {
            throw $this->createNotFoundException('Das Lademanifest wurde nicht gefunden.');
        }

        return $manifest;
    }

    /** @return list<string> */
    private function shipmentIds(Request $request): array
    {
        $shipmentIds = [];
        foreach ($request->request->all('shipment_id') as $shipmentId) {
            if (!is_string($shipmentId) || trim($shipmentId) === '') {
                throw new \InvalidArgumentException('Die ausgewählten Sendungen sind ungültig.');
            }
            $shipmentIds[] = trim($shipmentId);
        }
        if ($shipmentIds === []) {
            throw new \InvalidArgumentException('Mindestens eine Sendung muss ausgewählt werden.');
        }

        return $shipmentIds;
    }

    private function required(Request $request, string $field): string
    {
        $value = trim((string) $request->request->get($field));
        if ($value === '') {
            throw new \InvalidArgumentException(sprintf('Das Feld "%s" ist erforderlich.', $field));
        }

        return $value;
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
