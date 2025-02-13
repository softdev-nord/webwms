<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use WebWMS\Entity\TransportRequest;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Helper\FormHelper\TransportRequestFormHelper;
use WebWMS\Service\LoggingService;
use WebWMS\Service\RequirementsService;
use WebWMS\Service\TransportRequest\TransportRequestService;

#[ClassInformation(
    package: 'WebWMS\Controller',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'TransportRequestController'
)]
class TransportRequestController extends AbstractController
{
    public function __construct(
        private readonly RequirementsService $requirementsService,
        private readonly TransportRequestService $transportRequestService,
        private readonly TransportRequestFormHelper $transportRequestFormHelper,
        private readonly LoggingService $loggingService,
    ) {
    }

    #[Route('/transportauftrag', name: 'transport_request')]
    public function index(): Response
    {
        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'transport_request/index.html.twig',
            [
                'appName' => $this->requirementsService->getAppName(),
                'appVersion' => $this->requirementsService->getAppVersion(),
                'appVersionNumber' => $this->requirementsService->getAppVersionNumber(),
                'appCopyright' => $this->requirementsService->getAppCopyright(),
                'appLizenz' => $this->requirementsService->getAppLizenz(),
                'page' => 'Artikelübersicht',
            ]
        );
    }

    #[Route('/transportauftrag_anlegen', name: 'add_transport_request')]
    public function addTransportRequest(Request $request): Response
    {
        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->transportRequestFormHelper->addTransportRequestForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TransportRequest $newTransportRequest */
            $newTransportRequest = $form->getData();
            $transportRequestNr = $newTransportRequest->getTrNr();
            $responseData = [];

            $responseData['message'] = 'Der Transportauftrag mit der TA-Nr. ' . $transportRequestNr . ' wurde erfolgreich angelegt.';
            $logMessage = 'Der Transportauftrag mit der TA-Nr. ' . $transportRequestNr . ' wurde angelegt.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->transportRequestService->addTransportRequest($newTransportRequest);

            return new JsonResponse($responseData);
        }

        return $this->render(
            'transport_request/transport_request_add.html.twig',
            [
                'lastId' => $this->transportRequestService->getLastTransportRequestNr(),
                'transportRequestForm' => $form->createView(),
                'editTransportRequest' => false,
            ]
        );
    }

    #[Route('ta_position_bearbeiten/id/{id}', name: 'edit_ta_position')]
    public function editTransportRequest(Request $request, int $id): RedirectResponse|JsonResponse|Response|null
    {
        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        $transportRequest = $this->transportRequestService->getTransportRequestById($id);

        if (!$transportRequest instanceof TransportRequest) {
            return null;
        }

        $form = $this->transportRequestFormHelper->editTransportRequestForm($transportRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TransportRequest $updatedTransportRequest */
            $updatedTransportRequest = $form->getData();
            $transportRequestNr = $updatedTransportRequest->getTrNr();
            $responseData = [];

            $responseData['message'] = 'Der Transportauftrag mit der TA-Nr. ' . $transportRequestNr . ' wurde erfolgreich geändert.';
            $logMessage = 'Der Transportauftrag mit der TA-Nr. ' . $transportRequestNr . ' wurde geändert.';

            $this->loggingService->write($request, $logMessage, $this->getUser()->getUserIdentifier());
            $this->transportRequestService->updateTransportRequest($updatedTransportRequest);

            return new JsonResponse($responseData);
        }

        return $this->render(
            'transport_request/transport_request_edit.html.twig',
            [
                'transportRequestForm' => $form->createView(),
                'transportRequest' => $transportRequest,
                'editTransportRequest' => true,
            ]
        );
    }

    /**
     * @SuppressWarnings(UnusedFormalParameter)
     */
    #[Route('ta_position_löschen/id/{id}', name: 'delete_ta_position')]
    public function deleteTransportRequest(string $id): RedirectResponse|JsonResponse|Response|null
    {
        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        return null;
    }

    #[Route('/transport_request_ajax', name: 'transport_request_ajax')]
    public function getAllTransportRequests(): JsonResponse
    {
        return $this->transportRequestService->getAllOpenTransportRequests();
    }
}
