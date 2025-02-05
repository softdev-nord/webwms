<?php

declare(strict_types=1);

namespace WebWMS\Helper\FormHelper;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use WebWMS\Entity\StockLocation;
use WebWMS\Form\Stock\StockLocation\AddStockLocationType;
use WebWMS\Form\Stock\StockLocation\DeleteStockLocationType;
use WebWMS\Form\Stock\StockLocation\EditStockLocationType;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Helper\FormHelper',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'StockLocationFormHelper'
)]
readonly class StockLocationFormHelper
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

    public function addStockLocationForm(): FormInterface
    {
        return $this->createForm(AddStockLocationType::class);
    }

    public function editStockLocationForm(?StockLocation $stockLocation): FormInterface
    {
        return $this->createForm(EditStockLocationType::class, $stockLocation);
    }

    public function deleteStockLocationForm(?StockLocation $stockLocation): FormInterface
    {
        return $this->createForm(DeleteStockLocationType::class, $stockLocation);
    }
}
