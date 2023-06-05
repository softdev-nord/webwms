<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Helper\FormHelper;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use WebWMS\Entity\StockLocation;
use WebWMS\Form\Stock\StockLocation\AddStockLocationType;
use WebWMS\Form\Stock\StockLocation\DeleteStockLocationType;
use WebWMS\Form\Stock\StockLocation\EditStockLocationType;
use WebWMS\Helper\FormHelper\StockLocationFormHelper;

/**
 * @package:    WebWMS\Tests\Unit\Helper\FormHelper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLocationFormHelperTest
 *
 * @covers \WebWMS\Helper\FormHelper\StockLocationFormHelper
 */
final class StockLocationFormHelperTest extends TestCase
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

        $helper = new StockLocationFormHelper($formFactory);
        $result = $helper->createForm($type, $data, $options);

        self::assertSame($formInterface, $result);
    }

    public function testAddStockLocationForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(AddStockLocationType::class)
            ->willReturn($formInterface);

        $helper = new StockLocationFormHelper($formFactory);
        $result = $helper->addStockLocationForm();

        self::assertSame($formInterface, $result);
    }

    public function testEditStockLocationForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $stockLocation = $this->createMock(StockLocation::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(EditStockLocationType::class, $stockLocation)
            ->willReturn($formInterface);

        $helper = new StockLocationFormHelper($formFactory);
        $result = $helper->editStockLocationForm($stockLocation);

        self::assertSame($formInterface, $result);
    }

    public function testDeleteStockLocationForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $stockLocation = $this->createMock(StockLocation::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(DeleteStockLocationType::class, $stockLocation)
            ->willReturn($formInterface);

        $helper = new StockLocationFormHelper($formFactory);
        $result = $helper->deleteStockLocationForm($stockLocation);

        self::assertSame($formInterface, $result);
    }
}
