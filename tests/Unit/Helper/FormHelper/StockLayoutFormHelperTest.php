<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Helper\FormHelper;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use WebWMS\Entity\StockLayout;
use WebWMS\Form\Stock\StockLayout\AddStockLayoutType;
use WebWMS\Form\Stock\StockLayout\DeleteStockLayoutType;
use WebWMS\Form\Stock\StockLayout\EditStockLayoutType;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Helper\FormHelper\StockLayoutFormHelper;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Helper\FormHelper',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'StockLayoutFormHelperTest'
)]
#[CoversClass(StockLayoutFormHelper::class)]
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
            ->expects($this->once())
            ->method('create')
            ->with($type, $data, $options)
            ->willReturn($formInterface);

        $stockLayoutFormHelper = new StockLayoutFormHelper($formFactory);
        $form = $stockLayoutFormHelper->createForm($type, $data, $options);

        self::assertSame($formInterface, $form);
    }

    public function testAddStockLayoutForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(AddStockLayoutType::class)
            ->willReturn($formInterface);

        $stockLayoutFormHelper = new StockLayoutFormHelper($formFactory);
        $form = $stockLayoutFormHelper->addStockLayoutForm();

        self::assertSame($formInterface, $form);
    }

    public function testEditStockLayoutForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $stockLayout = $this->createMock(StockLayout::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(EditStockLayoutType::class, $stockLayout)
            ->willReturn($formInterface);

        $stockLayoutFormHelper = new StockLayoutFormHelper($formFactory);
        $form = $stockLayoutFormHelper->editStockLayoutForm($stockLayout);

        self::assertSame($formInterface, $form);
    }

    public function testDeleteStockLayoutForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $stockLayout = $this->createMock(StockLayout::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(DeleteStockLayoutType::class, $stockLayout)
            ->willReturn($formInterface);

        $stockLayoutFormHelper = new StockLayoutFormHelper($formFactory);
        $form = $stockLayoutFormHelper->deleteStockLayoutForm($stockLayout);

        self::assertSame($formInterface, $form);
    }
}
