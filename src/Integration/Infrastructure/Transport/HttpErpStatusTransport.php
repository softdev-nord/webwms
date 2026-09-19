<?php

declare(strict_types=1);

namespace WebWMS\Integration\Infrastructure\Transport;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use WebWMS\Integration\Application\PublishedIntegrationMessage;
use WebWMS\Integration\Domain\CredentialProvider;
use WebWMS\Integration\Domain\ErpConnection;
use WebWMS\Integration\Domain\ErpStatusTransport;

final readonly class HttpErpStatusTransport implements ErpStatusTransport
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private CredentialProvider $credentials
    ) {
    }

    public function deliver(ErpConnection $connection, PublishedIntegrationMessage $message): void
    {
        $body = json_encode([
            'id' => $message->messageId,
            'eventName' => $message->eventName,
            'aggregateType' => $message->aggregateType,
            'aggregateId' => $message->aggregateId,
            'occurredAt' => $message->occurredAt,
            'payload' => $message->payload,
        ], JSON_THROW_ON_ERROR);
        $signature = hash_hmac('sha256', $body, $this->credentials->secret($connection->credentialEnv));
        $response = $this->httpClient->request('POST', $connection->endpointUrl . '/status-events', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Idempotency-Key' => $message->messageId,
                'X-WebWMS-Event' => $message->eventName,
                'X-WebWMS-Signature' => 'sha256=' . $signature,
            ],
            'body' => $body,
            'timeout' => 10,
        ]);
        $statusCode = $response->getStatusCode();
        if ($statusCode < 200 || $statusCode >= 300) {
            throw new \RuntimeException(sprintf(
                'ERP connection "%s" rejected status event with HTTP %d.',
                $connection->name,
                $statusCode,
            ));
        }
    }
}
