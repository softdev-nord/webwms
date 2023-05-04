<?php

declare(strict_types=1);

namespace WebWMS\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SlackNotificationService
 */
class SlackNotificationService
{
    public function __construct(
        private HttpClientInterface $client
    ) {
    }

    /**
     * @SuppressWarnings("unused")
     */
    public function sendSlackNotification(string $freeStockLocations): ResponseInterface
    {
        $message = json_encode($freeStockLocations);
        $logMessage = sprintf('Der Artikel mit der Artikel-Nr. %s wurde geändert.', '60004');

        return $this->client->request('POST', 'https://hooks.slack.com/services/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', [
            'body' => "{\"channel\": \"#webwms\", \"username\": \"webhookbot\", \"text\": \"'.$logMessage.'\"}",
        ]);
    }
}
