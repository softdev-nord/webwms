<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use WebWMS\Entity\Permission as Permissions;
use WebWMS\Repository\PermissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        Permission
 */
class Permission extends AbstractController
{
    public function __construct(
        private PermissionRepository $permissionRepository
    ) {
    }

    #[Route('/acl/permission', name: 'acl_permission_index')]
    public function index()
    {
        return $this->render('role/index.html.twig', [
            'page' => 'Übersicht Benutzerollen',
            'permissions' => $this->permissionRepository->findAll()
        ]);
    }

    #[Route('/acl/permission/create', name: 'acl_create_permission')]
    public function create(Request $request): JsonResponse
    {
        try {
            $name = $request->get('name');
            $scope = $request->get('scope');
            $category = $request->get('category');

            $permission = new Permissions();

            $permission->setName($name);
            $permission->setCategory($category);
            $permission->setScope($scope);
            $permission->setDescription($name . ' Description');


            $permissionId = $this->permissionRepository->add($permission);

            return $this->json([
              'permissionId' => $permissionId,
              'permissionName' => $name,
              "message" => "Permission has been created successfully!",
            ]);
        } catch (\Throwable $e) {
            return $this->json(["error_message" => $e->getMessage()]);
        }
    }
}
