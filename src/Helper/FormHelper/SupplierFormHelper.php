<?php

declare(strict_types=1);

namespace WebWMS\Helper\FormHelper;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use WebWMS\Form\Supplier\AddSupplierType;
use WebWMS\Form\Supplier\DeleteSupplierType;
use WebWMS\Form\Supplier\EditSupplierType;

/**
 * @package:    WebWMS\Helper\FormHelper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        SupplierFormHelper
 */
class SupplierFormHelper
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

    public function addSupplierForm(): FormInterface
    {
        return $this->createForm(AddSupplierType::class);
    }

    /**
     * @param object $supplier
     * @return FormInterface
     */
    public function editSupplierForm(object $supplier): FormInterface
    {
        return $this->createForm(EditSupplierType::class, $supplier);
    }

    /**
     * @param object $supplier
     * @return FormInterface
     */
    public function deleteSupplierForm(object $supplier): FormInterface
    {
        return $this->createForm(DeleteSupplierType::class, $supplier);
    }
}
