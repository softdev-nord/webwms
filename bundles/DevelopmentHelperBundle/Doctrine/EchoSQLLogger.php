<?php

declare(strict_types=1);

namespace WebWMS\Bundles\DevelopmentHelperBundle\Doctrine;

use Doctrine\DBAL\Logging\SQLLogger;
use WebWMS\Twig\DoctrineExtension;

/**
 * Print executed SQL to the console, in such a way that they can be easily copied to other SQL tools for further
 * debugging. This is similar to the symfony debug bar, but useful in CLI commands and tests.
 */
class EchoSQLLogger implements SQLLogger
{
    public function startQuery($sql, ?array $params = null, ?array $types = null): void
    {
        $doctrineExtension = new DoctrineExtension();
        echo $doctrineExtension->replaceQueryParameters(
            $sql,
            $params ?? []
        )
            .';'
            .PHP_EOL;
    }

    public function stopQuery(): void
    {
    }
}
