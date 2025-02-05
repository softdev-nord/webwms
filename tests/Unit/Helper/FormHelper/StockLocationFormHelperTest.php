<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Helper\FormHelper;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use WebWMS\Entity\StockLocation;
use WebWMS\Form\Stock\StockLocation\AddStockLocationType;
use WebWMS\Form\Stock\StockLocation\DeleteStockLocationType;
use WebWMS\Form\Stock\StockLocation\EditStockLocationType;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Helper\FormHelper\StockLocationFormHelper;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Helper\FormHelper',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'StockLocationFormHelperTest'
)]
#[CoversClass(StockLocationFormHelper::class)]
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
            ->expects($this->once())
            ->method('create')
            ->with($type, $data, $options)
            ->willReturn($formInterface);

        $stockLocationFormHelper = new StockLocationFormHelper($formFactory);
        $form = $stockLocationFormHelper->createForm($type, $data, $options);

        self::assertSame($formInterface, $form);
    }

    public function testAddStockLocationForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(AddStockLocationType::class)
            ->willReturn($formInterface);

        $stockLocationFormHelper = new StockLocationFormHelper($formFactory);
        $form = $stockLocationFormHelper->addStockLocationForm();

        self::assertSame($formInterface, $form);
    }

    public function testEditStockLocationForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $stockLocation = $this->createMock(StockLocation::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(EditStockLocationType::class, $stockLocation)
            ->willReturn($formInterface);

        $stockLocationFormHelper = new StockLocationFormHelper($formFactory);
        $form = $stockLocationFormHelper->editStockLocationForm($stockLocation);

        self::assertSame($formInterface, $form);
    }

    public function testDeleteStockLocationForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $stockLocation = $this->createMock(StockLocation::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(DeleteStockLocationType::class, $stockLocation)
            ->willReturn($formInterface);

        $stockLocationFormHelper = new StockLocationFormHelper($formFactory);
        $form = $stockLocationFormHelper->deleteStockLocationForm($stockLocation);

        self::assertSame($formInterface, $form);
    }
}
