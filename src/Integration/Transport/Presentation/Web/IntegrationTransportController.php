<?php

declare(strict_types=1);

namespace WebWMS\Integration\Transport\Presentation\Web;

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
use WebWMS\Integration\Application\IntegrationTransportService;
use WebWMS\Integration\Application\Query\IntegrationQueryService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/integration/transports', name: 'v3_transport_')]
class IntegrationTransportController extends AbstractController
{
    public function __construct(
        private readonly IntegrationQueryService $integrationQueries,
        private readonly IntegrationTransportService $transport,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('integration.transport.read')]
    public function index(): Response
    {
        return $this->render('integration/transport/index.html.twig', [
            'page' => 'integration.transport.index.tcp_ip_and_web_service',
            'endpoints' => $this->integrationQueries->transportEndpoints($this->user()->tenantId()),
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
            $this->addFlash('success', 'integration.transport.flash.transport_endpoint_was_created');

            return $this->redirectToRoute('v3_transport_index');
        }

        return $this->render('integration/transport/new.html.twig', ['page' => 'integration.transport.new.create_transport_endpoint']);
    }

    #[Route('/{endpointId}/status', name: 'status', methods: ['POST'])]
    #[IsGranted('integration.transport.write')]
    public function status(string $endpointId, Request $request): RedirectResponse
    {
        $this->assertCsrf($request, 'v3_transport_status_' . $endpointId);
        $endpoint = $this->integrationQueries->transportEndpoint($this->user()->tenantId(), $endpointId);
        if ($endpoint === null) {
            throw $this->createNotFoundException('Der Transport-Endpunkt wurde nicht gefunden.');
        }

        $user = $this->user();
        $this->transport->changeStatus($user->tenantId(), $endpointId, !(bool) $endpoint['active'], $user->actorId(), new DateTimeImmutable());
        $this->addFlash('success', (bool) $endpoint['active'] ? 'integration.flash.endpoint_was_paused' : 'integration.flash.endpoint_was_activated');

        return $this->redirectToRoute('v3_transport_index');
    }

    #[Route('/{endpointId}/deliver', name: 'deliver', methods: ['GET', 'POST'])]
    #[IsGranted('integration.transport.write')]
    public function deliver(string $endpointId, Request $request): Response
    {
        $user = $this->user();
        $endpoint = $this->integrationQueries->transportEndpoint($user->tenantId(), $endpointId);
        if ($endpoint === null) {
            throw $this->createNotFoundException('The transport endpoint was not found.');
        }

        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_transport_deliver_' . $endpointId);
            $payload = json_decode($this->required($request, 'payload'), true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($payload)) {
                throw new InvalidArgumentException('The transport payload must be a JSON object.');
            }
            /** @var array<string, mixed> $payload */

            $messageId = $this->required($request, 'message_id');
            $this->transport->deliver($user->tenantId(), $endpointId, $messageId, $payload);
            $this->addFlash('success', 'integration.transport.flash.message_was_delivered');

            return $this->redirectToRoute('v3_transport_index');
        }

        return $this->render('integration/transport/deliver.html.twig', [
            'page' => 'integration.transport.deliver.deliver_message',
            'endpoint' => $endpoint,
            'messageId' => Uuid::v7()->toRfc4122(),
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
            throw new InvalidArgumentException(sprintf('Das Feld "%s" ist erforderlich.', $field));
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
