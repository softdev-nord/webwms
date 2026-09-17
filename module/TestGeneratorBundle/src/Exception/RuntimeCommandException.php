<?php

declare(strict_types=1);

namespace SoftDevNord\TestGenerator\Exception;

use RuntimeException;
use Symfony\Component\Console\Exception\ExceptionInterface;

/**
 * An exception whose output is displayed as a clean error.
 */
final class RuntimeCommandException extends RuntimeException implements ExceptionInterface
{
}
