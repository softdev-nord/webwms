<?php
declare(strict_types=1);
namespace WebWMS\Inventory\Application;
use DateTimeImmutable;
final readonly class AddPackingPackageCommand
{
    /** @param list<string> $pickTaskIds */
    public function __construct(public string $packageId, public string $orderId, public string $tenantId, public string $packageNumber, public int $weightGrams, public array $pickTaskIds, public string $packedBy, public DateTimeImmutable $packedAt) {}
}
