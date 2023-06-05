<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Helper\FormHelper;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use WebWMS\Entity\StockZone;
use WebWMS\Form\Stock\StockZone\AddStockZoneType;
use WebWMS\Form\Stock\StockZone\DeleteStockZoneType;
use WebWMS\Form\Stock\StockZone\EditStockZoneType;
use WebWMS\Helper\FormHelper\StockZoneFormHelper;

/**
 * @package:    WebWMS\Tests\Unit\Helper\FormHelper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockZoneFormHelperTest
 *
 * @covers \WebWMS\Helper\FormHelper\StockZoneFormHelper
 */
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

        $helper = new StockZoneFormHelper($formFactory);
        $result = $helper->createForm($type, $data, $options);

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

        $helper = new StockZoneFormHelper($formFactory);
        $result = $helper->addStockZoneForm();

        self::assertSame($formInterface, $result);
    }

    public function testEditStockZoneForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $stockZone = $this->createMock(StockZone::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(EditStockZoneType::class, $stockZone)
            ->willReturn($formInterface);

        $helper = new StockZoneFormHelper($formFactory);
        $result = $helper->editStockZoneForm($stockZone);

        self::assertSame($formInterface, $result);
    }

    public function testDeleteStockZoneForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $stockZone = $this->createMock(StockZone::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(DeleteStockZoneType::class, $stockZone)
            ->willReturn($formInterface);

        $helper = new StockZoneFormHelper($formFactory);
        $result = $helper->deleteStockZoneForm($stockZone);

        self::assertSame($formInterface, $result);
    }
}
