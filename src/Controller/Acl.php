<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Entity\CustomRole;
use WebWMS\Entity\Group;
use WebWMS\Entity\Permission;
use WebWMS\Entity\Role;
use WebWMS\Entity\User;
use WebWMS\Repository\PermissionRepository;
use WebWMS\Repository\RoleRepository;
use WebWMS\Repository\UserRepository;
use WebWMS\Service\RoleService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        Acl
 */
class Acl extends AbstractController
{
    public function __construct(
        private PermissionRepository $permissionRepository,
        private RoleRepository $roleRepository,
        private RoleService $roleService
    ) {
    }

    #[Route('/acl', name: 'app_index')]
    public function index(): Response
    {
        // dd($this->permissionRepository->getAllPermissions());

        return $this->render('acl/index.html.twig', [
            'page' => 'Übersicht Benutzerrechte',
            'acl_permissions' => $this->permissionRepository->getAllPermissions(),
            'acl_roles' => $this->roleService->getAllRoles(),
        ]);
    }

//    #[Route('/acl/{username}/{role}/{permission}', name: 'acl_index')]
//    public function index(
//        Request $request,
//        string $username,
//        string $role,
//        string $permission,
//        UserRepository $userRepository,
//        RoleRepository $roleRepository,
//        PermissionRepository $permissionRepository
//    ): Response {
// //    $user = $this->getUser();
// //
// //    if (!$permissionService->canAccess($user->getId(), $groupId, new Permission('list any contract content')) ||
// //      !$featureService->isEnabled('gov_contracts_feature', $groupId)
// //    ) {
// //      return new JsonResponse('Access denied', Response::HTTP_UNAUTHORIZED);
// //    }
//        $user = new User();
//        $user->setUsername($username.rand());
//        $user->setPassword('testPassword');
//        $user->setEmail('example1'.rand().'@test.com');
//        //$user->setId(33);
//
//        $userRole = new CustomRole();
//        $userRole->setName($role.rand());
//        $userRole->setDescription('testDescription');
//        $userRole->setType('testRoleType'.rand());
//
//        $userRolePermission = new Permission();
//        $userRolePermission->setName($permission);
//        $userRolePermission->setDescription('testDescription');
//        $userRolePermission->setScope('testScope'.rand());
//        $userRolePermission->setCategory('testCategory'.rand());
//        $permissionRepository->add($userRolePermission);
//
//        $userRole->addPermission($userRolePermission);
//        $roleRepository->add($userRole);
//
//        $user->addGroupRole($userRole);
//
//        $userRepository->add($user);
//        $responseJson = '{"data":[{"id":258801,"state":{"formatted_value":"Signed","raw_value":{"title":"Signed","color":"#06CF71"}},"title":{"formatted_value":"Contrat de sous traitance Finance","raw_value":{"title":"Contrat de sous traitance Finance","is_restricted":false,"bundle":"contract"}},"parties":{"formatted_value":"Solstice Entertainment, Maggie LEJEUNE DE LEGENDRE ","fields":{"formatted_value":["Solstice Entertainment","Maggie LEJEUNE DE LEGENDRE "],"value":["258028","258112"]}},"signed-on":"","tags":{"formatted_value":"Finance , France","tags":[{"title":"Finance","color":"#cbc2fc"},{"title":"France","color":"#a9fcd0"}]},"end-date":"","delete_contract":true,"update_contract":true,"permission_contract":true},{"id":258802,"state":{"formatted_value":"Signed","raw_value":{"title":"Signed","color":"#06CF71"}},"title":{"formatted_value":"Contrat de prestation de services investissements","raw_value":{"title":"Contrat de prestation de services investissements","is_restricted":false,"bundle":"contract"}},"parties":{"formatted_value":"Wonder Media, Marguerite Investissement","fields":{"formatted_value":["Wonder Media","Marguerite Investissement"],"value":["257908","257956"]}},"signed-on":"","tags":{"formatted_value":"Finance , Investissement","tags":[{"title":"Finance","color":"#cbc2fc"},{"title":"Investissement","color":"#617fd8"}]},"end-date":"","delete_contract":true,"update_contract":true,"permission_contract":true}]}';
//        return $this->json($responseJson);
//    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[Route('/fetch/user', name: 'app_user')]
    public function fetchUser(Request $request, UserRepository $userRepository): Response
    {
        $user = $this->getUser();

        /** @var Role $role */
        foreach ($user->getRoles() as $role) {
            var_dump('user role =>'.$role->getName().'<br/>');
            foreach ($role->getPermissions() as $permission) {
                var_dump('user role permission =>'.$permission->getPermission().'<br/>');
            }
        }

        return new Response('Ok');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[Route('/create/{id}/document', name: 'app_manage_documentation')]
    public function createDocument(Group $group, Request $request): Response
    {
        $this->denyAccessUnlessGranted('documentation_og create document', $group);

        return new Response('Ok');
    }
}
