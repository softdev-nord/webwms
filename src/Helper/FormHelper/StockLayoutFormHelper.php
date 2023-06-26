<?php

declare(strict_types=1);

namespace WebWMS\Helper\FormHelper;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use WebWMS\Entity\StockLayout;
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
     * @return FormInterface
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
     * @param StockLayout|null $stockLayout
     * @return FormInterface
     */
    public function editStockLayoutForm(?StockLayout $stockLayout): FormInterface
    {
        return $this->createForm(EditStockLayoutType::class, $stockLayout);
    }

    /**
     * @param StockLayout|null $stockLayout
     * @return FormInterface
     */
    public function deleteStockLayoutForm(?StockLayout $stockLayout): FormInterface
    {
        return $this->createForm(DeleteStockLayoutType::class, $stockLayout);
    }
}
