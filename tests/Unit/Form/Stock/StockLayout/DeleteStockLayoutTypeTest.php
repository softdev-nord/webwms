<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Form\Stock\StockLayout;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use WebWMS\Entity\StockLayoutEntity;
use WebWMS\Form\Stock\StockLayout\DeleteStockLayoutType;

/**
 * @package:    WebWMS\Tests\Unit\Form\Stock\StockLayoutEntity
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        DeleteStockLayoutTypeTest
 */
#[CoversClass(DeleteStockLayoutType::class)]
final class DeleteStockLayoutTypeTest extends TestCase
{
    public function testBuildForm(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder
            ->expects(self::once())
            ->method('add')
            ->withAnyParameters();

        $deleteStockLayoutType = new DeleteStockLayoutType();
        $deleteStockLayoutType->buildForm($builder, []);
    }

    public function testConfigureOptions(): void
    {
        $resolver = $this->createMock(OptionsResolver::class);
        $resolver
            ->expects(self::once())
            ->method('setDefaults')
            ->with(['data_class' => StockLayoutEntity::class]);

        $deleteStockLayoutType = new DeleteStockLayoutType();
        $deleteStockLayoutType->configureOptions($resolver);
    }
}
