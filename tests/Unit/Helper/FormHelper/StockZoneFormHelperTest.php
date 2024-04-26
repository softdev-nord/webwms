<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Helper\FormHelper;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use WebWMS\Entity\StockZoneEntity;
use WebWMS\Form\Stock\StockZone\AddStockZoneType;
use WebWMS\Form\Stock\StockZone\DeleteStockZoneType;
use WebWMS\Form\Stock\StockZone\EditStockZoneType;
use WebWMS\Helper\FormHelper\StockZoneFormHelper;

/**
 * @package:    WebWMS\Tests\Unit\Helper\FormHelper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2024, SoftDev Nord
 * Class        StockZoneFormHelperTest
 */
#[CoversClass(StockZoneFormHelper::class)]
final class StockZoneFormHelperTest extends TestCase
{
    public function testCreateForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $type = 'SomeType';
        $data = null;
        $options = [];

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with($type, $data, $options)
            ->willReturn($formInterface);

        $stockZoneFormHelper = new StockZoneFormHelper($formFactory);
        $result = $stockZoneFormHelper->createForm($type, $data, $options);

        self::assertSame($formInterface, $result);
    }

    public function testAddStockZoneForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(AddStockZoneType::class)
            ->willReturn($formInterface);

        $stockZoneFormHelper = new StockZoneFormHelper($formFactory);
        $result = $stockZoneFormHelper->addStockZoneForm();

        self::assertSame($formInterface, $result);
    }

    public function testEditStockZoneForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $stockZone = $this->createMock(StockZoneEntity::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(EditStockZoneType::class, $stockZone)
            ->willReturn($formInterface);

        $stockZoneFormHelper = new StockZoneFormHelper($formFactory);
        $result = $stockZoneFormHelper->editStockZoneForm($stockZone);

        self::assertSame($formInterface, $result);
    }

    public function testDeleteStockZoneForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $stockZone = $this->createMock(StockZoneEntity::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(DeleteStockZoneType::class, $stockZone)
            ->willReturn($formInterface);

        $stockZoneFormHelper = new StockZoneFormHelper($formFactory);
        $result = $stockZoneFormHelper->deleteStockZoneForm($stockZone);

        self::assertSame($formInterface, $result);
    }
}
