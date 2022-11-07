<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Entity\Group;
use WebWMS\Entity\User;
use WebWMS\Form\GroupFeatureType;
use WebWMS\Form\GroupType;
use WebWMS\Repository\DefaultRoleRepository;
use WebWMS\Repository\GroupRepository;
use WebWMS\Repository\UserRepository;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        GroupController
 */
class GroupController extends AbstractController
{
    public function __construct(
        private GroupRepository $groupRepository,
        private DefaultRoleRepository $roleRepository,
        private UserRepository $userRepository,
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[Route('/acl/group/create', name: 'group_create')]
    public function create(Request $request): Response
    {
        try {
            $group = new Group();
            $groupName = 'group_'.rand(0, 9999);
            $group->setName($groupName);

            $groupId = $this->groupRepository->add($group);

            return $this->json(['group_id' => $groupId, 'group_name' => $groupName]);
        } catch (\Throwable $e) {
            return $this->json(['error_message' => $e->getMessage()]);
        }
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route('/acl/group/{groupId}/role/{roleId}', name: 'assign_role_to_group')]
    public function assignRole(int $groupId, int $roleId): Response
    {
        $group = $this->getGroup($groupId);

        $role = $this->roleRepository->findOneBy(['id' => (int) $roleId, 'type' => 'default']);
        if (empty($role)) {
            throw new EntityNotFoundException('Role does not exist with provided Id!');
        }

        try {
            $group->addRole($role);
            $updatedGroup = $this->groupRepository->add($group);

            return $this->json(['group_id' => $updatedGroup, 'message' => 'role has been assigned to the group!']);
        } catch (\Throwable $e) {
            return $this->json(['error_message' => $e->getMessage()]);
        }
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route('/acl/group/{groupId}/user/{userId}', name: 'assign_user_to_group')]
    public function assignUser(int $groupId, int $userId): JsonResponse
    {
        $group = $this->getGroup($groupId);

        $user = $this->userRepository->findOneBy(['id' => (int) $userId]);
        if (empty($user)) {
            throw new EntityNotFoundException('User does not exist with provided Id!');
        }

        try {
            $group->addUser($user);
            $updatedGroup = $this->groupRepository->add($group);

            return $this->json(['group_id' => $updatedGroup, 'message' => 'User has been added to the group!']);
        } catch (\Throwable $e) {
            return $this->json(['error_message' => $e->getMessage()]);
        }
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[Route('/acl/list/groups', name: 'acl_group_list')]
    public function list(Request $request, GroupRepository $groupRepository): Response
    {
        $user = $this->getUser();
        $groups = $user->getGroups();

        return $this->render('group/list.html.twig', [
          'groups' => $groups,
        ]);
    }

    #[Route('/acl/edit/group/{id}', name: 'acl_group_edit')]
    public function edit(Group $group, Request $request, GroupRepository $groupRepository): Response
    {
        $form = $this->createForm(GroupType::class, $group);
        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));
            if ($form->isSubmitted() && $form->isValid()) {
                $groupRepository->add($group);
                $this->addFlash('success', 'Group Updated!');

                return $this->redirectToRoute('group_edit', [
                  'id' => $group->getId(),
                ]);
            }
        }

        return $this->render('group/edit.html.twig', [
          'groupForm' => $form->createView(),
          'group' => $group,
        ]);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[Route('/acl/list/group/{id}/roles', name: 'acl_group_roles_list')]
    public function listRoles(Group $group, Request $request): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if (!$user->hasGroup($group)) {
            return new Response('Access denied');
        }

        $roles = $user->getGroupRoles();

        return $this->render('group/listRoles.html.twig', [
          'roles' => $roles,
        ]);
    }

    /**
     * @throws EntityNotFoundException
     */
    private function getGroup(int $groupId): Group
    {
        $group = $this->groupRepository->findOneBy(['id' => $groupId]);

        if (empty($group)) {
            throw new EntityNotFoundException('Group does not exist with provided Id!');
        }

        return $group;
    }

    #[Route('/acl/group/{id}/features', name: 'acl_group_features')]
    public function groupFeatures(Group $group, Request $request): Response
    {
        $user = $this->getUser();

        if (!$user->hasGroup($group)) {
            return new Response('Access denied');
        }

        $form = $this->createForm(GroupFeatureType::class, $group);
        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));
            if ($form->isSubmitted() && $form->isValid()) {
                $this->groupRepository->add($group);
                $this->addFlash('success', 'Features Updated for this group!');

                return $this->redirectToRoute('group_features', [
                  'id' => $group->getId(),
                ]);
            }
        }

        return $this->render('group/featureList.html.twig', [
          'groupFeatureForm' => $form->createView(),
          'group' => $group,
        ]);
    }
}
