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

/**
 * @package:    WebWMS\Helper\FormHelper
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        CustomerOrderFormHelper
 */
class CustomerOrderFormHelper
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

    public function addCustomerOrderForm(): FormInterface
    {
        return $this->createForm(AddCustomerOrderType::class);
    }

    /**
     * @param CustomerOrder|null $customerOrder
     * @return FormInterface
     */
    public function editCustomerOrderForm(?CustomerOrder $customerOrder): FormInterface
    {
        return $this->createForm(EditCustomerOrderType::class, $customerOrder);
    }

    /**
     * @param CustomerOrder|null $customerOrder
     * @return FormInterface
     */
    public function deleteCustomerOrderForm(?CustomerOrder $customerOrder): FormInterface
    {
        return $this->createForm(DeleteCustomerOrderType::class, $customerOrder);
    }

    public function addCustomerOrderPosForm(): FormInterface
    {
        return $this->createForm(CustomerOrderPosType::class);
    }

    /**
     * @param CustomerOrderPos|null $customerOrderPos
     * @return FormInterface
     */
    public function editCustomerOrderPosForm(?CustomerOrderPos $customerOrderPos): FormInterface
    {
        return $this->createForm(CustomerOrderPosType::class, $customerOrderPos);
    }

    /**
     * @param CustomerOrderPos|null $customerOrderPos
     * @return FormInterface
     */
    public function deleteCustomerOrderPosForm(?CustomerOrderPos $customerOrderPos): FormInterface
    {
        return $this->createForm(CustomerOrderPosType::class, $customerOrderPos);
    }
}
