<?php

declare(strict_types=1);

namespace WebWMS\Helper\FormHelper;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use WebWMS\Entity\StockZoneEntity;
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

    public function addStockZoneForm(): FormInterface
    {
        return $this->createForm(AddStockZoneType::class);
    }

    /**
     * @param StockZoneEntity|null $stockZoneEntity
     */
    public function editStockZoneForm(?StockZoneEntity $stockZoneEntity): FormInterface
    {
        return $this->createForm(EditStockZoneType::class, $stockZoneEntity);
    }

    /**
     * @param StockZoneEntity|null $stockZoneEntity
     */
    public function deleteStockZoneForm(?StockZoneEntity $stockZoneEntity): FormInterface
    {
        return $this->createForm(DeleteStockZoneType::class, $stockZoneEntity);
    }
}
