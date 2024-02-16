<?php

declare(strict_types=1);

namespace WebWMS\Helper\FormHelper;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use WebWMS\Entity\StockLocationEntity;
use WebWMS\Form\Stock\StockLocation\AddStockLocationType;
use WebWMS\Form\Stock\StockLocation\DeleteStockLocationType;
use WebWMS\Form\Stock\StockLocation\EditStockLocationType;

/**
 * @package:    WebWMS\Helper\FormHelper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLocationFormHelper
 */
class StockLocationFormHelper
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    /**
     * @param class-string<FormTypeInterface<mixed>> $type
     * @param mixed|null $data
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

    /**
     * @param StockLocationEntity|null $stockLocationEntity
     */
    public function editStockLocationForm(?StockLocationEntity $stockLocationEntity): FormInterface
    {
        return $this->createForm(EditStockLocationType::class, $stockLocationEntity);
    }

    /**
     * @param StockLocationEntity|null $stockLocationEntity
     */
    public function deleteStockLocationForm(?StockLocationEntity $stockLocationEntity): FormInterface
    {
        return $this->createForm(DeleteStockLocationType::class, $stockLocationEntity);
    }
}
