<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use WebWMS\Form\Model\ChangePassword;
use WebWMS\Helper\FormHelper\UserFormHelper;
use WebWMS\Service\DateTimeService;
use WebWMS\Service\LoggingService;
use WebWMS\Service\RequirementsService;
use WebWMS\Service\User\UserService;
use WebWMS\Service\Validation\ChangePasswordValidationService;
use WebWMS\Service\Validation\UserValidationService;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        User
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class User extends AbstractController
{
    public function __construct(
        private RequirementsService $requirementsService,
        private LoggingService $loggingService,
        private UserService $userService,
        private UserValidationService $userValidationService,
        private ChangePasswordValidationService $passwordValidationService,
        private UserPasswordHasherInterface $passwordHasher,
        private DateTimeService $dateTimeService,
        private UserFormHelper $userFormHelper
    ) {
    }

    #[Route('/benutzer', name: 'user')]
    public function index(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'user/index.html.twig',
            [
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Übersicht Benutzer',
            ]
        );
    }

    #[Route('/benutzer_anlegen', name: 'add_user')]
    public function addUser(Request $request): RedirectResponse|Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->userFormHelper->addUserForm();
        $form->handleRequest($request);
        $requestData = $form->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            $logMessage = 'Der Benutzer ' . $requestData['username'] . ' wurde angelegt.';
            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->userService->addUser($request);

            return new JsonResponse($requestData);
        }

        return $this->render(
            'user/user_add.html.twig',
            [
                'lastId' => $this->getLastUser()[0],
                'userForm' => $form->createView(),
                'addUser' => true,
            ]
        );
    }

    /**
     * @throws \Exception
     */
    #[Route('benutzer_bearbeiten/benutzername/{username}', name: 'edit_user')]
    public function editUser(Request $request, string $username): RedirectResponse|JsonResponse|Response|null
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->userService->getUserByUsername($username);

        if (!$user) {
            return null;
        }

        $form = $this->userFormHelper->editUserForm($user);
        $form->handleRequest($request);
        $username = $user->getUserIdentifier();
        $requestData = $form->getData();

        $responseData = $this->userValidationService->validateUserData($requestData);
        $responseData['message'] = '';

        if ($form->isSubmitted() && $form->isValid()) {
            if ($responseData['success']) {
                $responseData['message'] = 'Der Benutzer ' . $username . ' wurde erfolgreich geändert.';
                $logMessage = 'Der Benutzer ' . $username . ' wurde geändert.';
                $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
                $this->userService->updateUser($request);

                return new JsonResponse($responseData);
            }

            $responseData['message'] = 'Der Benutzer konnte nicht gespeichert werden.';

            return new JsonResponse($responseData);
        }

        return $this->render(
            'user/user_edit.html.twig',
            [
                'userForm' => $form->createView(),
                'addUser' => false,
            ]
        );
    }

    /**
     * @throws \Exception
     */
    #[Route('benutzer_passwort_bearbeiten/benutzername/{username}', name: 'edit_user_password')]
    public function editUserPassword(Request $request, string $username): RedirectResponse|JsonResponse|Response|null
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->userService->getUserByUsername($username);

        if (!$user) {
            return null;
        }

        $changePasswordModel = new ChangePassword();
        $form = $this->userFormHelper->changePasswordForm($changePasswordModel);
        $form->handleRequest($request);
        $username = $user->getUserIdentifier();
        $newPassword = $form->getData()->getNewPassword();

        $responseData = $this->passwordValidationService->validateChangePasswordData($user, $form);
        $responseData['message'] = '';

        if ($form->isSubmitted()) {
            if ($responseData['success']) {
                $responseData['message'] = 'Das Passwort für den Benutzer ' . $username . ' wurde erfolgreich geändert.';
                $logMessage = 'Das Passwort für den Benutzer ' . $username . ' wurde geändert.';
                $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
                $newHashedPassword = $this->passwordHasher->hashPassword(
                    $user,
                    $newPassword
                );
                $user->setUpdatedAt($this->dateTimeService->createDateTime());
                $this->userService->upgradePassword($user, $newHashedPassword);

                return new JsonResponse($responseData);
            }

            $responseData['message'] = 'Das Passwort konnte nicht gespeichert werden.';

            return new JsonResponse($responseData);
        }

        return $this->render(
            'user/user_password_edit.html.twig',
            [
                'userPasswordForm' => $form->createView(),
                'username' => $username,
            ]
        );
    }

    #[Route('/benutzer_löschen/benutzername/{username}', name: 'delete_user')]
    public function deleteArticle(Request $request, string $username): RedirectResponse|JsonResponse|Response|null
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->userService->getUserByUsername($username);

        if (!$user) {
            return null;
        }

        $form = $this->userFormHelper->deleteUserForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $responseData['message'] = 'Der Benutzer ' . $username . ' wurde erfolgreich gelöscht.';
            $logMessage = 'Der Benutzer ' . $username . ' wurde gelöscht.';
            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->userService->deleteUser($username);

            return new JsonResponse($responseData);
        }

        return $this->render(
            'user/user_delete_ask.html.twig',
            [
                'username' => $username,
                'userForm' => $form->createView(),
            ]
        );
    }

    /**
     * @throws Exception
     */
    #[Route('/user_ajax', name: 'user_ajax')]
    public function getAllUsers(): JsonResponse
    {
        return $this->userService->getAllUsers();
    }

    /**
     * @return array<object>
     */
    public function getLastUser(): array
    {
        return $this->userService->getLastUser();
    }
}
