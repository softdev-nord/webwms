<?php

declare(strict_types=1);

namespace WebWMS\Documentation\Infrastructure\Screenshot;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Stringable;

final class PlaywrightProcessLogger extends AbstractLogger
{
    /** @var list<string> */
    private array $diagnostics = [];

    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    /** @param array<string, mixed> $context */
    public function log($level, Stringable|string $message, array $context = []): void
    {
        $this->logger->log($level, $message, $context);
        $stderr = $context['stderr'] ?? null;
        if (is_string($stderr) && trim($stderr) !== '') {
            $this->diagnostics[] = trim($stderr);
        }
    }

    public function reset(): void
    {
        $this->diagnostics = [];
    }

    public function diagnostics(): string
    {
        return implode(PHP_EOL, array_unique($this->diagnostics));
    }
}
