<?php

declare(strict_types=1);

namespace WebWMS\Helper\FormHelper;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use WebWMS\Entity\StockZone;
use WebWMS\Form\Stock\StockZone\AddStockZoneType;
use WebWMS\Form\Stock\StockZone\DeleteStockZoneType;
use WebWMS\Form\Stock\StockZone\EditStockZoneType;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Helper\FormHelper',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockZoneFormHelper'
)]
readonly class StockZoneFormHelper
{
    public function __construct(
        private FormFactoryInterface $formFactory,
    ) {
    }

    /**
     * @param class-string<FormTypeInterface<mixed>> $type
     * @param array<string> $options
     */
    public function createForm(string $type, mixed $data = null, array $options = []): FormInterface
    {
        return $this->formFactory->create($type, $data, $options);
    }

    public function addStockZoneForm(): FormInterface
    {
        return $this->createForm(AddStockZoneType::class);
    }

    public function editStockZoneForm(?StockZone $stockZone): FormInterface
    {
        return $this->createForm(EditStockZoneType::class, $stockZone);
    }

    public function deleteStockZoneForm(?StockZone $stockZone): FormInterface
    {
        return $this->createForm(DeleteStockZoneType::class, $stockZone);
    }
}
