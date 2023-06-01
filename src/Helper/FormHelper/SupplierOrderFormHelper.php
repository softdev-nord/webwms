<?php

declare(strict_types=1);

namespace WebWMS\Helper\FormHelper;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use WebWMS\Entity\SupplierOrder;
use WebWMS\Entity\SupplierOrderPos;
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
        private FormFactoryInterface $formFactory
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

    public function addSupplierOrderForm(): FormInterface
    {
        return $this->createForm(AddSupplierOrderType::class);
    }

    /**
     * @param SupplierOrder|null $supplierOrder
     * @return FormInterface
     */
    public function editSupplierOrderForm(?SupplierOrder $supplierOrder): FormInterface
    {
        return $this->createForm(EditSupplierOrderType::class, $supplierOrder);
    }

    /**
     * @param SupplierOrder|null $supplierOrder
     * @return FormInterface
     */
    public function deleteSupplierOrderForm(?SupplierOrder $supplierOrder): FormInterface
    {
        return $this->createForm(DeleteSupplierOrderType::class, $supplierOrder);
    }

    public function addSupplierOrderPosForm(): FormInterface
    {
        return $this->createForm(SupplierOrderPosType::class);
    }

    /**
     * @param SupplierOrderPos|null $supplierOrderPos
     * @return FormInterface
     */
    public function editSupplierOrderPosForm(?SupplierOrderPos $supplierOrderPos): FormInterface
    {
        return $this->createForm(SupplierOrderPosType::class, $supplierOrderPos);
    }

    /**
     * @param SupplierOrderPos|null $supplierOrderPos
     * @return FormInterface
     */
    public function deleteSupplierOrderPosForm(?SupplierOrderPos $supplierOrderPos): FormInterface
    {
        return $this->createForm(SupplierOrderPosType::class, $supplierOrderPos);
    }
}
