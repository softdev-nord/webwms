<?php

declare(strict_types=1);

namespace WebWMS\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use WebWMS\Entity\User;
use WebWMS\Service\User\UserService;

/**
 * @package:    WebWMS\EventSubscriber
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        LastLoginSubscriber
 *
 * Subscriber zur Aktualisierung der letzten Anmeldezeit des Benutzers.
 */
class LastLoginSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private UserService $userService
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        // return the subscribed events, their methods and priorities
        return [
           KernelEvents::FINISH_REQUEST => [
               ['updateLastLogin', -10],
           ],
        ];
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function updateLastLogin(): void
    {
        $accessToken = $this->tokenStorage->getToken();
        if ($accessToken !== null) {
            /* @var User $user */
            $user = $accessToken->getUser();
            if ($user instanceof User) {
                $this->userService->updateLastLogin($user);
            }
        }
    }
}
