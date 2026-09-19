<?php

declare(strict_types=1);

namespace WebWMS\Integration\Infrastructure\Transport;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use WebWMS\Integration\Domain\CarrierConnection;
use WebWMS\Integration\Domain\CarrierTransport;
use WebWMS\Integration\Domain\CredentialProvider;

final readonly class HttpCarrierTransport implements CarrierTransport
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private CredentialProvider $credentials
    ) {
    }

    public function products(CarrierConnection $connection): array
    {
        $data = $this->request($connection, 'GET', '/products', null, null);
        $products = $data['products'] ?? null;
        if (!is_array($products)) {
            throw new \RuntimeException('The carrier products response is invalid.');
        }

        return array_values(array_map(static function (mixed $product): array {
            if (!is_array($product) || !is_string($product['code'] ?? null) || !is_string($product['name'] ?? null)) {
                throw new \RuntimeException('A carrier product is invalid.');
            }

            return ['code' => $product['code'], 'name' => $product['name']];
        }, $products));
    }

    public function createLabel(CarrierConnection $connection, array $shipment, string $idempotencyKey): array
    {
        $data = $this->request($connection, 'POST', '/labels', $shipment, $idempotencyKey);

        return ['trackingNumber' => $this->string($data, 'trackingNumber'), 'labelReference' => $this->string($data, 'labelReference')];
    }

    public function handoverManifest(CarrierConnection $connection, array $manifest, string $idempotencyKey): array
    {
        $data = $this->request($connection, 'POST', '/manifests', $manifest, $idempotencyKey);

        return ['handoverReference' => $this->string($data, 'handoverReference')];
    }

    public function tracking(CarrierConnection $connection, string $trackingNumber): array
    {
        $data = $this->request($connection, 'GET', '/tracking/' . rawurlencode($trackingNumber), null, null);
        $result = ['status' => $this->string($data, 'status'), 'occurredAt' => $this->string($data, 'occurredAt')];
        if (isset($data['description']) && is_string($data['description'])) {
            $result['description'] = $data['description'];
        }

        return $result;
    }

    /** @param array<string, mixed>|null $payload @return array<string, mixed> */
    private function request(CarrierConnection $connection, string $method, string $path, ?array $payload, ?string $idempotencyKey): array
    {
        $headers = ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . $this->credentials->secret($connection->credentialEnv)];
        if ($idempotencyKey !== null) {
            $headers['Idempotency-Key'] = $idempotencyKey;
        }
        $options = ['headers' => $headers, 'timeout' => 20];
        if ($payload !== null) {
            $options['json'] = $payload;
        }
        $response = $this->httpClient->request($method, $connection->endpointUrl . $path, $options);
        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            throw new \RuntimeException(sprintf('Carrier "%s" rejected %s with HTTP %d.', $connection->name, $path, $response->getStatusCode()));
        }

        return $response->toArray(false);
    }

    /** @param array<string, mixed> $data */
    private function string(array $data, string $field): string
    {
        if (!is_string($data[$field] ?? null) || trim($data[$field]) === '') {
            throw new \RuntimeException(sprintf('Carrier response field "%s" is missing.', $field));
        }

        return $data[$field];
    }
}
