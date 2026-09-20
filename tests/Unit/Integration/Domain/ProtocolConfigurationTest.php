<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Integration\Domain;

use PHPUnit\Framework\TestCase;
use WebWMS\Integration\Domain\ProtocolConfiguration;

final class ProtocolConfigurationTest extends TestCase
{
    public function testItAcceptsRestOverHttpFraming(): void
    {
        $configuration = new ProtocolConfiguration('endpoint', 'rest_json', 'http', 3000, 10000);

        self::assertSame('http', $configuration->framing);
    }

    public function testItRejectsHttpFramingForRawTcp(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new ProtocolConfiguration('endpoint', 'raw_tcp', 'http', 3000, 10000);
    }
}
