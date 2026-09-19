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
use WebWMS\Integration\Application\CarrierGateway;
use WebWMS\Integration\Application\ChangeCarrierConnectionStatusCommand;
use WebWMS\Integration\Application\ChangeCarrierConnectionStatusHandler;
use WebWMS\Integration\Application\RegisterCarrierConnectionCommand;
use WebWMS\Integration\Application\RegisterCarrierConnectionHandler;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/integration/carrier-connections', name: 'v3_carrier_connection_')]
final class V3CarrierConnectionController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly RegisterCarrierConnectionHandler $registerConnection,
        private readonly ChangeCarrierConnectionStatusHandler $changeStatus,
        private readonly CarrierGateway $gateway,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('integration.carrier_connection.read')]
    public function index(): Response
    {
        return $this->render('v3/integration/carrier-connection/index.html.twig', [
            'page' => 'Carrier-Verbindungen',
            'connections' => $this->queries->carrierConnections($this->tenantUser()->tenantId()),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    #[IsGranted('integration.carrier_connection.write')]
    public function create(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_carrier_connection_create');
            $user = $this->tenantUser();
            $connectionId = Uuid::v7()->toRfc4122();
            ($this->registerConnection)(new RegisterCarrierConnectionCommand(
                $connectionId,
                $user->tenantId(),
                $this->required($request, 'name'),
                $this->required($request, 'carrier_code'),
                $this->required($request, 'endpoint_url'),
                $this->required($request, 'credential_env'),
                $request->request->getBoolean('active'),
                $user->actorId(),
                new DateTimeImmutable(),
            ));
            $this->addFlash('success', 'Die Carrier-Verbindung wurde angelegt.');

            return $this->redirectToRoute('v3_carrier_connection_show', ['connectionId' => $connectionId]);
        }

        return $this->render('v3/integration/carrier-connection/new.html.twig', [
            'page' => 'Carrier-Verbindung anlegen',
        ]);
    }

    #[Route('/{connectionId}', name: 'show', methods: ['GET'])]
    #[IsGranted('integration.carrier_connection.read')]
    public function show(string $connectionId): Response
    {
        return $this->render('v3/integration/carrier-connection/show.html.twig', [
            'page' => 'Carrier-Verbindung',
            'connection' => $this->requiredConnection($connectionId),
        ]);
    }

    #[Route('/{connectionId}/products', name: 'products', methods: ['GET'])]
    #[IsGranted('integration.carrier.read')]
    public function products(string $connectionId): Response
    {
        $connection = $this->requiredConnection($connectionId);
        if (!(bool) ($connection['active'] ?? false)) {
            throw new \DomainException('Versandprodukte können nur für eine aktive Carrier-Verbindung abgerufen werden.');
        }

        return $this->render('v3/integration/carrier-connection/products.html.twig', [
            'page' => 'Carrier-Produkte',
            'connection' => $connection,
            'products' => $this->gateway->products(
                $this->tenantUser()->tenantId(),
                $this->connectionValue($connection, 'carrier_code'),
            ),
        ]);
    }

    #[Route('/{connectionId}/status', name: 'status', methods: ['POST'])]
    #[IsGranted('integration.carrier_connection.write')]
    public function status(string $connectionId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_carrier_connection_status_' . $connectionId);
        $connection = $this->requiredConnection($connectionId);
        $active = !(bool) ($connection['active'] ?? false);
        $user = $this->tenantUser();
        ($this->changeStatus)(new ChangeCarrierConnectionStatusCommand(
            $connectionId,
            $user->tenantId(),
            $active,
            $user->actorId(),
            new DateTimeImmutable(),
        ));
        $this->addFlash('success', $active
            ? 'Die Carrier-Verbindung wurde aktiviert.'
            : 'Die Carrier-Verbindung wurde pausiert.');

        return $this->redirectToRoute('v3_carrier_connection_show', ['connectionId' => $connectionId]);
    }

    /** @return array<string, mixed> */
    private function requiredConnection(string $connectionId): array
    {
        $connection = $this->queries->carrierConnection($this->tenantUser()->tenantId(), $connectionId);
        if ($connection === null) {
            throw $this->createNotFoundException('Die Carrier-Verbindung wurde nicht gefunden.');
        }

        return $connection;
    }

    /** @param array<string, mixed> $connection */
    private function connectionValue(array $connection, string $field): string
    {
        $value = $connection[$field] ?? null;
        if (!is_string($value) || $value === '') {
            throw new LogicException(sprintf('Das persistierte Carrier-Feld "%s" ist ungültig.', $field));
        }

        return $value;
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
