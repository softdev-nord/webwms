<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Application;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Application\ApiV3QueryService;

final class ApiV3QueryServiceTest extends TestCase
{
    public function testStockQuotesTheReservedCursorAliasForMariaDb(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::once())
            ->method('fetchAllAssociative')
            ->with(
                self::callback(static function (string $sql): bool {
                    self::assertStringContainsString('AS `cursor`', $sql);

                    return true;
                }),
                self::isType('array'),
            )
            ->willReturn([]);

        $queries = new ApiV3QueryService($connection);

        self::assertSame([], $queries->stock('tenant-id', null, 200, null));
    }
}
