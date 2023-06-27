<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Helper\FormHelper;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use WebWMS\Entity\StockLayout;
use WebWMS\Form\Stock\StockLayout\AddStockLayoutType;
use WebWMS\Form\Stock\StockLayout\DeleteStockLayoutType;
use WebWMS\Form\Stock\StockLayout\EditStockLayoutType;
use WebWMS\Helper\FormHelper\StockLayoutFormHelper;

/**
 * @package:    WebWMS\Tests\Unit\Helper\FormHelper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLayoutFormHelperTest
 *
 * @covers \WebWMS\Helper\FormHelper\StockLayoutFormHelper
 */
final class StockLayoutFormHelperTest extends TestCase
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

        $helper = new StockLayoutFormHelper($formFactory);
        $result = $helper->createForm($type, $data, $options);

        self::assertSame($formInterface, $result);
    }

    public function testAddStockLayoutForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(AddStockLayoutType::class)
            ->willReturn($formInterface);

        $helper = new StockLayoutFormHelper($formFactory);
        $result = $helper->addStockLayoutForm();

        self::assertSame($formInterface, $result);
    }

    public function testEditStockLayoutForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $stockLayout = $this->createMock(StockLayout::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(EditStockLayoutType::class, $stockLayout)
            ->willReturn($formInterface);

        $helper = new StockLayoutFormHelper($formFactory);
        $result = $helper->editStockLayoutForm($stockLayout);

        self::assertSame($formInterface, $result);
    }

    public function testDeleteStockLayoutForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $stockLayout = $this->createMock(StockLayout::class);

        $formFactory
            ->expects(self::once())
            ->method('create')
            ->with(DeleteStockLayoutType::class, $stockLayout)
            ->willReturn($formInterface);

        $helper = new StockLayoutFormHelper($formFactory);
        $result = $helper->deleteStockLayoutForm($stockLayout);

        self::assertSame($formInterface, $result);
    }
}
