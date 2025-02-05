<?php

declare(strict_types=1);

namespace WebWMS\Service\DataHandlers\Logging;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\Logging;
use WebWMS\Entity\User;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Service\DateTimeService;

#[ClassInformation(
    package: 'WebWMS\Service\DataHandlers\Logging',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'LoggingDataHandler'
)]
readonly class LoggingDataHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DateTimeService $dateTimeService,
    ) {
    }

    public function write(Request $request, string $message, string $username): void
    {
        /** @var User $user */
        $user = $this->entityManager->getRepository(
            User::class
        )->findOneBy(
            ['username' => $username]
        );

        /** @var string $route */
        $route = $request->attributes->get('_route');

        $logging = new Logging();
        $logging->setRoute($route);
        $logging->setMessage($message);
        $logging->setDate($this->dateTimeService->createDateTime());
        $logging->setUser($user->getFirstname() . ' ' . $user->getLastname());
        $logging->setIpAddress((string) $request->getClientIp());
        $logging->setUserAgent((string) $request->headers->get('UserController-Agent'));

        $this->entityManager->persist($logging);
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
