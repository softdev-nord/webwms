<?php

declare(strict_types=1);

namespace WebWMS\Helper\FormHelper;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use WebWMS\Entity\StockZone;
use WebWMS\Form\Stock\StockZone\AddStockZoneType;
use WebWMS\Form\Stock\StockZone\DeleteStockZoneType;
use WebWMS\Form\Stock\StockZone\EditStockZoneType;


/**
 * @package:    WebWMS\Helper\FormHelper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockZoneFormHelper
 */
class StockZoneFormHelper
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

    public function addStockZoneForm(): FormInterface
    {
        return $this->createForm(AddStockZoneType::class);
    }

    /**
     * @param StockZone|null $stockZone
     * @return FormInterface
     */
    public function editStockZoneForm(?StockZone $stockZone): FormInterface
    {
        return $this->createForm(EditStockZoneType::class, $stockZone);
    }

    /**
     * @param StockZone|null $stockZone
     * @return FormInterface
     */
    public function deleteStockZoneForm(?StockZone $stockZone): FormInterface
    {
        return $this->createForm(DeleteStockZoneType::class, $stockZone);
    }
}
