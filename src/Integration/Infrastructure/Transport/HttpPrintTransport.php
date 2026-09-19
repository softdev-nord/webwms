<?php

declare(strict_types=1);

namespace WebWMS\Integration\Infrastructure\Transport;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use WebWMS\Integration\Domain\CredentialProvider;
use WebWMS\Integration\Domain\Printer;
use WebWMS\Integration\Domain\PrintJob;
use WebWMS\Integration\Domain\PrintTransport;

final readonly class HttpPrintTransport implements PrintTransport
{
    public function __construct(
        private HttpClientInterface $client,
        private CredentialProvider $credentials
    ) {
    }

    public function print(Printer $printer, PrintJob $job): string
    {
        $response = $this->client->request('POST', $printer->endpointUrl . '/print-jobs', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->credentials->secret($printer->credentialEnv),
                'Idempotency-Key' => $job->idempotencyKey,
            ],
            'json' => [
                'jobId' => $job->id,
                'documentType' => $job->documentType,
                'documentReference' => $job->documentReference,
                'format' => $job->format,
                'copies' => $job->copies,
            ],
        ]);
        $data = $response->toArray();
        if (!is_string($data['jobReference'] ?? null) || trim($data['jobReference']) === '') {
            throw new \RuntimeException('The printer response does not contain a job reference.');
        }

        return $data['jobReference'];
    }
}
