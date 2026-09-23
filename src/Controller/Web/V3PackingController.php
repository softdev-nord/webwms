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
use WebWMS\Inventory\Application\AddPackingPackageCommand;
use WebWMS\Inventory\Application\AddPackingPackageHandler;
use WebWMS\Inventory\Application\CompletePackingOrderCommand;
use WebWMS\Inventory\Application\CompletePackingOrderHandler;
use WebWMS\Inventory\Application\CreatePackingOrderCommand;
use WebWMS\Inventory\Application\CreatePackingOrderHandler;
use WebWMS\Inventory\Application\OutboundProcessService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/packing', name: 'v3_packing_')]
final class V3PackingController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly CreatePackingOrderHandler $createPackingOrder,
        private readonly AddPackingPackageHandler $addPackage,
        private readonly CompletePackingOrderHandler $completePackingOrder,
        private readonly OutboundProcessService $outboundProcesses,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('fulfillment.pack.read')]
    public function index(): Response
    {
        return $this->render('v3/packing/index.html.twig', [
            'page' => 'Packen',
            'packingOrders' => $this->queries->packingOrders($this->tenantUser()->tenantId()),
        ]);
    }

    #[Route('/from-pick-list/{pickListId}', name: 'create', methods: ['POST'])]
    #[IsGranted('fulfillment.pack.write')]
    public function create(string $pickListId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_packing_create_' . $pickListId);
        $user = $this->tenantUser();
        $pickList = $this->queries->pickList($user->tenantId(), $pickListId);
        if ($pickList === null || ($pickList['status'] ?? null) !== 'completed') {
            throw $this->createNotFoundException('Eine abgeschlossene Pickliste ist erforderlich.');
        }
        if ($this->queries->outboundQualityDecision($user->tenantId(), $pickListId) !== 'released') {
            throw new \DomainException('Vor dem Packen muss die Ausgangs-QS die Pickliste freigeben.');
        }
        $packingOrderId = Uuid::v7()->toRfc4122();
        ($this->createPackingOrder)(new CreatePackingOrderCommand(
            $packingOrderId,
            $user->tenantId(),
            $pickListId,
            $this->required($request, 'code'),
            $user->actorId(),
            new DateTimeImmutable(),
        ));
        $this->addFlash('success', 'Der Packauftrag wurde angelegt.');

        return $this->redirectToRoute('v3_packing_show', ['packingOrderId' => $packingOrderId]);
    }

    #[Route('/{packingOrderId}', name: 'show', methods: ['GET'])]
    #[IsGranted('fulfillment.pack.read')]
    public function show(string $packingOrderId): Response
    {
        $user = $this->tenantUser();

        return $this->render('v3/packing/show.html.twig', [
            'page' => 'Packauftrag',
            'packingOrder' => $this->requiredPackingOrder($packingOrderId),
            'packableTasks' => $this->queries->packablePickTasks($user->tenantId(), $packingOrderId),
        ]);
    }

    #[Route('/{packingOrderId}/packages', name: 'package', methods: ['POST'])]
    #[IsGranted('fulfillment.pack.write')]
    public function package(string $packingOrderId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_packing_package_' . $packingOrderId);
        $this->requiredPackingOrder($packingOrderId);
        $taskIds = [];
        foreach ($request->request->all('pick_task_id') as $taskId) {
            if (!is_string($taskId) || trim($taskId) === '') {
                throw new \InvalidArgumentException('Die ausgewählten Pickpositionen sind ungültig.');
            }
            $taskIds[] = $taskId;
        }
        if ($taskIds === []) {
            throw new \InvalidArgumentException('Mindestens eine Pickposition muss ausgewählt werden.');
        }
        $user = $this->tenantUser();
        $weight = $this->positiveInt($request, 'weight_grams');
        $this->outboundProcesses->assertPackageWeight($user->tenantId(), $weight);
        ($this->addPackage)(new AddPackingPackageCommand(
            Uuid::v7()->toRfc4122(),
            $packingOrderId,
            $user->tenantId(),
            $this->required($request, 'package_number'),
            $weight,
            $taskIds,
            $user->actorId(),
            new DateTimeImmutable(),
        ));
        $this->addFlash('success', 'Das Paket wurde versiegelt.');

        return $this->redirectToRoute('v3_packing_show', ['packingOrderId' => $packingOrderId]);
    }

    #[Route('/{packingOrderId}/complete', name: 'complete', methods: ['POST'])]
    #[IsGranted('fulfillment.pack.execute')]
    public function complete(string $packingOrderId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_packing_complete_' . $packingOrderId);
        $this->requiredPackingOrder($packingOrderId);
        $user = $this->tenantUser();
        ($this->completePackingOrder)(new CompletePackingOrderCommand(
            $packingOrderId,
            $user->tenantId(),
            $user->actorId(),
            new DateTimeImmutable(),
        ));
        $this->addFlash('success', 'Der Packauftrag wurde abgeschlossen.');

        return $this->redirectToRoute('v3_packing_show', ['packingOrderId' => $packingOrderId]);
    }

    /** @return array<string, mixed> */
    private function requiredPackingOrder(string $packingOrderId): array
    {
        $order = $this->queries->packingOrder($this->tenantUser()->tenantId(), $packingOrderId);
        if ($order === null) {
            throw $this->createNotFoundException('Der Packauftrag wurde nicht gefunden.');
        }

        return $order;
    }

    private function required(Request $request, string $field): string
    {
        $value = trim((string) $request->request->get($field));
        if ($value === '') {
            throw new \InvalidArgumentException(sprintf('Das Feld "%s" ist erforderlich.', $field));
        }

        return $value;
    }

    private function positiveInt(Request $request, string $field): int
    {
        $value = $this->required($request, $field);
        if (!ctype_digit($value) || (int) $value <= 0) {
            throw new \InvalidArgumentException(sprintf('Das Feld "%s" muss eine positive Ganzzahl sein.', $field));
        }

        return (int) $value;
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
