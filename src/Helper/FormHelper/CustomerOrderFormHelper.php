<?php

declare(strict_types=1);

namespace WebWMS\Helper\FormHelper;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use WebWMS\Entity\CustomerOrder;
use WebWMS\Entity\CustomerOrderPos;
use WebWMS\Form\CustomerOrder\AddCustomerOrderType;
use WebWMS\Form\CustomerOrder\CustomerOrderPosType;
use WebWMS\Form\CustomerOrder\DeleteCustomerOrderType;
use WebWMS\Form\CustomerOrder\EditCustomerOrderType;
use WebWMS\Helper\Attribute\ClassInformation;

#[ClassInformation(
    package: 'WebWMS\Helper\FormHelper',
    author: 'SoftDev Nord, Rene Irrgang',
    copyright: 'Copyright © 2019-2025, SoftDev Nord',
    class: 'CustomerOrderFormHelper'
)]
readonly class CustomerOrderFormHelper
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

    public function addCustomerOrderForm(): FormInterface
    {
        return $this->createForm(AddCustomerOrderType::class);
    }

    public function editCustomerOrderForm(?CustomerOrder $customerOrder): FormInterface
    {
        return $this->createForm(EditCustomerOrderType::class, $customerOrder);
    }

    public function deleteCustomerOrderForm(?CustomerOrder $customerOrder): FormInterface
    {
        return $this->createForm(DeleteCustomerOrderType::class, $customerOrder);
    }

    public function addCustomerOrderPosForm(): FormInterface
    {
        return $this->createForm(CustomerOrderPosType::class);
    }

    public function editCustomerOrderPosForm(?CustomerOrderPos $customerOrderPos): FormInterface
    {
        return $this->createForm(CustomerOrderPosType::class, $customerOrderPos);
    }

    public function deleteCustomerOrderPosForm(?CustomerOrderPos $customerOrderPos): FormInterface
    {
        return $this->createForm(CustomerOrderPosType::class, $customerOrderPos);
    }
}
