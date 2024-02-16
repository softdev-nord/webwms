<?php

declare(strict_types=1);

namespace WebWMS\Helper\FormHelper;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use WebWMS\Entity\SupplierOrderEntity;
use WebWMS\Entity\SupplierOrderPosEntity;
use WebWMS\Form\SupplierOrder\AddSupplierOrderType;
use WebWMS\Form\SupplierOrder\DeleteSupplierOrderType;
use WebWMS\Form\SupplierOrder\EditSupplierOrderType;
use WebWMS\Form\SupplierOrder\SupplierOrderPosType;

/**
 * @package:    WebWMS\Helper\FormHelper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierOrderFormHelper
 */
class SupplierOrderFormHelper
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

    public function addSupplierOrderForm(): FormInterface
    {
        return $this->createForm(AddSupplierOrderType::class);
    }

    /**
     * @param SupplierOrderEntity|null $supplierOrderEntity
     */
    public function editSupplierOrderForm(?SupplierOrderEntity $supplierOrderEntity): FormInterface
    {
        return $this->createForm(EditSupplierOrderType::class, $supplierOrderEntity);
    }

    /**
     * @param SupplierOrderEntity|null $supplierOrderEntity
     */
    public function deleteSupplierOrderForm(?SupplierOrderEntity $supplierOrderEntity): FormInterface
    {
        return $this->createForm(DeleteSupplierOrderType::class, $supplierOrderEntity);
    }

    public function addSupplierOrderPosForm(): FormInterface
    {
        return $this->createForm(SupplierOrderPosType::class);
    }

    /**
     * @param SupplierOrderPosEntity|null $supplierOrderPosEntity
     */
    public function editSupplierOrderPosForm(?SupplierOrderPosEntity $supplierOrderPosEntity): FormInterface
    {
        return $this->createForm(SupplierOrderPosType::class, $supplierOrderPosEntity);
    }

    /**
     * @param SupplierOrderPosEntity|null $supplierOrderPosEntity
     */
    public function deleteSupplierOrderPosForm(?SupplierOrderPosEntity $supplierOrderPosEntity): FormInterface
    {
        return $this->createForm(SupplierOrderPosType::class, $supplierOrderPosEntity);
    }
}
