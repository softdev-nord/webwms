<?php

declare(strict_types=1);

namespace WebWMS\Integration\Infrastructure\Api;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use WebWMS\Integration\Domain\AutomationDeviceNotFoundException;
use WebWMS\Integration\Domain\DeviceNotFoundException;
use WebWMS\Integration\Domain\ErpConnectionNotFoundException;
use WebWMS\Integration\Domain\MeasurementDeviceNotFoundException;
use WebWMS\Integration\Domain\OutboxMessageNotFoundException;
use WebWMS\Integration\Domain\WcsConnectionNotFoundException;
use WebWMS\Inventory\Domain\InsufficientAvailableStockException;
use WebWMS\Inventory\Domain\InventoryReferenceNotFoundException;

#[AsEventListener(event: KernelEvents::EXCEPTION)]
final readonly class ApiExceptionSubscriber
{
    public function __invoke(ExceptionEvent $event): void
    {
        if (!str_starts_with($event->getRequest()->getPathInfo(), '/api/v3')) {
            return;
        }

        $exception = $event->getThrowable();
        [$status, $title, $detail] = match (true) {
            $exception instanceof AccessDeniedHttpException,
            $exception instanceof AccessDeniedException => [403, 'Forbidden', 'The API client lacks the required permission.'],
            $exception instanceof JsonException => [400, 'Bad Request', 'The request body must contain valid JSON.'],
            $exception instanceof InventoryReferenceNotFoundException => [404, 'Not Found', $exception->getMessage()],
            $exception instanceof OutboxMessageNotFoundException => [404, 'Not Found', $exception->getMessage()],
            $exception instanceof ErpConnectionNotFoundException => [404, 'Not Found', $exception->getMessage()],
            $exception instanceof DeviceNotFoundException => [404, 'Not Found', $exception->getMessage()],
            $exception instanceof AutomationDeviceNotFoundException => [404, 'Not Found', $exception->getMessage()],
            $exception instanceof MeasurementDeviceNotFoundException => [404, 'Not Found', $exception->getMessage()],
            $exception instanceof WcsConnectionNotFoundException => [404, 'Not Found', $exception->getMessage()],
            $exception instanceof InsufficientAvailableStockException => [409, 'Conflict', $exception->getMessage()],
            $exception instanceof \DomainException => [409, 'Conflict', $exception->getMessage()],
            $exception instanceof UniqueConstraintViolationException => [409, 'Conflict', 'The resource already exists.'],
            $exception instanceof \InvalidArgumentException => [422, 'Unprocessable Entity', $exception->getMessage()],
            $exception instanceof HttpExceptionInterface => [$exception->getStatusCode(), Response::$statusTexts[$exception->getStatusCode()] ?? 'Request failed', $exception->getMessage()],
            default => [500, 'Internal Server Error', 'The request could not be processed.'],
        };

        $event->setResponse(new JsonResponse([
            'type' => 'about:blank',
            'title' => $title,
            'status' => $status,
            'detail' => $detail,
        ], $status, ['Content-Type' => 'application/problem+json']));
    }
}
