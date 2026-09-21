<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Inventory\Domain;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use WebWMS\Inventory\Domain\StockSelectionRuleDefinition;
use WebWMS\Inventory\Domain\StockSelectionStrategy;

final class StockSelectionRuleDefinitionTest extends TestCase
{
    /** @return iterable<string, array{StockSelectionStrategy, string}> */
    public static function strategyOrders(): iterable
    {
        yield 'fifo' => [StockSelectionStrategy::Fifo, 'first_received_at IS NULL ASC, first_received_at ASC, b.expires_at ASC'];
        yield 'lifo' => [StockSelectionStrategy::Lifo, 'first_received_at DESC, b.expires_at DESC'];
        yield 'fefo' => [StockSelectionStrategy::Fefo, 'b.expires_at IS NULL ASC, b.expires_at ASC, first_received_at ASC'];
    }

    #[DataProvider('strategyOrders')]
    public function testStrategyDefinesDeterministicStockOrder(StockSelectionStrategy $strategy, string $expected): void
    {
        self::assertSame($expected, $strategy->orderByClause());
    }

    public function testDefinitionNormalizesInput(): void
    {
        $definition = new StockSelectionRuleDefinition(' fifo ', ' Ältester Zugang ', StockSelectionStrategy::Fifo, 10, true);

        self::assertSame('FIFO', $definition->code);
        self::assertSame('Ältester Zugang', $definition->name);
    }

    public function testDefinitionRejectsNonPositivePriority(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new StockSelectionRuleDefinition('FIFO', 'FIFO', StockSelectionStrategy::Fifo, 0, true);
    }

    public function testUnknownStrategyIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        StockSelectionStrategy::fromInput('unknown');
    }
}
