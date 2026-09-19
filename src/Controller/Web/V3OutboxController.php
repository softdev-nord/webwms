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
use WebWMS\Integration\Application\AcknowledgeOutboxMessageCommand;
use WebWMS\Integration\Application\AcknowledgeOutboxMessageHandler;
use WebWMS\Integration\Application\ApiV3QueryService;
use WebWMS\Integration\Application\RetryDeadLetterCommand;
use WebWMS\Integration\Application\RetryDeadLetterHandler;
use WebWMS\Security\V3\TenantPermissionUser;

#[Route('/v3/integration/outbox', name: 'v3_outbox_')]
final class V3OutboxController extends AbstractController
{
    private const STATUSES = ['pending', 'processing', 'published', 'dead_letter', 'acknowledged'];

    public function __construct(
        private readonly ApiV3QueryService $queries,
        private readonly AcknowledgeOutboxMessageHandler $acknowledgeMessage,
        private readonly RetryDeadLetterHandler $retryDeadLetter,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    #[IsGranted('integration.outbox.read')]
    public function index(Request $request): Response
    {
        $status = $request->query->getString('status', 'pending');
        if (!in_array($status, self::STATUSES, true)) {
            throw new \InvalidArgumentException('Der Outbox-Statusfilter ist ungültig.');
        }
        $cursor = trim($request->query->getString('cursor'));
        $messages = $this->queries->outboxMessages(
            $this->tenantUser()->tenantId(),
            $status,
            50,
            $cursor === '' ? null : $cursor,
        );
        $last = $messages === [] ? null : $messages[array_key_last($messages)]['id'] ?? null;

        return $this->render('v3/integration/outbox/index.html.twig', [
            'page' => 'Integrations-Outbox',
            'messages' => $messages,
            'statuses' => self::STATUSES,
            'selectedStatus' => $status,
            'nextCursor' => count($messages) === 50 && is_string($last) ? $last : null,
        ]);
    }

    #[Route('/{messageId}', name: 'show', methods: ['GET'])]
    #[IsGranted('integration.outbox.read')]
    public function show(string $messageId): Response
    {
        return $this->render('v3/integration/outbox/show.html.twig', [
            'page' => 'Outbox-Nachricht',
            'message' => $this->requiredMessage($messageId),
        ]);
    }

    #[Route('/{messageId}/retry', name: 'retry', methods: ['POST'])]
    #[IsGranted('integration.outbox.retry')]
    public function retry(string $messageId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_outbox_retry_' . $messageId);
        $message = $this->requiredMessage($messageId);
        if (($message['status'] ?? null) !== 'dead_letter') {
            throw new \DomainException('Nur Dead-Letter-Nachrichten können erneut eingereiht werden.');
        }
        $user = $this->tenantUser();
        ($this->retryDeadLetter)(new RetryDeadLetterCommand(
            $messageId,
            $user->tenantId(),
            $user->actorId(),
            new DateTimeImmutable(),
        ));
        $this->addFlash('success', 'Die Nachricht wurde erneut in die Verarbeitung eingereiht.');

        return $this->redirectToRoute('v3_outbox_show', ['messageId' => $messageId]);
    }

    #[Route('/{messageId}/acknowledgement', name: 'acknowledge', methods: ['POST'])]
    #[IsGranted('integration.outbox.acknowledge')]
    public function acknowledge(string $messageId, Request $request): Response
    {
        $this->assertCsrf($request, 'v3_outbox_acknowledge_' . $messageId);
        $message = $this->requiredMessage($messageId);
        if (($message['status'] ?? null) !== 'pending') {
            throw new \DomainException('Nur offene Nachrichten können manuell quittiert werden.');
        }
        $user = $this->tenantUser();
        ($this->acknowledgeMessage)(new AcknowledgeOutboxMessageCommand(
            $messageId,
            $user->tenantId(),
            $user->actorId(),
            new DateTimeImmutable(),
        ));
        $this->addFlash('success', 'Die Nachricht wurde quittiert.');

        return $this->redirectToRoute('v3_outbox_show', ['messageId' => $messageId]);
    }

    /** @return array<string, mixed> */
    private function requiredMessage(string $messageId): array
    {
        $message = $this->queries->outboxMessage($this->tenantUser()->tenantId(), $messageId);
        if ($message === null) {
            throw $this->createNotFoundException('Die Outbox-Nachricht wurde nicht gefunden.');
        }

        return $message;
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
