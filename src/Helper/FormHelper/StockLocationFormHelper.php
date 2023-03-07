<?php

declare(strict_types=1);

namespace WebWMS\Helper\FormHelper;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use WebWMS\Entity\StockLocation;
use WebWMS\Form\Stock\StockLocation\AddStockLocationType;
use WebWMS\Form\Stock\StockLocation\DeleteStockLocationType;
use WebWMS\Form\Stock\StockLocation\EditStockLocationType;

/**
 * @package:    WebWMS\Helper\FormHelper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        StockLocationFormHelper
 */
class StockLocationFormHelper
{
    public function __construct(
        private FormFactoryInterface $formFactory
    ) {
    }

    /**
     * @param string $type
     * @param mixed|null $data
     * @param array<string> $options
     * @return FormInterface
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
     * @param StockLocation|null $stockLocation
     * @return FormInterface
     */
    public function editStockLocationForm(?StockLocation $stockLocation): FormInterface
    {
        return $this->createForm(EditStockLocationType::class, $stockLocation);
    }

    /**
     * @param StockLocation|null $stockLocation
     * @return FormInterface
     */
    public function deleteStockLocationForm(?StockLocation $stockLocation): FormInterface
    {
        return $this->createForm(DeleteStockLocationType::class, $stockLocation);
    }
}
