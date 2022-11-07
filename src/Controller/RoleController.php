<?php

namespace WebWMS\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Entity\DefaultRole;
use WebWMS\Entity\Role;
use WebWMS\Form\RoleType;
use WebWMS\Repository\DefaultRoleRepository;
use WebWMS\Repository\PermissionRepository;
use WebWMS\Repository\RoleRepository;

class RoleController extends AbstractController
{
    public function __construct(
        private DefaultRoleRepository $roleRepository,
        private PermissionRepository $permissionRepository
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[Route('/acl/role/create', name: 'role_create')]
    public function create(Request $request): Response
    {
        try {
            $role = new DefaultRole();
            $roleName = 'role_'.rand(0, 9999);
            $role->setName($roleName);
            $role->setDescription($roleName.' Description');
            $role->setType(Role::CUSTOM_ROLE_TYPE);

            $roleId = $this->roleRepository->add($role);

            return $this->json([
              'role_id' => $roleId,
              'role_name' => $roleName,
              'type' => Role::CUSTOM_ROLE_TYPE,
            ]);
        } catch (\Throwable $e) {
            return $this->json(['error_message' => $e->getMessage()]);
        }
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[Route('/acl/assign-role-to-permission/role/{roleId}/permission/{permissionId}', name: 'assign_permissions_to_role')]
    public function assignPermission(Request $request, int $roleId, int $permissionId): Response
    {
        try {
            $role = $this->roleRepository->findOneBy([
              'id' => $roleId,
            ]);
            if (empty($role)) {
                throw new EntityNotFoundException('Role does not exist with provided Id!');
            }

            $permission = $this->permissionRepository->findOneBy([
              'id' => $permissionId,
            ]);
            if (empty($permission)) {
                throw new EntityNotFoundException('Permission does not exist with provided Id!');
            }

            $role->addPermission($permission);
            $roleId = $this->roleRepository->add($role);

            return $this->json([
              'role_id' => $roleId,
              'message' => 'Permission '.$permission->getName().'has been assign to role!',
            ]);
        } catch (\Throwable $e) {
            return $this->json(['error_message' => $e->getMessage()]);
        }
    }

    #[Route('/acl/role/{id}/edit', name: 'role_edit')]
    public function edit(Role $role, Request $request, RoleRepository $roleRepository): Response
    {
        $form = $this->createForm(RoleType::class, $role);
        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));
            if ($form->isSubmitted() && $form->isValid()) {
                $roleRepository->add($role);
                $this->addFlash('success', 'User Updated!');

                return $this->redirectToRoute('role_edit', [
                  'id' => $role->getId(),
                ]);
            }
        }

        return $this->render('role/edit.html.twig', [
          'roleForm' => $form->createView(),
          'role' => $role,
        ]);
    }
}
