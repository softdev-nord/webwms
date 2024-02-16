<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Logging;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\LoggingEntity;
use WebWMS\Entity\UserEntity;
use WebWMS\Service\DateTimeService;

/**
 * @package:    WebWMS\Service\DataHandlers\LoggingEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        LoggingDataHandler
 */
class LoggingDataHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DateTimeService $dateTimeService
    ) {
    }

    public function write(Request $request, string $message, string $username): void
    {
        /** @var UserEntity $user */
        $user = $this->entityManager->getRepository(
            UserEntity::class)->findOneBy(
                ['username' => $username]
            );

        $loggingEntity = new LoggingEntity();
        $loggingEntity->setRoute((string) $request->attributes->get('_route'));
        $loggingEntity->setMessage($message);
        $loggingEntity->setDate($this->dateTimeService->createDateTime());
        $loggingEntity->setUser($user->getFirstname() . ' ' . $user->getLastname());
        $loggingEntity->setIpAddress((string) $request->getClientIp());
        $loggingEntity->setUserAgent((string) $request->headers->get('UserController-Agent'));

        $this->entityManager->persist($loggingEntity);
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

        $result = $queryBuilder->executeQuery();
        $results = $result->fetchAllAssociative();

        return new JsonResponse($results);
    }
}
