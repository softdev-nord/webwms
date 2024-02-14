<?php

declare(strict_types=1);

namespace WebWMS\Helper\FormHelper;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use WebWMS\Entity\StockLayoutEntity;
use WebWMS\Form\Stock\StockLayout\AddStockLayoutType;
use WebWMS\Form\Stock\StockLayout\DeleteStockLayoutType;
use WebWMS\Form\Stock\StockLayout\EditStockLayoutType;

/**
 * @package:    WebWMS\Helper\FormHelper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockLayoutFormHelper
 */
class StockLayoutFormHelper
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

    public function addStockLayoutForm(): FormInterface
    {
        return $this->createForm(AddStockLayoutType::class);
    }

    /**
     * @param StockLayoutEntity|null $stockLayoutEntity
     */
    public function editStockLayoutForm(?StockLayoutEntity $stockLayoutEntity): FormInterface
    {
        return $this->createForm(EditStockLayoutType::class, $stockLayoutEntity);
    }

    /**
     * @param StockLayoutEntity|null $stockLayoutEntity
     */
    public function deleteStockLayoutForm(?StockLayoutEntity $stockLayoutEntity): FormInterface
    {
        return $this->createForm(DeleteStockLayoutType::class, $stockLayoutEntity);
    }
}
