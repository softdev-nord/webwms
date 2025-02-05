<?php

declare(strict_types=1);

namespace WebWMS\Tests\Unit\Helper\FormHelper;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use WebWMS\Entity\StockZone;
use WebWMS\Form\Stock\StockZone\AddStockZoneType;
use WebWMS\Form\Stock\StockZone\DeleteStockZoneType;
use WebWMS\Form\Stock\StockZone\EditStockZoneType;
use WebWMS\Helper\Attribute\ClassInformation;
use WebWMS\Helper\FormHelper\StockZoneFormHelper;

#[ClassInformation(
    package: 'WebWMS\Tests\Unit\Helper\FormHelper',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2023, SoftDev Nord',
    class: 'StockZoneFormHelperTest'
)]
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
            ->expects($this->once())
            ->method('create')
            ->with($type, $data, $options)
            ->willReturn($formInterface);

        $stockZoneFormHelper = new StockZoneFormHelper($formFactory);
        $form = $stockZoneFormHelper->createForm($type, $data, $options);

        self::assertSame($formInterface, $form);
    }

    public function testAddStockZoneForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(AddStockZoneType::class)
            ->willReturn($formInterface);

        $stockZoneFormHelper = new StockZoneFormHelper($formFactory);
        $form = $stockZoneFormHelper->addStockZoneForm();

        self::assertSame($formInterface, $form);
    }

    public function testEditStockZoneForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $stockZone = $this->createMock(StockZone::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(EditStockZoneType::class, $stockZone)
            ->willReturn($formInterface);

        $stockZoneFormHelper = new StockZoneFormHelper($formFactory);
        $form = $stockZoneFormHelper->editStockZoneForm($stockZone);

        self::assertSame($formInterface, $form);
    }

    public function testDeleteStockZoneForm(): void
    {
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formInterface = $this->createMock(FormInterface::class);
        $stockZone = $this->createMock(StockZone::class);

        $formFactory
            ->expects($this->once())
            ->method('create')
            ->with(DeleteStockZoneType::class, $stockZone)
            ->willReturn($formInterface);

        $stockZoneFormHelper = new StockZoneFormHelper($formFactory);
        $form = $stockZoneFormHelper->deleteStockZoneForm($stockZone);

        self::assertSame($formInterface, $form);
    }
}
