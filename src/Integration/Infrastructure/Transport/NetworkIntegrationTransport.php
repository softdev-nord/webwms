<?php

declare(strict_types=1);

namespace WebWMS\Integration\Infrastructure\Transport;

use RuntimeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use WebWMS\Integration\Application\RuntimeIntegrationTransport;
use WebWMS\Integration\Domain\ConfiguredTransportEndpoint;
use WebWMS\Integration\Domain\CredentialProvider;

readonly class NetworkIntegrationTransport implements RuntimeIntegrationTransport
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private CredentialProvider $credentials,
    ) {
    }

    /** @param array<string, mixed> $payload */
    public function deliver(ConfiguredTransportEndpoint $target, string $messageId, array $payload): void
    {
        if ($target->endpoint->adapterType === 'http_webservice') {
            $this->deliverHttp($target, $messageId, $payload);

            return;
        }

        $this->deliverTcp($target, $messageId, $payload);
    }

    /** @param array<string, mixed> $payload */
    private function deliverHttp(ConfiguredTransportEndpoint $target, string $messageId, array $payload): void
    {
        $body = $target->protocol->protocol === 'soap_xml'
            ? $this->soapEnvelope($messageId, $payload)
            : json_encode(['messageId' => $messageId, 'payload' => $payload], JSON_THROW_ON_ERROR);
        $response = $this->httpClient->request('POST', $target->endpoint->address, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->credentials->secret($target->endpoint->credentialEnv),
                'Content-Type' => $target->protocol->protocol === 'soap_xml' ? 'application/soap+xml' : 'application/json',
                'Idempotency-Key' => $messageId,
            ],
            'body' => $body,
            'timeout' => $target->protocol->readTimeoutMs / 1000,
        ]);
        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            throw new RuntimeException(sprintf('Transport endpoint "%s" returned HTTP %d.', $target->endpoint->code, $response->getStatusCode()));
        }
    }

    /** @param array<string, mixed> $payload */
    private function deliverTcp(ConfiguredTransportEndpoint $target, string $messageId, array $payload): void
    {
        $errno = 0;
        $error = '';
        $socket = @stream_socket_client(
            $target->endpoint->address,
            $errno,
            $error,
            $target->protocol->connectTimeoutMs / 1000,
            STREAM_CLIENT_CONNECT,
        );
        if (!is_resource($socket)) {
            throw new RuntimeException(sprintf('TCP endpoint "%s" is unavailable: %s (%d).', $target->endpoint->code, $error, $errno));
        }

        try {
            stream_set_timeout($socket, intdiv($target->protocol->readTimeoutMs, 1000), ($target->protocol->readTimeoutMs % 1000) * 1000);
            $body = json_encode(['messageId' => $messageId, 'payload' => $payload], JSON_THROW_ON_ERROR);
            $message = json_encode([
                'messageId' => $messageId,
                'payload' => $payload,
                'signature' => 'sha256=' . hash_hmac('sha256', $body, $this->credentials->secret($target->endpoint->credentialEnv)),
            ], JSON_THROW_ON_ERROR);
            $frame = match ($target->protocol->framing) {
                'newline' => $message . "\n",
                'stx_etx' => "\x02" . $message . "\x03",
                default => $message,
            };
            if (fwrite($socket, $frame) !== strlen($frame)) {
                throw new RuntimeException(sprintf('TCP endpoint "%s" did not accept the complete frame.', $target->endpoint->code));
            }
        } finally {
            fclose($socket);
        }
    }

    /** @param array<string, mixed> $payload */
    private function soapEnvelope(string $messageId, array $payload): string
    {
        $json = htmlspecialchars(json_encode($payload, JSON_THROW_ON_ERROR), ENT_XML1 | ENT_QUOTES, 'UTF-8');

        return sprintf('<?xml version="1.0" encoding="UTF-8"?><Envelope xmlns="http://www.w3.org/2003/05/soap-envelope"><Body><Deliver><MessageId>%s</MessageId><Payload>%s</Payload></Deliver></Body></Envelope>', htmlspecialchars($messageId, ENT_XML1 | ENT_QUOTES, 'UTF-8'), $json);
    }
}
