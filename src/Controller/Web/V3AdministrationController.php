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
use WebWMS\Administration\Application\Access\CreateRole\CreateRoleCommand;
use WebWMS\Administration\Application\Access\CreateRole\CreateRoleHandler;
use WebWMS\Administration\Application\Access\CreateUser\CreateUserCommand;
use WebWMS\Administration\Application\Access\CreateUser\CreateUserHandler;
use WebWMS\Administration\Application\Access\V3AdministrationService;
use WebWMS\Administration\Application\Access\V3PermissionCatalog;
use WebWMS\Administration\Application\AdministrationWorkspaceService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/administration', name: 'v3_administration_')]
final class V3AdministrationController extends AbstractController
{
    public function __construct(
        private readonly V3AdministrationService $administration,
        private readonly CreateRoleHandler $createRole,
        private readonly CreateUserHandler $createUser,
        private readonly AdministrationWorkspaceService $workspace,
    ) {
    }

    #[Route('/workspace', name: 'workspace', methods: ['GET'])]
    #[IsGranted('administration.configuration.read')]
    public function workspace(): Response
    {
        return $this->render('v3/administration/workspace.html.twig', [
            'workspace' => $this->workspace->workspace($this->tenantUser()->tenantId()),
            'page' => 'Mandant & Konfiguration',
        ]);
    }

    #[Route('/workspace/{resource}', name: 'workspace_create', requirements: ['resource' => 'partner|context|identity_provider|number_range|device_profile'], methods: ['POST'])]
    #[IsGranted('administration.configuration.write')]
    public function createWorkspaceResource(string $resource, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_administration_workspace_create_' . $resource);
        $user = $this->tenantUser();
        $this->workspace->create($user->tenantId(), $user->actorId(), $resource, $request->request->all(), new DateTimeImmutable());
        $this->addFlash('success', 'Die Konfiguration wurde angelegt.');

        return $this->redirectToRoute('v3_administration_workspace');
    }

    #[Route('/workspace/{resource}/{id}/status', name: 'workspace_status', requirements: ['resource' => 'partner|context|identity_provider|number_range|device_profile'], methods: ['POST'])]
    #[IsGranted('administration.configuration.write')]
    public function workspaceStatus(string $resource, string $id, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_administration_workspace_status_' . $resource . '_' . $id);
        $user = $this->tenantUser();
        $this->workspace->setEnabled($user->tenantId(), $user->actorId(), $resource, $id, $this->required($request, 'enabled') === '1', new DateTimeImmutable());
        $this->addFlash('success', 'Der Status wurde aktualisiert.');

        return $this->redirectToRoute('v3_administration_workspace');
    }

    #[Route('/workspace/process', name: 'process_configure', methods: ['POST'])]
    #[IsGranted('administration.configuration.write')]
    public function configureProcess(Request $request): Response
    {
        $this->assertCsrf($request, 'v3_administration_process_configure');
        $user = $this->tenantUser();
        $this->workspace->configureProcess($user->tenantId(), $user->actorId(), $this->required($request, 'process_key'), $this->required($request, 'name'), $request->request->getBoolean('enabled'), (string) $request->request->get('configuration', '{}'), new DateTimeImmutable());
        $this->addFlash('success', 'Die Prozesskonfiguration wurde gespeichert.');

        return $this->redirectToRoute('v3_administration_workspace');
    }

    #[Route('/workspace/deployment', name: 'deployment_configure', methods: ['POST'])]
    #[IsGranted('administration.configuration.write')]
    public function configureDeployment(Request $request): Response
    {
        $this->assertCsrf($request, 'v3_administration_deployment_configure');
        $user = $this->tenantUser();
        $values = [];
        foreach (['deployment_mode', 'public_url', 'storage_driver', 'queue_transport', 'release_channel'] as $field) {
            $values[$field] = $this->required($request, $field);
        }
        $this->workspace->configureDeployment($user->tenantId(), $user->actorId(), $values, new DateTimeImmutable());
        $this->addFlash('success', 'Das Betriebsprofil wurde gespeichert.');

        return $this->redirectToRoute('v3_administration_workspace');
    }

    #[Route('/workspace/number-ranges/{code}/next', name: 'number_range_next', methods: ['POST'])]
    #[IsGranted('administration.number_range.use')]
    public function nextNumber(string $code, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_administration_number_range_next_' . $code);
        $user = $this->tenantUser();
        $number = $this->workspace->nextNumber($user->tenantId(), $user->actorId(), $code, new DateTimeImmutable());
        $this->addFlash('success', sprintf('Nächste Nummer: %s', $number));

        return $this->redirectToRoute('v3_administration_workspace');
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->tenantUser();
        if (!$user->hasPermission('administration.user.read')
            && !$user->hasPermission('administration.role.read')
            && !$user->hasPermission('administration.api_client.read')) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('v3/administration/index.html.twig', [
            'users' => $user->hasPermission('administration.user.read') ? $this->administration->users($user->tenantId()) : [],
            'roles' => $user->hasPermission('administration.role.read') ? $this->administration->roles($user->tenantId()) : [],
            'apiClients' => $user->hasPermission('administration.api_client.read') ? $this->administration->apiClients($user->tenantId()) : [],
            'page' => 'Administration',
        ]);
    }

    #[Route('/roles/new', name: 'role_new', methods: ['GET', 'POST'])]
    #[IsGranted('administration.role.write')]
    public function createRole(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_administration_role_create');
            $permissions = $this->stringList($request, 'permissions');
            ($this->createRole)(new CreateRoleCommand(
                Uuid::v7()->toRfc4122(),
                $this->tenantUser()->tenantId(),
                $this->required($request, 'code'),
                $this->required($request, 'name'),
                $permissions,
                new DateTimeImmutable(),
            ));
            $this->addFlash('success', 'Die Rolle wurde angelegt.');

            return $this->redirectToRoute('v3_administration_index');
        }

        return $this->render('v3/administration/role_new.html.twig', [
            'permissions' => V3PermissionCatalog::ALL,
            'page' => 'Neue Rolle',
        ]);
    }

    #[Route('/users/new', name: 'user_new', methods: ['GET', 'POST'])]
    #[IsGranted('administration.user.write')]
    public function createUser(Request $request): Response
    {
        $user = $this->tenantUser();
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_administration_user_create');
            ($this->createUser)(new CreateUserCommand(
                Uuid::v7()->toRfc4122(),
                $user->tenantId(),
                $this->required($request, 'email'),
                $this->required($request, 'display_name'),
                $this->required($request, 'password'),
                $this->stringList($request, 'role_ids'),
                new DateTimeImmutable(),
            ));
            $this->addFlash('success', 'Der Benutzer wurde angelegt.');

            return $this->redirectToRoute('v3_administration_index');
        }

        return $this->render('v3/administration/user_new.html.twig', [
            'roles' => $this->administration->roles($user->tenantId()),
            'page' => 'Neuer Benutzer',
        ]);
    }

    #[Route('/users/{userId}/status', name: 'user_status', methods: ['POST'])]
    #[IsGranted('administration.user.write')]
    public function userStatus(string $userId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_administration_user_status_' . $userId);
        $user = $this->tenantUser();
        $active = $this->required($request, 'active') === '1';
        if (!$active && $userId === $user->actorId()) {
            throw new \InvalidArgumentException('Der aktuell angemeldete Benutzer kann sich nicht selbst deaktivieren.');
        }
        $this->administration->setUserActive($user->tenantId(), $userId, $active, new DateTimeImmutable());
        $this->addFlash('success', 'Der Benutzerstatus wurde aktualisiert.');

        return $this->redirectToRoute('v3_administration_index');
    }

    #[Route('/users/{userId}/roles', name: 'user_roles', methods: ['POST'])]
    #[IsGranted('administration.user.write')]
    public function userRoles(string $userId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_administration_user_roles_' . $userId);
        $user = $this->tenantUser();
        $this->administration->setUserRoles($user->tenantId(), $user->actorId(), $userId, $this->stringList($request, 'role_ids'), new DateTimeImmutable());
        $this->addFlash('success', 'Die Benutzerrollen wurden aktualisiert.');

        return $this->redirectToRoute('v3_administration_index');
    }

    #[Route('/roles/{roleId}', name: 'role_edit', methods: ['GET', 'POST'])]
    #[IsGranted('administration.role.write')]
    public function editRole(string $roleId, Request $request): Response
    {
        $user = $this->tenantUser();
        $roles = $this->administration->roles($user->tenantId());
        $role = null;
        foreach ($roles as $candidate) {
            if ($candidate['id'] === $roleId) {
                $role = $candidate;

                break;
            }
        }
        if ($role === null) {
            throw $this->createNotFoundException();
        }
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_administration_role_edit_' . $roleId);
            $this->administration->updateRole($user->tenantId(), $user->actorId(), $roleId, $this->required($request, 'name'), $this->stringList($request, 'permissions'), new DateTimeImmutable());
            $this->addFlash('success', 'Die Rolle wurde aktualisiert.');

            return $this->redirectToRoute('v3_administration_index');
        }

        return $this->render('v3/administration/role_edit.html.twig', ['role' => $role, 'permissions' => V3PermissionCatalog::ALL, 'page' => 'Rolle bearbeiten']);
    }

    #[Route('/api-clients/new', name: 'api_client_new', methods: ['GET', 'POST'])]
    #[IsGranted('administration.api_client.write')]
    public function createApiClient(Request $request): Response
    {
        $user = $this->tenantUser();
        if ($request->isMethod('POST')) {
            $this->assertCsrf($request, 'v3_administration_api_client_create');
            $result = $this->administration->createApiClient(
                $user->tenantId(),
                $this->required($request, 'acting_user_id'),
                $this->required($request, 'name'),
                $this->stringList($request, 'permissions'),
                new DateTimeImmutable(),
            );
            $request->getSession()->set('v3_api_credential', $result['credential']);

            return $this->redirectToRoute('v3_administration_api_client_credential');
        }

        return $this->render('v3/administration/api_client_new.html.twig', [
            'users' => $this->administration->users($user->tenantId()),
            'permissions' => V3PermissionCatalog::ALL,
            'page' => 'API-Client',
        ]);
    }

    #[Route('/api-clients/credential', name: 'api_client_credential', methods: ['GET'])]
    #[IsGranted('administration.api_client.write')]
    public function apiClientCredential(Request $request): Response
    {
        $credential = $request->getSession()->remove('v3_api_credential');
        if (!is_string($credential)) {
            return $this->redirectToRoute('v3_administration_index');
        }

        return $this->render('v3/administration/api_client_credential.html.twig', [
            'credential' => $credential,
        ]);
    }

    #[Route('/api-clients/{clientId}/status', name: 'api_client_status', methods: ['POST'])]
    #[IsGranted('administration.api_client.write')]
    public function apiClientStatus(string $clientId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_administration_api_client_status_' . $clientId);
        $this->administration->setApiClientActive(
            $this->tenantUser()->tenantId(),
            $clientId,
            $this->required($request, 'active') === '1',
        );
        $this->addFlash('success', 'Der API-Client-Status wurde aktualisiert.');

        return $this->redirectToRoute('v3_administration_index');
    }

    /** @return list<string> */
    private function stringList(Request $request, string $field): array
    {
        $values = [];
        foreach ($request->request->all($field) as $value) {
            if (!is_string($value) || trim($value) === '') {
                throw new \InvalidArgumentException(sprintf('Das Feld "%s" enthält einen ungültigen Wert.', $field));
            }
            $values[] = trim($value);
        }

        return array_values(array_unique($values));
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
