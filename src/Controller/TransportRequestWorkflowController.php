<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use WebWMS\Repository\TransportRequestRepository;
use WebWMS\Service\Workflow\TransportRequestWorkflowService;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Controller',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'TransportRequestWorkflowController'
)]
class TransportRequestWorkflowController extends AbstractController
{
    public function __construct(
        private readonly TransportRequestRepository $transportRequestRepository,
        private readonly TransportRequestWorkflowService $workflowService,
    ) {
    }

    /**
     * Startet einen Transport
     */
    #[Route('/transport_request/{id}/start', name: 'transport_request_start', methods: ['POST'])]
    public function startTransportRequest(int $id, Request $request): JsonResponse
    {
        if (!$this->getUser() instanceof UserInterface) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        try {
            $transportRequest = $this->transportRequestRepository->find($id);
            if (!$transportRequest) {
                return new JsonResponse(['error' => 'Transport request not found'], 404);
            }

            if ($this->workflowService->start($transportRequest)) {
                $this->transportRequestRepository->save($transportRequest);
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Transport request started',
                    'state' => $transportRequest->getTrState(),
                ]);
            }

            return new JsonResponse(['error' => 'Cannot start transport request from current state'], 422);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Beendet einen Transport
     */
    #[Route('/transport_request/{id}/complete', name: 'transport_request_complete', methods: ['POST'])]
    public function completeTransportRequest(int $id, Request $request): JsonResponse
    {
        if (!$this->getUser() instanceof UserInterface) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        try {
            $transportRequest = $this->transportRequestRepository->find($id);
            if (!$transportRequest) {
                return new JsonResponse(['error' => 'Transport request not found'], 404);
            }

            if ($this->workflowService->complete($transportRequest)) {
                $this->transportRequestRepository->save($transportRequest);
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Transport request completed',
                    'state' => $transportRequest->getTrState(),
                ]);
            }

            return new JsonResponse(['error' => 'Cannot complete transport request from current state'], 422);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Storniert einen Transport
     */
    #[Route('/transport_request/{id}/cancel', name: 'transport_request_cancel', methods: ['POST'])]
    public function cancelTransportRequest(int $id, Request $request): JsonResponse
    {
        if (!$this->getUser() instanceof UserInterface) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        try {
            $transportRequest = $this->transportRequestRepository->find($id);
            if (!$transportRequest) {
                return new JsonResponse(['error' => 'Transport request not found'], 404);
            }

            if ($this->workflowService->cancel($transportRequest)) {
                $this->transportRequestRepository->save($transportRequest);
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Transport request cancelled',
                    'state' => $transportRequest->getTrState(),
                ]);
            }

            return new JsonResponse(['error' => 'Cannot cancel transport request from current state'], 422);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Archiviert einen Transport
     */
    #[Route('/transport_request/{id}/archive', name: 'transport_request_archive', methods: ['POST'])]
    public function archiveTransportRequest(int $id, Request $request): JsonResponse
    {
        if (!$this->getUser() instanceof UserInterface) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        try {
            $transportRequest = $this->transportRequestRepository->find($id);
            if (!$transportRequest) {
                return new JsonResponse(['error' => 'Transport request not found'], 404);
            }

            if ($this->workflowService->archive($transportRequest)) {
                $this->transportRequestRepository->save($transportRequest);
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Transport request archived',
                    'state' => $transportRequest->getTrState(),
                ]);
            }

            return new JsonResponse(['error' => 'Cannot archive transport request from current state'], 422);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Gibt Status und erlaubte Übergänge zurück
     */
    #[Route('/transport_request/{id}/status', name: 'transport_request_status', methods: ['GET'])]
    public function getTransportRequestStatus(int $id): JsonResponse
    {
        if (!$this->getUser() instanceof UserInterface) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        try {
            $transportRequest = $this->transportRequestRepository->find($id);
            if (!$transportRequest) {
                return new JsonResponse(['error' => 'Transport request not found'], 404);
            }

            return new JsonResponse([
                'id' => $transportRequest->getId(),
                'state' => $this->workflowService->getCurrentMarking($transportRequest),
                'enabled_transitions' => $this->workflowService->getEnabledTransitions($transportRequest),
            ]);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}

