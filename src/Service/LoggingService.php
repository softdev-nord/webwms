<?php

declare(strict_types=1);

namespace WebWMS\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\Logging;
use WebWMS\Entity\User;

/**
 * @package:    WebWMS\Service
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        LoggingService
 */
class LoggingService
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

        $logEntry = new Logging();
        $logEntry->setRoute($request->attributes->get('_route'));
        $logEntry->setMessage($message);
        $logEntry->setDate($this->dateTimeService->createDateTime());
        $logEntry->setUser($user->getFirstname().' '.$user->getLastname());
        $logEntry->setIpAddress($request->getClientIp());
        $logEntry->setUserAgent($request->headers->get('User-Agent'));

        $this->entityManager->persist($logEntry);
        $this->entityManager->flush();
    }
}
