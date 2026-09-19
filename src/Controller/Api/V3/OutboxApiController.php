<?php

declare(strict_types=1);

namespace WebWMS\Controller\Api\V3;

use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use WebWMS\Integration\Application\AcknowledgeOutboxMessageCommand;
use WebWMS\Integration\Application\AcknowledgeOutboxMessageHandler;
use WebWMS\Integration\Application\ApiV3QueryService;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/api/v3/outbox', name: 'api_v3_outbox_')]
final class OutboxApiController extends AbstractController
{
    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly AcknowledgeOutboxMessageHandler $acknowledgeMessage
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[IsGranted('integration.outbox.read')]
    public function list(Request $request): JsonResponse
    {
        $messages = $this->queries->pendingOutboxMessages(
            $this->apiUser()->tenantId(),
            $this->limit($request),
            $this->optionalQueryString($request, 'cursor'),
        );
        $last = $messages === [] ? null : $messages[array_key_last($messages)]['id'] ?? null;

        return new JsonResponse(['data' => $messages, 'meta' => [
            'count' => count($messages),
            'nextCursor' => is_string($last) ? $last : null,
        ]]);
    }

    #[Route('/{messageId}/acknowledgement', name: 'acknowledge', methods: ['POST'])]
    #[IsGranted('integration.outbox.acknowledge')]
    public function acknowledge(string $messageId): JsonResponse
    {
        if ($this->queries->outboxMessage($this->apiUser()->tenantId(), $messageId) === null) {
            throw $this->createNotFoundException('The outbox message does not exist.');
        }
        ($this->acknowledgeMessage)(new AcknowledgeOutboxMessageCommand(
            $messageId,
            $this->apiUser()->tenantId(),
            $this->apiUser()->actorId(),
            new DateTimeImmutable(),
        ));

        $message = $this->queries->outboxMessage($this->apiUser()->tenantId(), $messageId);

        return new JsonResponse(['data' => $message]);
    }

    private function apiUser(): TenantPermissionUser
    {
        $user = $this->getUser();
        if (!$user instanceof TenantPermissionUser) {
            throw $this->createAccessDeniedException('An authenticated tenant user is required.');
        }

        return $user;
    }

    private function limit(Request $request): int
    {
        $limit = $request->query->getInt('limit', 50);
        if ($limit < 1 || $limit > 100) {
            throw new \InvalidArgumentException('The limit must be between 1 and 100.');
        }

        return $limit;
    }

    private function optionalQueryString(Request $request, string $name): ?string
    {
        $value = $request->query->getString($name);

        return $value === '' ? null : $value;
    }
}
