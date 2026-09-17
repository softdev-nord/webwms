<?php

declare(strict_types=1);

namespace WebWMS\Security\EventListener;

use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use WebWMS\Security\Attribute\RequireRole;

class RequireRoleListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => ['onKernelController', 10],
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();

        // Extrahiere Controller + Method
        if (!is_array($controller)) {
            return;
        }

        [$controllerInstance, $methodName] = $controller;

        // Prüfe RequireRole Attribute
        $reflectionMethod = new ReflectionMethod($controllerInstance::class, $methodName);
        $attributes = $reflectionMethod->getAttributes(RequireRole::class);

        foreach ($attributes as $attribute) {
            /** @var RequireRole $requireRole */
            $requireRole = $attribute->newInstance();

            // Prüfe ob Nutzer irgendeine der erforderlichen Rollen hat
            $hasRole = false;
            foreach ($requireRole->roles as $role) {
                if ($this->authorizationChecker->isGranted($role)) {
                    $hasRole = true;
                    break;
                }
            }

            if (!$hasRole) {
                throw new AccessDeniedException(
                    'Access denied. Required roles: ' . implode(', ', $requireRole->roles)
                );
            }
        }
    }
}

