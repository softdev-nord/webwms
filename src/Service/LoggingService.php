<?php

declare(strict_types=1);

namespace WebWMS\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use WebWMS\Entity\Logging;

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
        private ContainerInterface $container
    ) {
    }

    public function write(Request $request, $message)
    {
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $logEntry = new Logging();
        $logEntry->setRoute($request->attributes->get('_route'));
        $logEntry->setMessage($message);
        $logEntry->setDate(new \DateTime('NOW', new \DateTimeZone('Europe/Berlin')));
        $logEntry->setUser($user->getFirstname().' '.$user->getLastname());
        $logEntry->setIpAddress($request->getClientIp());
        $logEntry->setUserAgent($request->headers->get('User-Agent'));

        $this->entityManager->persist($logEntry);
        $this->entityManager->flush();
    }
}
