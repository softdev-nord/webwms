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
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Helper\FormHelper',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'SupplierOrderFormHelper'
)]
readonly class SupplierOrderFormHelper
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

    public function addSupplierOrderForm(): FormInterface
    {
        return $this->createForm(AddSupplierOrderType::class);
    }

    public function editSupplierOrderForm(?SupplierOrder $supplierOrder): FormInterface
    {
        return $this->createForm(EditSupplierOrderType::class, $supplierOrder);
    }

    public function deleteSupplierOrderForm(?SupplierOrder $supplierOrder): FormInterface
    {
        return $this->createForm(DeleteSupplierOrderType::class, $supplierOrder);
    }

    public function addSupplierOrderPosForm(): FormInterface
    {
        return $this->createForm(SupplierOrderPosType::class);
    }

    public function editSupplierOrderPosForm(?SupplierOrderPos $supplierOrderPos): FormInterface
    {
        return $this->createForm(SupplierOrderPosType::class, $supplierOrderPos);
    }

    public function deleteSupplierOrderPosForm(?SupplierOrderPos $supplierOrderPos): FormInterface
    {
        return $this->createForm(SupplierOrderPosType::class, $supplierOrderPos);
    }
}
