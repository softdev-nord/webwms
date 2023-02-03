<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Logging;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\Logging;
use WebWMS\Entity\User;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\Logging
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        LoggingDataHandler
 */
class LoggingDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DateTimeService $dateTimeService
    ) {
    }

    public function write(Request $request, string $message, string $username): void
    {
        $user = $this->entityManager->getRepository(
            User::class)->findOneBy(
                ['username' => $username]
            );

        if (!$user) {
            return;
        }

        $logEntry = new Logging();
        $logEntry->setRoute($request->attributes->get('_route'));
        $logEntry->setMessage($message);
        $logEntry->setDate($this->dateTimeService->createDateTime());
        $logEntry->setIpAddress((string) $request->getClientIp());
        $logEntry->setUserAgent((string) $request->headers->get('User-Agent'));

        $this->entityManager->persist($logEntry);
        $this->entityManager->flush();
    }

    /**
     * @throws Exception
     */
    public function getAllLogs(): JsonResponse
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

        $queryBuilder
            ->select('*')
            ->from('logging');

        $stmt = $queryBuilder->executeQuery();
        $results = $stmt->fetchAllAssociative();

        return new JsonResponse($results);
    }
}
