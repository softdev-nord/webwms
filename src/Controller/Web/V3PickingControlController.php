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
use WebWMS\Fulfillment\Application\AdvancedPickingService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/picking-control', name: 'v3_picking_control_')]
final class V3PickingControlController extends AbstractController
{
    public function __construct(private readonly AdvancedPickingService $picking)
    {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('fulfillment.pick.control')]
    public function index(): Response
    {
        return $this->render('v3/picking/control.html.twig', ['page' => 'Kommissionierleitstand', 'workspace' => $this->picking->workspace($this->user()->tenantId())]);
    }

    #[Route('/waves', name: 'wave_create', methods: ['POST'])]
    #[IsGranted('fulfillment.pick.wave.write')]
    public function createWave(Request $request): Response
    {
        $this->csrf($request, 'v3_pick_wave_create');
        $user = $this->user();
        $planned = trim((string) $request->request->get('planned_start_at'));
        $this->picking->createWave($user->tenantId(), $user->actorId(), $this->required($request, 'code'), $this->required($request, 'name'), $this->required($request, 'strategy'), $this->required($request, 'selection_type'), $this->optional($request, 'selection_value'), max(1, $request->request->getInt('priority', 50)), $planned === '' ? null : new DateTimeImmutable($planned), $this->strings($request, 'pick_list_ids'), new DateTimeImmutable());
        $this->addFlash('success', 'Die Pickwelle wurde geplant.');

        return $this->redirectToRoute('v3_picking_control_index');
    }

    #[Route('/waves/{waveId}/release', name: 'wave_release', methods: ['POST'])]
    #[IsGranted('fulfillment.pick.wave.write')]
    public function release(string $waveId, Request $request): Response
    {
        $this->csrf($request, 'v3_pick_wave_release_' . $waveId);
        $user = $this->user();
        $this->picking->releaseWave($user->tenantId(), $user->actorId(), $waveId, new DateTimeImmutable());
        $this->addFlash('success', 'Die Pickwelle wurde freigegeben.');

        return $this->redirectToRoute('v3_picking_control_index');
    }

    #[Route('/waves/{waveId}/pick-lists/{pickListId}/consolidation', name: 'consolidate', methods: ['POST'])]
    #[IsGranted('fulfillment.pick.execute')]
    public function consolidate(string $waveId, string $pickListId, Request $request): Response
    {
        $this->csrf($request, 'v3_pick_consolidate_' . $waveId . '_' . $pickListId);
        $user = $this->user();
        $this->picking->consolidate($user->tenantId(), $user->actorId(), $waveId, $pickListId, new DateTimeImmutable());
        $this->addFlash('success', 'Der Zielbehälter wurde konsolidiert.');

        return $this->redirectToRoute('v3_picking_control_index');
    }

    private function user(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw new LogicException('The V3 session does not contain a tenant user.');
        }

        return $user;
    }

    private function csrf(Request $request, string $id): void
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

    /** @return list<string> */
    private function strings(Request $request, string $field): array
    {
        return array_values(array_filter($request->request->all($field), 'is_string'));
    }
}
