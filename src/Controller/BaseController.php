<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use WebWMS\Event\BaseEventInterface;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Controller',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'BaseController'
)]
class BaseController extends AbstractController
{
    protected function checkUser(): RedirectResponse
    {
        if (!$this->getUser() instanceof UserInterface) {
            return $this->redirectToRoute('app_login');
        }

        return new RedirectResponse('/');
    }

    protected function objectToArray(object $object): array
    {
        $reflectionClass = new ReflectionClass(get_class($object));
        $array = array();
        foreach ($reflectionClass->getProperties() as $property) {
            $array[$property->getName()] = $property->getValue($object);
        }
        return $array;
    }

//    protected function triggerEvent(BaseEventInterface $event, string $eventName, Request $request): void
//    {
//        $event->getRequest($request);
//        $this->eventDispatcher->dispatch($event, $eventName);
//    }
}
