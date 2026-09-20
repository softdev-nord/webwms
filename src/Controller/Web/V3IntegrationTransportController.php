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
use WebWMS\Integration\Application\ApiV3QueryService;
use WebWMS\Integration\Application\IntegrationTransportService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/integration/transports', name: 'v3_transport_')]
final class V3IntegrationTransportController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly IntegrationTransportService $transport,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('integration.transport.read')]
    public function index(): Response
    {
        return $this->render('v3/integration/transport/index.html.twig', [
            'page' => 'TCP/IP und Webservice',
            'endpoints' => $this->queries->transportEndpoints($this->user()->tenantId()),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    #[IsGranted('integration.transport.write')]
    public function create(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_transport_create');
            $user = $this->user();
            $this->transport->register(
                $user->tenantId(),
                $this->required($request, 'code'),
                $this->required($request, 'name'),
                $this->required($request, 'adapter_type'),
                $this->required($request, 'address'),
                $this->required($request, 'credential_env'),
                $this->required($request, 'protocol'),
                $this->required($request, 'framing'),
                $request->request->getInt('connect_timeout_ms'),
                $request->request->getInt('read_timeout_ms'),
                $request->request->getBoolean('active'),
                $user->actorId(),
                new DateTimeImmutable(),
            );
            $this->addFlash('success', 'Der Transport-Endpunkt wurde angelegt.');

            return $this->redirectToRoute('v3_transport_index');
        }

        return $this->render('v3/integration/transport/new.html.twig', ['page' => 'Transport-Endpunkt anlegen']);
    }

    #[Route('/{endpointId}/status', name: 'status', methods: ['POST'])]
    #[IsGranted('integration.transport.write')]
    public function status(string $endpointId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_transport_status_' . $endpointId);
        $endpoint = $this->queries->transportEndpoint($this->user()->tenantId(), $endpointId);
        if ($endpoint === null) {
            throw $this->createNotFoundException('Der Transport-Endpunkt wurde nicht gefunden.');
        }
        $user = $this->user();
        $this->transport->changeStatus($user->tenantId(), $endpointId, !(bool) $endpoint['active'], $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', (bool) $endpoint['active'] ? 'Der Endpunkt wurde pausiert.' : 'Der Endpunkt wurde aktiviert.');

        return $this->redirectToRoute('v3_transport_index');
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

    private function assertCsrf(Request $request, string $id): void
    {
        if (!$this->isCsrfTokenValid($id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Das Formular-Token ist ungültig.');
        }
    }
}
