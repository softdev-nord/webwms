<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Documentation\Screenshot;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use WebWMS\Documentation\Infrastructure\Screenshot\DemoReferenceResolver;

final class DemoReferenceResolverTest extends TestCase
{
    public function testLiteralValuesAreNotChanged(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::never())->method('fetchOne');

        self::assertSame(['resource' => 'bin'], (new DemoReferenceResolver($connection))->resolve(['resource' => 'bin']));
    }

    public function testUnknownDemoReferenceIsRejected(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unknown handbook demo reference');

        (new DemoReferenceResolver($this->createMock(Connection::class)))->resolve(['id' => '@demo.unknown']);
    }
}
