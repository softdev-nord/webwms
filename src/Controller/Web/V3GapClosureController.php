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
use WebWMS\Platform\Application\GapClosureService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/parity', name: 'v3_parity_')]
#[IsGranted('platform.parity.read')]
final class V3GapClosureController extends AbstractController
{
    public function __construct(private readonly GapClosureService $service)
    {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('v3/parity/index.html.twig', ['workspace' => $this->service->workspace($this->user()->tenantId())]);
    }

    #[Route('/configurations/{resource}/new', name: 'configuration_new', methods: ['GET', 'POST'])]
    #[Route('/configurations/{resource}/{id}/edit', name: 'configuration_edit', methods: ['GET', 'POST'])]
    #[IsGranted('platform.parity.write')]
    public function configuration(string $resource, Request $request, ?string $id = null): Response
    {
        $user = $this->user();
        $configuration = $id === null ? null : $this->service->configuration($user->tenantId(), $resource, $id);
        if ($request->isMethod('POST')) {
            $this->csrf($request, 'v3_parity_configuration');
            $payload = $this->decodeJsonObject((string) $request->request->get('configuration_json'));
            $this->service->saveConfiguration($user->tenantId(), $user->actorId(), $resource, $id, $this->required($request, 'code'), $this->required($request, 'name'), $payload, $request->request->getBoolean('active'), new DateTimeImmutable());
            $this->addFlash('success', 'Die Konfiguration wurde gespeichert.');

            return $this->redirectToRoute('v3_parity_index');
        }

        return $this->render('v3/parity/configuration_form.html.twig', ['resource' => $resource, 'label' => GapClosureService::RESOURCES[$resource] ?? $resource, 'configuration' => $configuration]);
    }

    #[Route('/work-items/{workflow}/new', name: 'work_item_new', methods: ['GET', 'POST'])]
    #[IsGranted('platform.parity.execute')]
    public function newWorkItem(string $workflow, Request $request): Response
    {
        if (!isset(GapClosureService::WORKFLOWS[$workflow])) {
            throw $this->createNotFoundException();
        }
        if ($request->isMethod('POST')) {
            $this->csrf($request, 'v3_parity_work_item');
            $user = $this->user();
            $id = $this->service->createWorkItem($user->tenantId(), $user->actorId(), $workflow, $this->required($request, 'reference'), $this->decodeJsonObject((string) $request->request->get('payload_json')), new DateTimeImmutable());
            $this->addFlash('success', 'Der Vorgang wurde angelegt.');

            return $this->redirectToRoute('v3_parity_work_item_show', ['id' => $id]);
        }

        return $this->render('v3/parity/work_item_form.html.twig', ['workflow' => $workflow, 'label' => GapClosureService::WORKFLOWS[$workflow]]);
    }

    #[Route('/work-items/{id}', name: 'work_item_show', methods: ['GET'])]
    public function showWorkItem(string $id): Response
    {
        $item = $this->service->workItem($this->user()->tenantId(), $id);

        return $this->render('v3/parity/work_item_show.html.twig', ['item' => $item, 'transitions' => $this->service->allowedTransitions((string) $item['workflow_type'], (string) $item['status'])]);
    }

    #[Route('/work-items/{id}/transition', name: 'work_item_transition', methods: ['POST'])]
    #[IsGranted('platform.parity.execute')]
    public function transition(string $id, Request $request): Response
    {
        $this->csrf($request, 'v3_parity_transition_' . $id);
        $user = $this->user();
        $this->service->transition($user->tenantId(), $user->actorId(), $id, $this->required($request, 'status'), new DateTimeImmutable());
        $this->addFlash('success', 'Der Status wurde aktualisiert.');

        return $this->redirectToRoute('v3_parity_work_item_show', ['id' => $id]);
    }

    /** @return array<string, mixed> */
    private function decodeJsonObject(string $value): array
    {
        $payload = json_decode($value === '' ? '{}' : $value, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($payload)) {
            throw new \InvalidArgumentException('Die Konfiguration muss ein JSON-Objekt sein.');
        }

        return $payload;
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

    private function user(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw new LogicException('The V3 session does not contain a tenant user.');
        }

        return $user;
    }
}
