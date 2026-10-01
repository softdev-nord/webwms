<?php

declare(strict_types=1);

namespace WebWMS\Integration\Infrastructure\Transport;

use RuntimeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use WebWMS\Integration\Application\PublishedIntegrationMessage;
use WebWMS\Integration\Application\WcsCommandTransport;
use WebWMS\Integration\Domain\CredentialProvider;
use WebWMS\Integration\Domain\WcsConnection;

readonly class HttpWcsCommandTransport implements WcsCommandTransport
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private CredentialProvider $credentials,
    ) {
    }

    public function deliver(WcsConnection $connection, PublishedIntegrationMessage $message): void
    {
        $body = json_encode([
            'messageId' => $message->messageId,
            'eventName' => $message->eventName,
            'commandId' => $message->aggregateId,
            'command' => $message->payload,
            'occurredAt' => $message->occurredAt,
        ], JSON_THROW_ON_ERROR);
        $response = $this->httpClient->request('POST', rtrim($connection->endpointUrl, '/') . '/commands', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->credentials->secret($connection->credentialEnv),
                'Content-Type' => 'application/json',
                'Idempotency-Key' => $message->messageId,
            ],
            'body' => $body,
            'timeout' => 10,
        ]);
        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            throw new RuntimeException(sprintf('WCS connection "%s" rejected command with HTTP %d.', $connection->code, $response->getStatusCode()));
        }
    }
}
