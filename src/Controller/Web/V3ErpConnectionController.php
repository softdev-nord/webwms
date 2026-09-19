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
use WebWMS\Integration\Application\ChangeErpConnectionStatusCommand;
use WebWMS\Integration\Application\ChangeErpConnectionStatusHandler;
use WebWMS\Integration\Application\RegisterErpConnectionCommand;
use WebWMS\Integration\Application\RegisterErpConnectionHandler;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/integration/erp-connections', name: 'v3_erp_connection_')]
final class V3ErpConnectionController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly RegisterErpConnectionHandler $registerConnection,
        private readonly ChangeErpConnectionStatusHandler $changeStatus,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('integration.erp_connection.read')]
    public function index(): Response
    {
        return $this->render('v3/integration/erp-connection/index.html.twig', [
            'page' => 'ERP-Verbindungen',
            'connections' => $this->queries->erpConnections($this->tenantUser()->tenantId()),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    #[IsGranted('integration.erp_connection.write')]
    public function create(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_erp_connection_create');
            $user = $this->tenantUser();
            $connectionId = Uuid::v7()->toRfc4122();
            ($this->registerConnection)(new RegisterErpConnectionCommand(
                $connectionId,
                $user->tenantId(),
                $this->required($request, 'name'),
                $this->required($request, 'endpoint_url'),
                $this->required($request, 'credential_env'),
                $request->request->getBoolean('active'),
                $user->actorId(),
                new DateTimeImmutable(),
            ));
            $this->addFlash('success', 'Die ERP-Verbindung wurde angelegt.');

            return $this->redirectToRoute('v3_erp_connection_show', ['connectionId' => $connectionId]);
        }

        return $this->render('v3/integration/erp-connection/new.html.twig', [
            'page' => 'ERP-Verbindung anlegen',
        ]);
    }

    #[Route('/{connectionId}', name: 'show', methods: ['GET'])]
    #[IsGranted('integration.erp_connection.read')]
    public function show(string $connectionId): Response
    {
        return $this->render('v3/integration/erp-connection/show.html.twig', [
            'page' => 'ERP-Verbindung',
            'connection' => $this->requiredConnection($connectionId),
        ]);
    }

    #[Route('/{connectionId}/status', name: 'status', methods: ['POST'])]
    #[IsGranted('integration.erp_connection.write')]
    public function status(string $connectionId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_erp_connection_status_' . $connectionId);
        $connection = $this->requiredConnection($connectionId);
        $active = !$this->isActive($connection);
        $user = $this->tenantUser();
        ($this->changeStatus)(new ChangeErpConnectionStatusCommand(
            $connectionId,
            $user->tenantId(),
            $active,
            $user->actorId(),
            new DateTimeImmutable(),
        ));
        $this->addFlash('success', $active
            ? 'Die ERP-Verbindung wurde aktiviert.'
            : 'Die ERP-Verbindung wurde pausiert.');

        return $this->redirectToRoute('v3_erp_connection_show', ['connectionId' => $connectionId]);
    }

    /** @return array<string, mixed> */
    private function requiredConnection(string $connectionId): array
    {
        $connection = $this->queries->erpConnection($this->tenantUser()->tenantId(), $connectionId);
        if ($connection === null) {
            throw $this->createNotFoundException('Die ERP-Verbindung wurde nicht gefunden.');
        }

        return $connection;
    }

    /** @param array<string, mixed> $connection */
    private function isActive(array $connection): bool
    {
        return (bool) ($connection['active'] ?? false);
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
