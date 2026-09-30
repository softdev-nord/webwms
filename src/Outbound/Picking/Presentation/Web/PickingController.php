<?php

declare(strict_types=1);

namespace WebWMS\Outbound\Picking\Presentation\Web;

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
use WebWMS\Inventory\Application\AssignPickListCommand;
use WebWMS\Inventory\Application\AssignPickListHandler;
use WebWMS\Inventory\Application\ConfirmPickTaskCommand;
use WebWMS\Inventory\Application\ConfirmPickTaskHandler;
use WebWMS\Outbound\Application\Query\OutboundQueryService;
use WebWMS\Outbound\Picking\Application\AdvancedPickingService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/picking', name: 'v3_picking_')]
class PickingController extends AbstractController
{
    public function __construct(
        private readonly OutboundQueryService $outboundQueries,
        private readonly AssignPickListHandler $assignPickList,
        private readonly ConfirmPickTaskHandler $confirmPickTask,
        private readonly AdvancedPickingService $advancedPicking,
    ) {
    }

    #[Route('/{pickListId}/route-optimization', name: 'optimize', methods: ['POST'])]
    #[IsGranted('fulfillment.pick.optimize')]
    public function optimize(string $pickListId, Request $request): RedirectResponse
    {
        $this->assertCsrf($request, 'v3_pick_optimize_' . $pickListId);
        $user = $this->tenantUser();
        $this->advancedPicking->optimizeRoute($user->tenantId(), $user->actorId(), $pickListId, new DateTimeImmutable());
        $this->addFlash('success', 'picking.flash.pick_route_was_optimized_based_on_warehouse_topology');

        return $this->redirectToRoute('v3_picking_show', ['pickListId' => $pickListId]);
    }

    #[Route('/{pickListId}/tasks/{taskId}/scan', name: 'scan', methods: ['POST'])]
    #[IsGranted('fulfillment.pick.execute')]
    public function scan(string $pickListId, string $taskId, Request $request): RedirectResponse
    {
        $this->assertCsrf($request, 'v3_pick_scan_' . $taskId);
        $pickList = $this->requiredPickList($pickListId);
        if (!$this->containsTask($pickList, $taskId)) {
            throw $this->createNotFoundException('Die Pickposition wurde nicht gefunden.');
        }

        $user = $this->tenantUser();
        $result = $this->advancedPicking->validateScan($user->tenantId(), $user->actorId(), $taskId, $this->required($request, 'location'), $this->required($request, 'product'), $this->optional($request, 'batch'), $this->optional($request, 'serial'), $request->request->getInt('quantity'), new DateTimeImmutable());
        if (!$result['valid']) {
            $this->addFlash('danger', $result['message']);

            return $this->redirectToRoute('v3_picking_show', ['pickListId' => $pickListId]);
        }

        ($this->confirmPickTask)(new ConfirmPickTaskCommand($taskId, $user->tenantId(), 'picked', Uuid::v7()->toRfc4122(), 'Scannerbestätigt', $user->actorId(), new DateTimeImmutable()));
        $this->addFlash('success', $result['message']);

        return $this->redirectToRoute('v3_picking_show', ['pickListId' => $pickListId]);
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('fulfillment.pick.read')]
    public function index(): Response
    {
        return $this->render('outbound/picking/index.html.twig', [
            'page' => 'picking.index.picking',
            'pickLists' => $this->outboundQueries->pickLists($this->tenantUser()->tenantId()),
        ]);
    }

    #[Route('/{pickListId}', name: 'show', methods: ['GET'])]
    #[IsGranted('fulfillment.pick.read')]
    public function show(string $pickListId): Response
    {
        return $this->render('outbound/picking/show.html.twig', [
            'page' => 'outbound.control.pick_list',
            'pickList' => $this->requiredPickList($pickListId),
        ]);
    }

    #[Route('/{pickListId}/assignment', name: 'assign', methods: ['POST'])]
    #[IsGranted('fulfillment.pick.assign')]
    public function assign(string $pickListId, Request $request): RedirectResponse
    {
        $this->assertCsrf($request, 'v3_pick_assign_' . $pickListId);
        $this->requiredPickList($pickListId);
        $user = $this->tenantUser();
        ($this->assignPickList)(new AssignPickListCommand(
            $pickListId,
            $user->tenantId(),
            $user->actorId(),
            $user->actorId(),
            new DateTimeImmutable(),
        ));
        $this->addFlash('success', 'picking.flash.pick_list_was_assigned_to_you');

        return $this->redirectToRoute('v3_picking_show', ['pickListId' => $pickListId]);
    }

    #[Route('/{pickListId}/tasks/{taskId}/confirmation', name: 'confirm', methods: ['POST'])]
    #[IsGranted('fulfillment.pick.execute')]
    public function confirm(string $pickListId, string $taskId, Request $request): RedirectResponse
    {
        $this->assertCsrf($request, 'v3_pick_confirm_' . $taskId);
        $pickList = $this->requiredPickList($pickListId);
        if (!$this->containsTask($pickList, $taskId)) {
            throw $this->createNotFoundException('Die Pickposition wurde nicht gefunden.');
        }

        $user = $this->tenantUser();
        $outcome = trim((string) $request->request->get('outcome'));
        ($this->confirmPickTask)(new ConfirmPickTaskCommand(
            $taskId,
            $user->tenantId(),
            $outcome,
            $outcome === 'picked' ? Uuid::v7()->toRfc4122() : null,
            trim((string) $request->request->get('note')),
            $user->actorId(),
            new DateTimeImmutable(),
        ));
        $this->addFlash('success', $outcome === 'picked' ? 'picking.flash.item_has_been_picked' : 'picking.flash.shortage_has_been_recorded');

        return $this->redirectToRoute('v3_picking_show', ['pickListId' => $pickListId]);
    }

    /** @return array<string, mixed> */
    private function requiredPickList(string $pickListId): array
    {
        $pickList = $this->outboundQueries->pickList($this->tenantUser()->tenantId(), $pickListId);
        if ($pickList === null) {
            throw $this->createNotFoundException('Die Pickliste wurde nicht gefunden.');
        }

        return $pickList;
    }

    /** @param array<string, mixed> $pickList */
    private function containsTask(array $pickList, string $taskId): bool
    {
        $tasks = $pickList['tasks'] ?? [];
        if (!is_array($tasks)) {
            return false;
        }


        return array_any($tasks, fn ($task): bool => is_array($task) && ($task['id'] ?? null) === $taskId);
    }

    private function assertCsrf(Request $request, string $id): void
    {
        if (!$this->isCsrfTokenValid($id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Das Formular-Token ist ungültig.');
        }
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

    private function tenantUser(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw new LogicException('The V3 session does not contain a tenant user.');
        }

        return $user;
    }
}
