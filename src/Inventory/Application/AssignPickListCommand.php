<?php
declare(strict_types=1);
namespace WebWMS\Inventory\Application;
use DateTimeImmutable;
final readonly class AssignPickListCommand
{
    public function __construct(public string $pickListId, public string $tenantId, public string $assignedTo, public string $assignedBy, public DateTimeImmutable $assignedAt) {}
}
