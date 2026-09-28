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
use WebWMS\Fulfillment\Application\AdvancedPickingService;
use WebWMS\Integration\Application\ApiV3QueryService;
use WebWMS\Inventory\Application\AssignPickListCommand;
use WebWMS\Inventory\Application\AssignPickListHandler;
use WebWMS\Inventory\Application\ConfirmPickTaskCommand;
use WebWMS\Inventory\Application\ConfirmPickTaskHandler;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/picking', name: 'v3_picking_')]
final class V3PickingController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly AssignPickListHandler $assignPickList,
        private readonly ConfirmPickTaskHandler $confirmPickTask,
        private readonly AdvancedPickingService $advancedPicking,
    ) {
    }

    #[Route('/{pickListId}/route-optimization', name: 'optimize', methods: ['POST'])]
    #[IsGranted('fulfillment.pick.optimize')]
    public function optimize(string $pickListId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_pick_optimize_' . $pickListId);
        $user = $this->tenantUser();
        $this->advancedPicking->optimizeRoute($user->tenantId(), $user->actorId(), $pickListId, new DateTimeImmutable());
        $this->addFlash('success', 'controller.v3picking.flash.die.pickroute.wurde.anhand.der.lagertopologie.optimiert.d6d1c9c');

        return $this->redirectToRoute('v3_picking_show', ['pickListId' => $pickListId]);
    }

    #[Route('/{pickListId}/tasks/{taskId}/scan', name: 'scan', methods: ['POST'])]
    #[IsGranted('fulfillment.pick.execute')]
    public function scan(string $pickListId, string $taskId, Request $request): Response
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
        return $this->render('v3/picking/index.html.twig', [
            'page' => 'ui.v3.picking.index.picking.7c3a00c',
            'pickLists' => $this->queries->pickLists($this->tenantUser()->tenantId()),
        ]);
    }

    #[Route('/{pickListId}', name: 'show', methods: ['GET'])]
    #[IsGranted('fulfillment.pick.read')]
    public function show(string $pickListId): Response
    {
        return $this->render('v3/picking/show.html.twig', [
            'page' => 'ui.v3.outbound.control.pickliste.77bddf1',
            'pickList' => $this->requiredPickList($pickListId),
        ]);
    }

    #[Route('/{pickListId}/assignment', name: 'assign', methods: ['POST'])]
    #[IsGranted('fulfillment.pick.assign')]
    public function assign(string $pickListId, Request $request): Response
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
        $this->addFlash('success', 'controller.v3picking.flash.die.pickliste.wurde.dir.zugewiesen.49ef502');

        return $this->redirectToRoute('v3_picking_show', ['pickListId' => $pickListId]);
    }

    #[Route('/{pickListId}/tasks/{taskId}/confirmation', name: 'confirm', methods: ['POST'])]
    #[IsGranted('fulfillment.pick.execute')]
    public function confirm(string $pickListId, string $taskId, Request $request): Response
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
        $this->addFlash('success', $outcome === 'picked' ? 'flash.picking.position_picked' : 'flash.picking.shortage_recorded');

        return $this->redirectToRoute('v3_picking_show', ['pickListId' => $pickListId]);
    }

    /** @return array<string, mixed> */
    private function requiredPickList(string $pickListId): array
    {
        $pickList = $this->queries->pickList($this->tenantUser()->tenantId(), $pickListId);
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
        foreach ($tasks as $task) {
            if (is_array($task) && ($task['id'] ?? null) === $taskId) {
                return true;
            }
        }

        return false;
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
            throw new \InvalidArgumentException(sprintf('Das Feld "%s" ist erforderlich.', $field));
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
