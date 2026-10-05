<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Domain;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use WebWMS\Inventory\Domain\ContactAddress;

class ContactAddressTest extends TestCase
{
    public function testItNormalizesAnAddress(): void
    {
        $address = new ContactAddress(' Customer GmbH ', ' Main Street 1 ', ' 20095 ', ' Hamburg ', ' de ');

        self::assertSame('Customer GmbH', $address->name());
        self::assertSame('Main Street 1', $address->street());
        self::assertSame('20095', $address->postalCode());
        self::assertSame('Hamburg', $address->city());
        self::assertSame('DE', $address->countryCode());
    }

    /** @param list<string> $values */
    #[DataProvider('invalidAddresses')]
    public function testItRejectsInvalidAddresses(array $values): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ContactAddress(...$values);
    }

    /** @return iterable<string, array{list<string>}> */
    public static function invalidAddresses(): iterable
    {
        yield 'missing name' => [['', 'Main Street 1', '20095', 'Hamburg', 'DE']];
        yield 'missing street' => [['Customer GmbH', '', '20095', 'Hamburg', 'DE']];
        yield 'missing postal code' => [['Customer GmbH', 'Main Street 1', '', 'Hamburg', 'DE']];
        yield 'missing city' => [['Customer GmbH', 'Main Street 1', '20095', '', 'DE']];
        yield 'invalid country code' => [['Customer GmbH', 'Main Street 1', '20095', 'Hamburg', 'DEU']];
    }
}
